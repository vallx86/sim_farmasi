@extends('layouts.app', ['title' => 'Billing - Pharmaseek SIMRS'])
@section('content')
<div class="page-head">
    <div><h2>Billing & Transaction History</h2><p>Review and manage patient dispensing bills and audit logs.</p></div>
    <div class="actions"><button class="btn">Print Receipt</button><button class="btn primary">Export PDF</button></div>
</div>

<div class="grid grid-2">
    <div class="stack">
        @if($billing)
        <div class="card pad">
            <h3>Transaction Summary</h3><hr style="border:0;border-top:1px solid #c9d2e3">
            <div class="grid grid-4" style="grid-template-columns:repeat(4,1fr)">
                <div><span class="metric-title">Bill ID</span><p><strong>{{ $billing->bill_number }}</strong></p></div>
                <div><span class="metric-title">Patient Name</span><p><strong>{{ $billing->patient->name }}</strong></p></div>
                <div><span class="metric-title">Dispensing ID</span><p><strong>{{ $billing->dispensing_id }}</strong></p></div>
                <div><span class="metric-title">Date</span><p>{{ $billing->created_at->format('M d, Y H:i') }}</p></div>
            </div>
            <span class="badge blue">{{ $billing->payment_method }}</span>
        </div>
        <div class="card">
            <div class="head-row"><h3>Itemized Receipt</h3><span class="badge blue">{{ $billing->items->count() }} Items</span></div>
            <table class="table">
                <thead><tr><th>Drug Name</th><th>Dosage/Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
                <tbody>@foreach($billing->items as $item)<tr><td><strong>{{ $item->medicine->name }}</strong></td><td>{{ $item->dosage_qty }}</td><td>Rp {{ number_format($item->unit_price,0,',','.') }}</td><td><strong>Rp {{ number_format($item->total,0,',','.') }}</strong></td></tr>@endforeach</tbody>
            </table>
            <div style="background:#eef5ff;padding:24px 32px;text-align:right"><p>Subtotal &nbsp; <strong>Rp {{ number_format($billing->subtotal,0,',','.') }}</strong></p><p>Tax (11%) &nbsp; <strong>Rp {{ number_format($billing->tax,0,',','.') }}</strong></p><hr style="border:0;border-top:1px solid #c9d2e3"><h2>Total &nbsp; <span style="color:#00549e">Rp {{ number_format($billing->total,0,',','.') }}</span></h2></div>
        </div>
        @else
        <div class="card pad"><h3>Belum ada billing</h3><p class="muted">Ubah status resep menjadi Penyiapan atau Dispensing Selesai, lalu generate billing.</p></div>
        @endif

        <div class="card pad">
            <h3>Generate Billing dari Resep</h3>
            <div class="stack">@forelse($readyPrescriptions as $rx)<form method="post" action="{{ route('billing.generate', $rx) }}" style="display:flex;justify-content:space-between;align-items:center;gap:12px;border:1px solid #c9d2e3;border-radius:6px;padding:12px">@csrf<span><strong>{{ $rx->rx_number }}</strong><br>{{ $rx->patient->name }} • {{ $rx->payment_method }}</span><button class="btn primary">Generate</button></form>@empty<p class="muted">Tidak ada resep siap billing.</p>@endforelse</div>
        </div>
    </div>

    <div class="stack">
        <div class="card pad" style="background:#e7f0ff;border-color:#075eaa"><h3>ⓘ Insurance Verified</h3><p>{{ $billing?->claim_status ?? 'BPJS claim belum tersedia.' }}. No patient copay required.</p></div>
        <div class="card pad"><h3>Audit Trail</h3><div class="audit">@foreach($audits as $audit)<div class="audit-item"><strong>{{ $audit->title }}</strong><br><span class="muted">{{ $audit->description }}<br>{{ $audit->created_at->format('H:i:s') }} - {{ $audit->actor }}</span></div>@endforeach</div></div>
    </div>
</div>
@endsection
