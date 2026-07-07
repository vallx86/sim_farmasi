<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index()
    {
        $prescriptions = Prescription::with(['patient', 'doctor'])->latest()->paginate(10);
        $patients = Patient::orderBy('name')->get();
        $doctors = Doctor::orderBy('name')->get();
        $medicines = Medicine::orderBy('name')->get();

        return view('prescriptions.index', compact('prescriptions', 'patients', 'doctors', 'medicines'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
            'priority' => ['required', 'in:Normal,Urgent,Critical'],
            'payment_method' => ['required', 'string', 'max:50'],
            'medicine_id' => ['required', 'array', 'min:1'],
            'medicine_id.*' => ['exists:medicines,id'],
            'dosage' => ['required', 'array', 'min:1'],
            'dosage.*' => ['nullable', 'string', 'max:100'],
            'qty' => ['required', 'array', 'min:1'],
            'qty.*' => ['integer', 'min:1'],
        ]);

        DB::transaction(function () use ($data) {
            $prescription = Prescription::create([
                'rx_number' => 'RX-' . now()->format('Ymd-His'),
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'] ?? null,
                'priority' => $data['priority'],
                'status' => 'Skrining',
                'payment_method' => $data['payment_method'],
                'prescribed_at' => now(),
            ]);

            foreach ($data['medicine_id'] as $index => $medicineId) {
                $prescription->items()->create([
                    'medicine_id' => $medicineId,
                    'dosage' => $data['dosage'][$index] ?? null,
                    'qty' => $data['qty'][$index] ?? 1,
                    'status' => 'Pending',
                ]);
            }

            AuditLog::create([
                'title' => 'Prescription Request Received',
                'description' => 'Resep baru masuk dan menunggu proses skrining.',
                'actor' => 'Apoteker/TTK',
            ]);
        });

        return redirect()->route('prescriptions.index')->with('success', 'Resep baru berhasil dibuat dan masuk ke tahap skrining.');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'items.medicine.category']);

        $names = $prescription->items->pluck('medicine.name')->map(fn ($name) => strtolower($name))->values();
        $hasWarfarin = $names->contains(fn ($name) => str_contains($name, 'warfarin'));
        $hasAspirin = $names->contains(fn ($name) => str_contains($name, 'aspirin'));
        $criticalAlert = $hasWarfarin && $hasAspirin;

        return view('prescriptions.show', compact('prescription', 'criticalAlert'));
    }

    public function updateStatus(Request $request, Prescription $prescription)
    {
        $data = $request->validate([
            'status' => ['required', 'in:Skrining,Konsultasi Dokter,Penyiapan,Dispensing Selesai,Ditolak'],
        ]);

        $prescription->update(['status' => $data['status']]);

        AuditLog::create([
            'title' => 'Prescription Status Updated',
            'description' => 'Status resep ' . $prescription->rx_number . ' diubah menjadi ' . $data['status'] . '.',
            'actor' => 'Apoteker/TTK',
        ]);

        return back()->with('success', 'Status resep berhasil diperbarui.');
    }
}
