<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Billing;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (class_exists(User::class)) {
            User::firstOrCreate(
                ['email' => 'admin@pharmaseek.test'],
                ['name' => 'Pharmaseek Admin', 'password' => Hash::make('password')]
            );
        }

        $antibiotic = Category::firstOrCreate(['name' => 'Antibiotic']);
        $analgesic = Category::firstOrCreate(['name' => 'Analgesic']);
        $ppi = Category::firstOrCreate(['name' => 'PPI']);
        $cardio = Category::firstOrCreate(['name' => 'Cardiovascular']);
        $anesthetic = Category::firstOrCreate(['name' => 'Anesthetic']);

        $medicines = [
            ['OBT-AMX-001', 'Amoxicillin 500mg', $antibiotic->id, 'tab', 1250, 100, 2500, now()->addDays(45)],
            ['OBT-PAR-092', 'Paracetamol Infusion 1g', $analgesic->id, 'vial', 15, 20, 1000, now()->addDays(2)],
            ['OBT-OMP-014', 'Omeprazole 20mg', $ppi->id, 'cap', 840, 40, 3000, now()->addDays(14)],
            ['OBT-CEF-003', 'Ceftriaxone 1g Vial', $antibiotic->id, 'vial', 320, 25, 15000, now()->addDays(28)],
            ['OBT-LID-046', 'Lidocaine 2%', $anesthetic->id, 'ampoule', 110, 20, 7000, now()->addDays(120)],
            ['OBT-WAR-011', 'Warfarin 5mg', $cardio->id, 'tab', 250, 30, 2200, now()->addDays(180)],
            ['OBT-ASP-081', 'Aspirin 81mg', $cardio->id, 'tab', 500, 50, 1200, now()->addDays(210)],
            ['OBT-AML-010', 'Amlodipine 10mg', $cardio->id, 'tab', 300, 30, 1800, now()->addDays(200)],
            ['OBT-INS-100', 'Insulin Glargine 100IU/ml', $cardio->id, 'vial', 12, 20, 85000, now()->addDays(76)],
        ];

        foreach ($medicines as [$code, $name, $categoryId, $unit, $stock, $minStock, $price, $expiry]) {
            $medicine = Medicine::updateOrCreate(['code' => $code], [
                'name' => $name,
                'category_id' => $categoryId,
                'unit' => $unit,
                'stock' => $stock,
                'min_stock' => $minStock,
                'unit_price' => $price,
                'expiry_date' => $expiry,
            ]);
            $medicine->refreshStatus();
            $medicine->save();
        }

        $vendors = [
            ['PBF-001', 'PT Kimia Farma Trading', '98%'],
            ['PBF-002', 'PT Enseval Putera', '95%'],
            ['PBF-004', 'PT Mensa Binasukses', '91%'],
            ['PBF-005', 'PT Anugerah Pharmindo', '93%'],
            ['PBF-012', 'PT Dexa Medica', '96%'],
        ];
        foreach ($vendors as [$code, $name, $sla]) {
            Vendor::firstOrCreate(['vendor_code' => $code], ['name' => $name, 'sla' => $sla]);
        }

        $doctor = Doctor::firstOrCreate(['name' => 'Dr. A. Rahman'], ['specialist' => 'Cardiology', 'sip_number' => 'SIP-001']);
        Doctor::firstOrCreate(['name' => 'Dr. A. Smith'], ['specialist' => 'Emergency', 'sip_number' => 'SIP-002']);

        $budi = Patient::firstOrCreate(['mrn' => '882910'], [
            'name' => 'Budi Wijaya',
            'gender' => 'Male',
            'age' => 62,
            'weight_kg' => 78,
            'height_cm' => 170,
            'diagnosis' => 'Atrial Fibrillation, Hypertension',
            'allergies' => 'Penicillin',
        ]);
        Patient::firstOrCreate(['mrn' => '882911'], ['name' => 'Siti Aminah', 'gender' => 'Female', 'age' => 49, 'diagnosis' => 'Fever']);
        Patient::firstOrCreate(['mrn' => '882912'], ['name' => 'Ahmad Yani', 'gender' => 'Male', 'age' => 55, 'diagnosis' => 'Hypertension']);

        $warfarin = Medicine::where('code', 'OBT-WAR-011')->first();
        $aspirin = Medicine::where('code', 'OBT-ASP-081')->first();
        $amlodipine = Medicine::where('code', 'OBT-AML-010')->first();
        $amox = Medicine::where('code', 'OBT-AMX-001')->first();
        $para = Medicine::where('code', 'OBT-PAR-092')->first();
        $saline = Medicine::where('name', 'like', '%Saline%')->first();

        $criticalRx = Prescription::firstOrCreate(['rx_number' => 'RX-2024-08912A'], [
            'patient_id' => $budi->id,
            'doctor_id' => $doctor->id,
            'priority' => 'Critical',
            'status' => 'Skrining',
            'payment_method' => 'Insurance (BPJS)',
            'prescribed_at' => now()->subMinutes(20),
        ]);
        $criticalRx->items()->firstOrCreate(['medicine_id' => $warfarin->id], ['dosage' => '5mg PO QD', 'qty' => 30, 'status' => 'Verified']);
        $criticalRx->items()->firstOrCreate(['medicine_id' => $aspirin->id], ['dosage' => '81mg PO QD', 'qty' => 30, 'status' => 'Critical']);
        $criticalRx->items()->firstOrCreate(['medicine_id' => $amlodipine->id], ['dosage' => '10mg PO QD', 'qty' => 30, 'status' => 'Verified']);

        $rx2 = Prescription::firstOrCreate(['rx_number' => 'RX-20231024-002'], [
            'patient_id' => Patient::where('mrn', '882911')->first()->id,
            'doctor_id' => $doctor->id,
            'priority' => 'Normal',
            'status' => 'Penyiapan',
            'payment_method' => 'Insurance (BPJS)',
            'prescribed_at' => now()->subHours(2),
        ]);
        $rx2->items()->firstOrCreate(['medicine_id' => $amox->id], ['dosage' => '500mg x 20 tabs', 'qty' => 20, 'status' => 'Verified']);
        $rx2->items()->firstOrCreate(['medicine_id' => $para->id], ['dosage' => '500mg x 10 tabs', 'qty' => 10, 'status' => 'Verified']);

        $vendor = Vendor::where('vendor_code', 'PBF-001')->first();
        $po = PurchaseOrder::firstOrCreate(['po_number' => 'PO-2024-0891'], [
            'vendor_id' => $vendor->id,
            'date_issued' => now()->subDays(1),
            'expected_delivery' => today(),
            'total_value' => 45200000,
            'status' => 'Verified',
        ]);
        $po->items()->firstOrCreate(['medicine_id' => $para->id], ['qty_ordered' => 100, 'qty_received' => 0, 'unit_cost' => 1000]);

        PurchaseOrder::firstOrCreate(['po_number' => 'PO-2024-0890'], [
            'vendor_id' => Vendor::where('vendor_code', 'PBF-002')->first()->id,
            'date_issued' => now()->subDays(2),
            'expected_delivery' => now()->addDays(1),
            'total_value' => 12550000,
            'status' => 'Pending',
        ]);

        $billing = Billing::firstOrCreate(['bill_number' => 'BL-2023-094A'], [
            'prescription_id' => $rx2->id,
            'patient_id' => $rx2->patient_id,
            'dispensing_id' => 'DSP-ERD-992',
            'payment_method' => 'Insurance (BPJS)',
            'subtotal' => 60000,
            'tax' => 6600,
            'total' => 66600,
            'claim_status' => 'Insurance Verified',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $billing->items()->firstOrCreate(['medicine_id' => $amox->id], ['dosage_qty' => '500mg x 20 tabs', 'unit_price' => 2500, 'total' => 50000]);
        $billing->items()->firstOrCreate(['medicine_id' => $para->id], ['dosage_qty' => '500mg x 10 tabs', 'unit_price' => 1000, 'total' => 10000]);

        AuditLog::firstOrCreate(['title' => 'Stock Deducted'], ['description' => 'System auto-deduction completed for dispensing items.', 'actor' => 'System']);
        AuditLog::firstOrCreate(['title' => 'Dispensing Confirmed'], ['description' => 'Pharmacist verified prescription vs dispensed items.', 'actor' => 'R. Sarah, Pharm.D']);
        AuditLog::firstOrCreate(['title' => 'ERD Request Received'], ['description' => 'Prescription routed from ER Doctor.', 'actor' => 'Dr. A. Smith']);
    }
}
