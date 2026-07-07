@extends('layouts.app', ['title' => 'Procurement - Pharmaseek SIMRS'])
@section('content')
<div class="page-head">
    <div><h2>Procurement & Purchase Orders</h2><p>Manage vendor relations, issue POs, and track incoming shipments.</p></div>
    <div class="actions"><button class="btn">Filter</button><a class="btn primary" href="#new-po">+ New Purchase Order</a></div>
</div>

<div class="grid grid-4">
    <div class="card pad"><div class="metric-title">Active POs</div><div class="metric-value">{{ $activePOs }}</div><span class="green">↗12%</span></div>
    <div class="card pad"><div class="metric-title">Pending Verification</div><div class="metric-value">{{ $pendingVerification }}</div><span class="muted">Requires SPV Signoff</span></div>
    <div class="card pad"><div class="metric-title">Expected Deliveries</div><div class="metric-value">{{ $expectedDeliveries }}</div><span class="muted">Today</span></div>
    <div class="card pad"><div class="metric-title">Vendor Performance</div><div class="metric-value">{{ $vendorPerformance }}</div><span class="muted">On-Time Rate</span></div>
</div>

<div style="height:26px"></div>
<div class="grid grid-2">
    <div class="stack">
        <div class="card">
            <div class="head-row"><h3>Active Purchase Orders</h3><a class="green">View All</a></div>
            <table class="table">
                <thead><tr><th>PO Number</th><th>Vendor</th><th>Date Issued</th><th>Total Value</th><th>Status</th><th>Receive</th></tr></thead>
                <tbody>
                @foreach($purchaseOrders as $po)
                    <tr>
                        <td><strong style="color:#00549e">{{ $po->po_number }}</strong></td>
                        <td>{{ $po->vendor->name }}</td>
                        <td>{{ $po->date_issued->format('d M Y') }}</td>
                        <td class="money">Rp {{ number_format($po->total_value, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $po->status === 'Pending' ? 'amber' : ($po->status === 'Realized' ? 'green' : 'blue') }}">{{ $po->status }}</span></td>
                        <td>
                            <form method="post" action="{{ route('procurement.receive', $po) }}" style="display:grid;grid-template-columns:70px 100px 128px auto;gap:6px">@csrf
                                <input class="input" type="number" name="qty_received" value="{{ optional($po->items->first())->qty_ordered ?? 1 }}" min="1">
                                <input class="input" name="batch_no" placeholder="BCH-XXX">
                                <input class="input" type="date" name="expiry_date">
                                <button class="btn">Receive</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px">{{ $purchaseOrders->links() }}</div>
        </div>

        <div class="card pad" id="new-po">
            <h3>Generate Purchase Order</h3>
            <form class="form" method="post" action="{{ route('procurement.store') }}">@csrf
                <div class="form-row"><div><label>Vendor/PBF</label><select name="vendor_id">@foreach($vendors as $vendor)<option value="{{ $vendor->id }}">{{ $vendor->name }}</option>@endforeach</select></div><div><label>Medicine</label><select name="medicine_id">@foreach($medicines as $medicine)<option value="{{ $medicine->id }}">{{ $medicine->name }} (stock: {{ $medicine->stock }})</option>@endforeach</select></div></div>
                <div class="form-row"><div><label>Qty Ordered</label><input class="input" type="number" name="qty_ordered" value="100" min="1"></div><div><label>Unit Cost</label><input class="input" type="number" name="unit_cost" value="1000" min="0"></div></div>
                <div><label>Expected Delivery</label><input class="input" type="date" name="expected_delivery"></div>
                <button class="btn primary">Create PO</button>
            </form>
        </div>
    </div>

    <div class="stack">
        <div class="card"><div class="head-row"><h3>Approved Vendors (PBF)</h3><strong>+</strong></div><div class="right-card stack">@foreach($vendors as $vendor)<div><strong>{{ $vendor->name }}</strong><br><span class="muted">ID: {{ $vendor->vendor_code }} • SLA: {{ $vendor->sla }}</span></div>@endforeach</div></div>
        @if($lowStock)
            <div class="note"><h3 style="margin-top:0">⚠ Low Stock Warning</h3><p>{{ $lowStock->name }} is below minimum threshold. Current: {{ $lowStock->stock }}, Min: {{ $lowStock->min_stock }}.</p><a href="#new-po"><strong>Auto-Generate PO</strong></a></div>
        @endif
    </div>
</div>
@endsection
