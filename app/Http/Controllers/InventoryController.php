<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\Vendor;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $medicines = Medicine::with('category')->orderBy('name')->paginate(10);
        $categories = Category::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->take(5)->get();

        $totalActiveItems = Medicine::count();
        $criticalLowStock = Medicine::whereColumn('stock', '<=', 'min_stock')->count();
        $expiringSoon = Medicine::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [today(), today()->addDays(90)])
            ->count();

        $expiryBuckets = [
            'critical' => Medicine::whereBetween('expiry_date', [today(), today()->addDays(30)])->count(),
            'warning' => Medicine::whereBetween('expiry_date', [today()->addDays(31), today()->addDays(60)])->count(),
            'notice' => Medicine::whereBetween('expiry_date', [today()->addDays(61), today()->addDays(90)])->count(),
        ];

        return view('inventory.index', compact('medicines', 'categories', 'vendors', 'totalActiveItems', 'criticalLowStock', 'expiringSoon', 'expiryBuckets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:medicines,code'],
            'name' => ['required', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:40'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $medicine = new Medicine($data);
        $medicine->refreshStatus();
        $medicine->save();

        return redirect()->route('inventory.index')->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function updateStock(Request $request, Medicine $medicine)
    {
        $data = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $medicine->stock = $data['stock'];
        $medicine->refreshStatus();
        $medicine->save();

        return back()->with('success', 'Stok obat berhasil diperbarui.');
    }
}
