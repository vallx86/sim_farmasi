@extends('layouts.app', ['title' => 'Reports - Pharmaseek SIMRS'])
@section('content')
<div class="page-head">
    <div><h2>Reports & Analytics</h2><p>Comprehensive overview of pharmacy operations and financial performance.</p></div>
    <div class="actions">
        <form method="get" action="{{ route('reports.index') }}" style="display:flex;gap:10px;flex-wrap:wrap">
            <input class="input" type="date" name="date_from" value="{{ $dateFrom }}" style="width:160px">
            <input class="input" type="date" name="date_to" value="{{ $dateTo }}" style="width:160px">
            <button class="btn" type="submit">Filter</button>
        </form>
        <a class="btn" href="{{ route('reports.print', request()->query()) }}" target="_blank">Export / Print</a>
    </div>
</div>

<div class="grid grid-4">
    <div class="card pad"><div class="metric-title">Total Revenue</div><div class="metric-value money">Rp {{ number_format($totalRevenue,0,',','.') }}</div><span class="green">Periode {{ $dateFrom }} s/d {{ $dateTo }}</span></div>
    <div class="card pad"><div class="metric-title">Total Expenditure</div><div class="metric-value money">Rp {{ number_format($totalExpenditure,0,',','.') }}</div><span class="amber">Berdasarkan Purchase Order</span></div>
    <div class="card pad"><div class="metric-title">Net Income</div><div class="metric-value money {{ $netIncome < 0 ? 'red' : 'green' }}">Rp {{ number_format($netIncome,0,',','.') }}</div><span class="muted">Revenue - Expenditure</span></div>
    <div class="card pad"><div class="metric-title">Transactions</div><div class="metric-value">{{ $totalTransactions }}</div><span class="muted">Billing tercatat</span></div>
</div>

<div style="height:16px"></div>
<div class="grid grid-4">
    <div class="card pad"><div class="metric-title">Avg Dispensing Time</div><div class="metric-value">{{ $avgDispensingTime }}</div><span class="green">Target 10m</span></div>
    <div class="card pad"><div class="metric-title">Stock Turnover Rate</div><div class="metric-value">{{ $stockTurnoverRate }}</div><span class="red">Below optimal 5x</span></div>
    <div class="card pad"><div class="metric-title">Clinical Interventions</div><div class="metric-value">{{ $clinicalInterventions }}</div><span class="red">Butuh perhatian farmasi klinis</span></div>
    <div class="card pad"><div class="metric-title">Critical Stock Alerts</div><div class="metric-value red">{{ $criticalStockAlerts }}</div><span class="muted">Obat <= minimum stok</span></div>
</div>

<div style="height:26px"></div>
<div class="grid grid-2">
    <div class="card pad">
        <h3>Revenue vs. Expenditure</h3>
        @php
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            $max = max(1, $monthlyRevenue->max() ?? 1, $monthlyExpenditure->max() ?? 1);
        @endphp
        <div class="bars">
            @foreach($months as $index => $monthLabel)
                @php
                    $rev = $monthlyRevenue[$index + 1] ?? 0;
                    $exp = $monthlyExpenditure[$index + 1] ?? 0;
                @endphp
                <div class="bar-pair">
                    <div class="bar" title="Revenue Rp {{ number_format($rev, 0, ',', '.') }}" style="height:{{ max(8, ($rev / $max) * 100) }}%"></div>
                    <div class="bar exp" title="Expenditure Rp {{ number_format($exp, 0, ',', '.') }}" style="height:{{ max(8, ($exp / $max) * 100) }}%"></div>
                </div>
            @endforeach
        </div>
        <div style="display:flex;justify-content:space-around;color:#667085;margin-top:8px">
            @foreach($months as $monthLabel)
                <span>{{ $monthLabel }}</span>
            @endforeach
        </div>
        <p class="muted" style="margin-top:16px">Biru menunjukkan pendapatan dari billing, sedangkan cokelat menunjukkan pengeluaran dari Purchase Order.</p>
    </div>

    <div class="card pad">
        <h3>Procurement by Vendor (PBF)</h3><p class="muted">YTD Spending Distribution</p>
        <div style="height:36px"></div>
        <div class="card pad" style="text-align:center;width:170px;margin:auto"><span class="muted">Total Spend</span><h3>Rp {{ number_format($vendorSpend->sum('total') / 1000000000, 2) }}B</h3></div>
        <div class="stack" style="margin-top:40px">
            @foreach($vendorSpend as $vendor)
            <div style="display:flex;justify-content:space-between;gap:12px"><span>{{ $vendor->name }}</span><strong>Rp {{ number_format($vendor->total / 1000000,0) }}M</strong></div>
            @endforeach
        </div>
    </div>
</div>

<div style="height:26px"></div>
<div class="grid grid-2">
    <div class="card">
        <div class="head-row"><h3>Top Moving Medicines</h3><span class="green">Auto from dispensing</span></div>
        <table class="table"><thead><tr><th>Medication</th><th>Category</th><th>Qty Dispensed</th><th>Trend</th></tr></thead><tbody>@forelse($topMoving as $row)<tr><td><strong>{{ $row->medicine->name }}</strong></td><td>{{ optional($row->medicine->category)->name }}</td><td>{{ $row->qty }}</td><td><span class="green">↗</span></td></tr>@empty<tr><td colspan="4" class="muted">Belum ada data dispensing.</td></tr>@endforelse</tbody></table>
    </div>
    <div class="card pad" style="background:#e5f0ff"><h3>Stock Expiry Forecast <span class="badge red">ACTION REQ</span></h3><div class="grid grid-2"><div><span class="metric-title">Projected Loss (90 Days)</span><div class="metric-value red">Rp {{ number_format($projectedLoss / 1000000, 1) }}M</div></div><div><span class="metric-title">Items at Risk</span><div class="metric-value">{{ $expiryRisk }} SKUs</div></div></div>
        <div class="stack" style="margin-top:18px">
            @foreach($analysisNotes as $note)
                <div>• {{ $note }}</div>
            @endforeach
        </div>
    </div>
</div>

<div style="height:26px"></div>
<div class="card">
    <div class="head-row"><h3>Critical Low Stock Items</h3><a class="green" href="{{ route('inventory.index') }}">Review Inventory</a></div>
    <table class="table">
        <thead><tr><th>Kode</th><th>Nama Obat</th><th>Kategori</th><th>Stok</th><th>Minimum</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($lowStockItems as $medicine)
            <tr><td>{{ $medicine->code }}</td><td><strong>{{ $medicine->name }}</strong></td><td>{{ optional($medicine->category)->name }}</td><td class="red">{{ $medicine->stock }}</td><td>{{ $medicine->min_stock }}</td><td><span class="badge red">{{ $medicine->status }}</span></td></tr>
        @empty
            <tr><td colspan="6" class="muted">Tidak ada stok kritis.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
