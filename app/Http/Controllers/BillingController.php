<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Billing;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        $billing = Billing::with(['patient', 'prescription', 'items.medicine'])->latest()->first();
        $audits = AuditLog::latest()->take(5)->get();
        $readyPrescriptions = Prescription::with('patient')
            ->whereDoesntHave('billing')
            ->whereIn('status', ['Penyiapan', 'Dispensing Selesai'])
            ->latest()
            ->take(8)
            ->get();

        return view('billing.index', compact('billing', 'audits', 'readyPrescriptions'));
    }

    public function generate(Prescription $prescription)
    {
        $prescription->load(['patient', 'items.medicine']);

        if ($prescription->billing) {
            return redirect()->route('billing.index')->with('success', 'Billing untuk resep ini sudah ada.');
        }

        DB::transaction(function () use ($prescription) {
            $subtotal = 0;
            foreach ($prescription->items as $item) {
                $subtotal += $item->qty * $item->medicine->unit_price;
            }

            $tax = $subtotal * 0.11;
            $billing = Billing::create([
                'bill_number' => 'BL-' . now()->format('Ymd-His'),
                'prescription_id' => $prescription->id,
                'patient_id' => $prescription->patient_id,
                'dispensing_id' => 'DSP-' . now()->format('His'),
                'payment_method' => $prescription->payment_method,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
                'claim_status' => $prescription->payment_method === 'Insurance (BPJS)' ? 'Insurance Verified' : 'Cash Payment',
            ]);

            foreach ($prescription->items as $item) {
                $lineTotal = $item->qty * $item->medicine->unit_price;
                $billing->items()->create([
                    'medicine_id' => $item->medicine_id,
                    'dosage_qty' => $item->dosage . ' x ' . $item->qty,
                    'unit_price' => $item->medicine->unit_price,
                    'total' => $lineTotal,
                ]);

                $medicine = $item->medicine;
                $medicine->stock = max(0, $medicine->stock - $item->qty);
                $medicine->refreshStatus();
                $medicine->save();
            }

            $prescription->update(['status' => 'Dispensing Selesai']);

            AuditLog::create([
                'title' => 'Stock Deducted',
                'description' => 'Stok obat otomatis berkurang setelah billing dibuat.',
                'actor' => 'System',
            ]);
        });

        return redirect()->route('billing.index')->with('success', 'Billing berhasil dibuat dan stok sudah dikurangi.');
    }
}
