<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->buildReportData($request);
        return view('reports.index', $data);
    }

    public function print(Request $request)
    {
        $data = $this->buildReportData($request);
        return view('reports.print', $data);
    }

    private function buildReportData(Request $request): array
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $billingRange = Billing::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        $poRange = PurchaseOrder::whereDate('date_issued', '>=', $dateFrom)
            ->whereDate('date_issued', '<=', $dateTo);

        $totalRevenue = (clone $billingRange)->sum('total');
        $totalExpenditure = (clone $poRange)->sum('total_value');
        $netIncome = $totalRevenue - $totalExpenditure;
        $totalTransactions = (clone $billingRange)->count();
        $pendingPrescriptions = Prescription::whereIn('status', ['Skrining', 'Penerimaan', 'Penyiapan', 'Konsultasi Dokter'])->count();
        $criticalStockAlerts = Medicine::whereColumn('stock', '<=', 'min_stock')->count();
        $avgDispensingTime = '8m 42s';
        $stockTurnoverRate = '4.2x';
        $clinicalInterventions = Prescription::where('status', 'Konsultasi Dokter')->count() + PrescriptionItem::where('status', 'Critical')->count();

        $monthlyRevenue = Billing::selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyExpenditure = PurchaseOrder::selectRaw('MONTH(date_issued) as month, SUM(total_value) as total')
            ->whereYear('date_issued', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $vendorSpend = Vendor::leftJoin('purchase_orders', 'vendors.id', '=', 'purchase_orders.vendor_id')
            ->select('vendors.name', DB::raw('COALESCE(SUM(purchase_orders.total_value), 0) as total'))
            ->groupBy('vendors.id', 'vendors.name')
            ->orderByDesc('total')
            ->take(4)
            ->get();

        $topMoving = PrescriptionItem::with('medicine.category')
            ->select('medicine_id', DB::raw('SUM(qty) as qty'))
            ->groupBy('medicine_id')
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        $expiryRisk = Medicine::whereBetween('expiry_date', [today(), today()->addDays(90)])->count();
        $projectedLoss = Medicine::whereBetween('expiry_date', [today(), today()->addDays(90)])
            ->selectRaw('SUM(stock * unit_price) as loss')
            ->value('loss') ?? 0;

        $lowStockItems = Medicine::with('category')
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->take(8)
            ->get();

        $analysisNotes = [
            'Pendapatan periode berjalan dihitung dari billing yang sudah terbentuk.',
            'Pengeluaran dihitung dari nilai Purchase Order pada periode yang dipilih.',
            'Projected loss berasal dari stok obat yang akan kedaluwarsa dalam 90 hari.',
            'Critical stock diambil dari obat dengan stok akhir kurang dari atau sama dengan minimum stok.',
        ];

        return compact(
            'dateFrom',
            'dateTo',
            'totalRevenue',
            'totalExpenditure',
            'netIncome',
            'totalTransactions',
            'pendingPrescriptions',
            'criticalStockAlerts',
            'avgDispensingTime',
            'stockTurnoverRate',
            'clinicalInterventions',
            'monthlyRevenue',
            'monthlyExpenditure',
            'vendorSpend',
            'topMoving',
            'expiryRisk',
            'projectedLoss',
            'lowStockItems',
            'analysisNotes'
        );
    }
}
