<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Medicine;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['vendor', 'items.medicine'])->latest()->paginate(10);
        $vendors = Vendor::orderBy('name')->get();
        $medicines = Medicine::orderBy('name')->get();

        $activePOs = PurchaseOrder::whereIn('status', ['Pending', 'Verified'])->count();
        $pendingVerification = PurchaseOrder::where('status', 'Pending')->count();
        $expectedDeliveries = PurchaseOrder::whereDate('expected_delivery', today())->count();
        $vendorPerformance = '94%';
        $lowStock = Medicine::whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->first();

        return view('procurement.index', compact(
            'purchaseOrders',
            'vendors',
            'medicines',
            'activePOs',
            'pendingVerification',
            'expectedDeliveries',
            'vendorPerformance',
            'lowStock'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'medicine_id' => ['required', 'exists:medicines,id'],
            'qty_ordered' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'expected_delivery' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($data) {
            $total = $data['qty_ordered'] * $data['unit_cost'];

            $po = PurchaseOrder::create([
                'po_number' => 'PO-' . now()->format('Ymd-His'),
                'vendor_id' => $data['vendor_id'],
                'date_issued' => today(),
                'expected_delivery' => $data['expected_delivery'] ?? today()->addDays(2),
                'total_value' => $total,
                'status' => 'Pending',
            ]);

            $po->items()->create([
                'medicine_id' => $data['medicine_id'],
                'qty_ordered' => $data['qty_ordered'],
                'qty_received' => 0,
                'unit_cost' => $data['unit_cost'],
            ]);

            AuditLog::create([
                'title' => 'Purchase Order Created',
                'description' => 'PO baru dibuat dan menunggu verifikasi SPV.',
                'actor' => 'Procurement',
            ]);
        });

        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil dibuat.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $data = $request->validate([
            'qty_received' => ['required', 'integer', 'min:1'],
            'batch_no' => ['nullable', 'string', 'max:60'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($purchaseOrder, $data) {
            $item = $purchaseOrder->items()->firstOrFail();
            $item->update([
                'qty_received' => $item->qty_received + $data['qty_received'],
                'batch_no' => $data['batch_no'] ?? $item->batch_no,
                'expiry_date' => $data['expiry_date'] ?? $item->expiry_date,
            ]);

            $medicine = $item->medicine;
            $medicine->stock += $data['qty_received'];
            if (!empty($data['expiry_date'])) {
                $medicine->expiry_date = $data['expiry_date'];
            }
            $medicine->refreshStatus();
            $medicine->save();

            $purchaseOrder->update(['status' => 'Realized']);

            AuditLog::create([
                'title' => 'Incoming Goods Received',
                'description' => 'Barang masuk diterima dan stok otomatis bertambah.',
                'actor' => 'Apoteker/TTK',
            ]);
        });

        return back()->with('success', 'Penerimaan barang berhasil, stok otomatis diperbarui.');
    }
}
