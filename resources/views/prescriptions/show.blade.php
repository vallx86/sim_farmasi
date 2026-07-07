@extends('layouts.app', ['title' => 'Prescription Screening - Pharmaseek SIMRS'])
@section('content')
<div class="page-head">
    <div>
        <p class="metric-title">{{ $prescription->rx_number }} <span class="badge red">{{ $prescription->status }}</span></p>
        <h2>Prescription Screening</h2>
    </div>
    <p>{{ optional($prescription->doctor)->name ?? 'Dokter' }} • {{ optional($prescription->prescribed_at)->format('H:i d M y') }}</p>
</div>

@if($criticalAlert)
<div class="alert-red">
    <h3 style="margin:0 0 6px;color:white">🚫 CRITICAL: Drug-Drug Interaction Detected</h3>
    <p style="margin:0">Kombinasi Warfarin dan Aspirin meningkatkan risiko perdarahan. Konsultasikan ke DPJP sebelum resep dilanjutkan.</p>
</div>
@endif

<div class="screening">
    <div class="stack">
        <div class="card pad">
            <h3>{{ $prescription->patient->name }}</h3>
            <p class="muted">{{ $prescription->patient->gender }}, {{ $prescription->patient->age }} yrs • MRN: {{ $prescription->patient->mrn }} • {{ $prescription->patient->weight_kg }} kg / {{ $prescription->patient->height_cm }} cm</p>
            <hr style="border:0;border-top:1px solid #c9d2e3">
            <div class="form-row">
                <div><span class="metric-title">Diagnosis</span><p>{{ $prescription->patient->diagnosis }}</p></div>
                <div><span class="metric-title">Allergies</span><p><span class="badge blue">{{ $prescription->patient->allergies ?? '-' }}</span></p></div>
            </div>
            <p><span class="metric-title">Relevant Labs</span><br><span class="red">INR: 3.2 (High)</span> &nbsp; Creatinine: 1.1 mg/dL &nbsp; ALT: 34 U/L</p>
        </div>

        <div class="card">
            <div class="head-row"><h3>Prescription Items</h3></div>
            <table class="table">
                <thead><tr><th>#</th><th>Medication</th><th>Dosage / Sig</th><th>Qty</th><th>Status</th></tr></thead>
                <tbody>
                @foreach($prescription->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->medicine->name }}</strong><br><span class="muted">{{ $item->medicine->unit }}</span></td>
                        <td>{{ $item->dosage }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{!! $criticalAlert && str_contains(strtolower($item->medicine->name), 'aspirin') ? '<span class="red">⚠</span>' : '<span class="green">✔</span>' !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="head-row"><h3>☑ Skrining Farmasi</h3></div>
        <div class="right-card checkbox-list">
            <p class="metric-title">1. Kajian Administratif</p>
            <div><input type="checkbox" checked> Identitas Pasien Lengkap & Jelas</div>
            <div><input type="checkbox" checked> Identitas Dokter Jelas (SIP, TTD)</div>
            <p class="metric-title" style="margin-top:16px">2. Kajian Farmasetik</p>
            <div><input type="checkbox" checked> Bentuk dan Kekuatan Sediaan Sesuai</div>
            <div><input type="checkbox" checked> Stabilitas dan Kompatibilitas Aman</div>
            <p class="metric-title red" style="margin-top:16px">3. Kajian Klinis</p>
            <div><input type="checkbox" checked> Ketepatan Indikasi & Dosis</div>
            <div><input type="checkbox" checked> Bebas Alergi</div>
            @if($criticalAlert)
            <div class="note" style="background:#fff1f1;border-color:#ffb4ad;color:#b91c1c;margin-top:14px">Sistem mendeteksi interaksi mayor. Status disarankan: Konsultasi Dokter.</div>
            @endif
            <form class="form" method="post" action="{{ route('prescriptions.status', $prescription) }}" style="margin-top:20px">
                @csrf @method('PATCH')
                <label>Ubah Status</label>
                <select name="status">
                    <option>Skrining</option><option>Konsultasi Dokter</option><option>Penyiapan</option><option>Dispensing Selesai</option><option>Ditolak</option>
                </select>
                <button class="btn {{ $criticalAlert ? 'red' : 'primary' }}">Simpan Status</button>
            </form>
            @if(!$criticalAlert)
            <form method="post" action="{{ route('billing.generate', $prescription) }}" style="margin-top:10px">@csrf<button class="btn primary" style="width:100%">Lanjutkan ke Billing</button></form>
            @endif
        </div>
    </div>
</div>
@endsection
