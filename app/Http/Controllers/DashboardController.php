<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\PrescriptionItem;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingPrescriptions = Prescription::whereIn('status', ['Skrining', 'Penerimaan', 'Penyiapan'])->count();
        $criticalStockAlerts = Medicine::whereColumn('stock', '<=', 'min_stock')->count();
        $dailyDispensed = PrescriptionItem::whereDate('created_at', today())->sum('qty');
        $shiftRevenue = Billing::whereDate('created_at', today())->sum('total');

        $recentPrescriptions = Prescription::with(['patient', 'doctor'])
            ->latest()
            ->take(5)
            ->get();

        $inventoryOverview = Medicine::with('category')
            ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 9999 ELSE DATEDIFF(expiry_date, CURRENT_DATE) END ASC')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'pendingPrescriptions',
            'criticalStockAlerts',
            'dailyDispensed',
            'shiftRevenue',
            'recentPrescriptions',
            'inventoryOverview'
        ));
    }
}
