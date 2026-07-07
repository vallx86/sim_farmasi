<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Laporan Analisis - Pharmaseek SIMRS</title>
    <style>
        body{font-family:Arial,sans-serif;color:#0f1f36;margin:28px}h1{margin:0 0 6px;font-size:28px}.muted{color:#667085}.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:22px 0}.card{border:1px solid #c9d2e3;border-radius:8px;padding:14px}.label{text-transform:uppercase;letter-spacing:.06em;color:#667085;font-size:12px;font-weight:bold}.value{font-size:22px;font-weight:bold;margin-top:12px}.red{color:#c1121f}.green{color:#00796b}.amber{color:#92400e}table{width:100%;border-collapse:collapse;margin-top:14px}th,td{border:1px solid #c9d2e3;padding:9px;text-align:left;font-size:13px}th{background:#f2f6ff}.section{margin-top:26px}.print-btn{padding:10px 14px;border:1px solid #075eaa;background:#075eaa;color:white;border-radius:6px;font-weight:bold;cursor:pointer}@media print{.print-btn{display:none}body{margin:12px}.grid{grid-template-columns:repeat(2,1fr)}}
    </style>
</head>
<body>
<button class="print-btn" onclick="window.print()">Print / Save PDF</button>
<h1>Laporan Analisis Pharmaseek SIMRS</h1>
<div class="muted">Periode: {{ $dateFrom }} sampai {{ $dateTo }}</div>

<div class="grid">
    <div class="card"><div class="label">Total Revenue</div><div class="value green">Rp {{ number_format($totalRevenue,0,',','.') }}</div></div>
    <div class="card"><div class="label">Total Expenditure</div><div class="value amber">Rp {{ number_format($totalExpenditure,0,',','.') }}</div></div>
    <div class="card"><div class="label">Net Income</div><div class="value {{ $netIncome < 0 ? 'red' : 'green' }}">Rp {{ number_format($netIncome,0,',','.') }}</div></div>
    <div class="card"><div class="label">Transactions</div><div class="value">{{ $totalTransactions }}</div></div>
</div>

<div class="section">
    <h2>Ringkasan Operasional</h2>
    <table>
        <tr><th>Indikator</th><th>Nilai</th><th>Keterangan</th></tr>
        <tr><td>Pending Prescription</td><td>{{ $pendingPrescriptions }}</td><td>Resep yang masih diproses</td></tr>
        <tr><td>Critical Stock Alerts</td><td>{{ $criticalStockAlerts }}</td><td>Stok kurang dari / sama dengan minimum</td></tr>
        <tr><td>Avg Dispensing Time</td><td>{{ $avgDispensingTime }}</td><td>Target 10 menit</td></tr>
        <tr><td>Clinical Interventions</td><td>{{ $clinicalInterventions }}</td><td>Konsultasi dokter atau item critical</td></tr>
        <tr><td>Projected Expiry Loss 90 Days</td><td>Rp {{ number_format($projectedLoss,0,',','.') }}</td><td>Nilai stok berisiko kedaluwarsa</td></tr>
    </table>
</div>

<div class="section">
    <h2>Top Moving Medicines</h2>
    <table>
        <thead><tr><th>Obat</th><th>Kategori</th><th>Qty Dispensed</th></tr></thead>
        <tbody>
        @forelse($topMoving as $row)
            <tr><td>{{ $row->medicine->name }}</td><td>{{ optional($row->medicine->category)->name }}</td><td>{{ $row->qty }}</td></tr>
        @empty
            <tr><td colspan="3">Belum ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="section">
    <h2>Critical Low Stock Items</h2>
    <table>
        <thead><tr><th>Kode</th><th>Nama Obat</th><th>Kategori</th><th>Stok</th><th>Minimum</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($lowStockItems as $medicine)
            <tr><td>{{ $medicine->code }}</td><td>{{ $medicine->name }}</td><td>{{ optional($medicine->category)->name }}</td><td>{{ $medicine->stock }}</td><td>{{ $medicine->min_stock }}</td><td>{{ $medicine->status }}</td></tr>
        @empty
            <tr><td colspan="6">Tidak ada stok kritis.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="section">
    <h2>Catatan Analisis</h2>
    <ul>
        @foreach($analysisNotes as $note)
            <li>{{ $note }}</li>
        @endforeach
    </ul>
</div>
</body>
</html>
