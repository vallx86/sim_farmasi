@extends('layouts.app', ['title' => 'Inventory - Pharmaseek SIMRS'])
@section('content')
<div class="page-head">
    <div><h2>Inventory Management</h2><p>Monitor stock levels, expiry dates, and procurement readiness.</p></div>
    <div class="actions"><button class="btn">↧ Export</button><a class="btn primary" href="#add-stock">+ Add Stock</a></div>
</div>

<div class="grid grid-4">
    <div class="card pad"><div class="metric-title">Total Active Items</div><div class="metric-value">{{ number_format($totalActiveItems) }}</div></div>
    <div class="card pad"><div class="metric-title">Critical Low Stock</div><div class="metric-value red">{{ $criticalLowStock }}</div><span class="badge red">Action Req.</span></div>
    <div class="card pad"><div class="metric-title">Expiring &lt; 90 Days</div><div class="metric-value amber">{{ $expiringSoon }}</div><span class="badge amber">Review</span></div>
    <div class="card pad"><h3>Expiry Timeline</h3><div class="stack"><div>&lt; 30 Days <strong style="float:right">{{ $expiryBuckets['critical'] }} Items</strong><div class="mini-bar red"><span style="width:{{ min(100, $expiryBuckets['critical'] * 5) }}%"></span></div></div><div>30 - 60 Days <strong style="float:right">{{ $expiryBuckets['warning'] }} Items</strong><div class="mini-bar"><span style="width:{{ min(100, $expiryBuckets['warning'] * 4) }}%"></span></div></div><div>60 - 90 Days <strong style="float:right">{{ $expiryBuckets['notice'] }} Items</strong><div class="mini-bar green"><span style="width:{{ min(100, $expiryBuckets['notice'] * 3) }}%"></span></div></div></div></div>
</div>

<div style="height:26px"></div>
<div class="grid grid-2">
    <div class="card">
        <div class="head-row"><h3>Current Stock List</h3><select style="width:180px"><option>All Categories</option></select></div>
        <table class="table">
            <thead><tr><th>Kode Obat / Name</th><th>Category</th><th>Stock Akhir</th><th>Days Left</th><th>Expiry Date</th><th>Status</th><th>Update</th></tr></thead>
            <tbody>
            @foreach($medicines as $medicine)
                <tr>
                    <td><strong style="color:#00549e">{{ $medicine->name }}</strong><br><span class="muted">{{ $medicine->code }}</span></td>
                    <td>{{ optional($medicine->category)->name }}</td>
                    <td class="{{ $medicine->stock <= $medicine->min_stock ? 'red' : '' }}"><strong>{{ number_format($medicine->stock) }}</strong></td>
                    <td class="{{ $medicine->days_left !== null && $medicine->days_left < 30 ? 'red' : 'green' }}">{{ $medicine->days_left ?? '-' }}d</td>
                    <td>{{ optional($medicine->expiry_date)->format('Y-m-d') }}</td>
                    <td><span class="badge {{ $medicine->status === 'Critical' ? 'red' : ($medicine->status === 'Optimal' ? 'green' : 'amber') }}">{{ $medicine->status }}</span></td>
                    <td><form method="post" action="{{ route('inventory.stock', $medicine) }}" style="display:flex;gap:6px">@csrf @method('PATCH')<input class="input" type="number" name="stock" value="{{ $medicine->stock }}" min="0" style="width:86px"><button class="btn">OK</button></form></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="padding:16px">{{ $medicines->links() }}</div>
    </div>

    <div class="stack">
        <div class="card pad" id="add-stock">
            <h3>Add Medicine</h3>
            <form class="form" method="post" action="{{ route('inventory.store') }}">@csrf
                <div class="form-row"><div><label>Kode</label><input class="input" name="code" required></div><div><label>Nama Obat</label><input class="input" name="name" required></div></div>
                <div class="form-row"><div><label>Kategori</label><select name="category_id"><option value="">-</option>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></div><div><label>Unit</label><input class="input" name="unit" value="tablet"></div></div>
                <div class="form-row"><div><label>Stock</label><input class="input" type="number" name="stock" value="0"></div><div><label>Min Stock</label><input class="input" type="number" name="min_stock" value="20"></div></div>
                <div class="form-row"><div><label>Harga</label><input class="input" type="number" name="unit_price" value="1000"></div><div><label>Expired</label><input class="input" type="date" name="expiry_date"></div></div>
                <button class="btn primary">Simpan Obat</button>
            </form>
        </div>
        <div class="card"><div class="head-row"><h3>PBF & Pending PO</h3></div><div class="right-card stack">@foreach($vendors as $vendor)<div class="card pad"><strong>{{ $vendor->name }}</strong><br><span class="muted">ID: {{ $vendor->vendor_code }} • SLA: {{ $vendor->sla }}</span></div>@endforeach</div></div>
    </div>
</div>
@endsection
