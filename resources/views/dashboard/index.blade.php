@extends('layouts.app', ['title' => 'Dashboard - Pharmaseek SIMRS'])
@section('content')
<div class="page-head">
    <div>
        <h2>Dashboard Overview</h2>
        <p>Real-time pharmacy metrics for current shift.</p>
    </div>
    <div class="actions"><a class="btn" href="{{ route('procurement.index') }}">Restock Request</a><a class="btn primary" href="{{ route('prescriptions.index') }}">New Prescription</a></div>
</div>

<div class="grid grid-4">
    <div class="card pad"><div class="metric-title">Pending Prescriptions</div><div class="metric-value">{{ $pendingPrescriptions }}</div><div class="red">↑ 12% from last hour</div></div>
    <div class="card pad"><div class="metric-title">Critical Stock Alerts</div><div class="metric-value red">{{ $criticalStockAlerts }}</div><div class="muted">Requires immediate action</div></div>
    <div class="card pad"><div class="metric-title">Daily Dispensed</div><div class="metric-value">{{ $dailyDispensed }}</div><div class="green">↓ Normal volume</div></div>
    <div class="card pad"><div class="metric-title">Shift Revenue</div><div class="metric-value money">IDR {{ number_format($shiftRevenue / 1000000, 0) }}M</div><div class="green">◎ On target</div></div>
</div>

<div style="height:26px"></div>
<div class="grid grid-2">
    <div class="card">
        <div class="head-row"><h3>Recent Prescription Queue</h3><a class="green" href="{{ route('prescriptions.index') }}">View All</a></div>
        <table class="table">
            <thead><tr><th>Patient Name</th><th>Prescription ID</th><th>Priority</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            @foreach($recentPrescriptions as $rx)
                <tr>
                    <td><strong>{{ $rx->patient->name }}</strong></td>
                    <td>{{ $rx->rx_number }}</td>
                    <td><span class="badge {{ $rx->priority === 'Critical' ? 'red' : ($rx->priority === 'Urgent' ? 'amber' : 'blue') }}">{{ $rx->priority }}</span></td>
                    <td><span class="badge blue">{{ $rx->status }}</span></td>
                    <td><a class="green" href="{{ route('prescriptions.show', $rx) }}">👁</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card pad">
        <h3>Inventory Overview</h3>
        <p class="muted">Days remaining for fast-moving drugs.</p>
        <div class="stack" style="margin-top:180px">
            @foreach($inventoryOverview as $med)
                <div style="display:grid;grid-template-columns:100px 1fr 42px;align-items:center;gap:10px">
                    <span style="font-family:monospace">{{ \Illuminate\Support\Str::limit($med->name, 11) }}</span>
                    <div class="mini-bar {{ $med->days_left !== null && $med->days_left < 7 ? 'red' : '' }}"><span style="width:{{ max(8, min(100, ($med->days_left ?? 30) * 3)) }}%"></span></div>
                    <strong class="{{ $med->days_left !== null && $med->days_left < 7 ? 'red' : '' }}">{{ $med->days_left ?? '-' }}d</strong>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
