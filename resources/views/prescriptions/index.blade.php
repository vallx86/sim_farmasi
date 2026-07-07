@extends('layouts.app', ['title' => 'Prescriptions - Pharmaseek SIMRS'])
@section('content')
<div class="page-head">
    <div><h2>Prescription Queue</h2><p>Kelola resep dari ERD/poli, lakukan skrining, lalu lanjutkan ke penyiapan.</p></div>
</div>

<div class="grid grid-2">
    <div class="card">
        <div class="head-row"><h3>Daftar Resep</h3></div>
        <table class="table">
            <thead><tr><th>No. RX</th><th>Pasien</th><th>Dokter</th><th>Prioritas</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($prescriptions as $rx)
                <tr>
                    <td><strong>{{ $rx->rx_number }}</strong></td>
                    <td>{{ $rx->patient->name }}</td>
                    <td>{{ optional($rx->doctor)->name ?? '-' }}</td>
                    <td><span class="badge {{ $rx->priority === 'Critical' ? 'red' : ($rx->priority === 'Urgent' ? 'amber' : 'blue') }}">{{ $rx->priority }}</span></td>
                    <td><span class="badge blue">{{ $rx->status }}</span></td>
                    <td><a class="btn" href="{{ route('prescriptions.show', $rx) }}">Skrining</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="padding:16px">{{ $prescriptions->links() }}</div>
    </div>

    <div class="card pad">
        <h3>New Prescription</h3>
        <form class="form" method="post" action="{{ route('prescriptions.store') }}">
            @csrf
            <div class="form-row">
                <div><label>Pasien</label><select name="patient_id" required>@foreach($patients as $patient)<option value="{{ $patient->id }}">{{ $patient->name }} - {{ $patient->mrn }}</option>@endforeach</select></div>
                <div><label>Dokter</label><select name="doctor_id"><option value="">-</option>@foreach($doctors as $doctor)<option value="{{ $doctor->id }}">{{ $doctor->name }}</option>@endforeach</select></div>
            </div>
            <div class="form-row">
                <div><label>Prioritas</label><select name="priority"><option>Normal</option><option>Urgent</option><option>Critical</option></select></div>
                <div><label>Pembayaran</label><select name="payment_method"><option>Insurance (BPJS)</option><option>Cash</option><option>Hospital Bill</option></select></div>
            </div>
            <div class="form-row">
                <div><label>Obat 1</label><select name="medicine_id[]" required>@foreach($medicines as $medicine)<option value="{{ $medicine->id }}">{{ $medicine->name }}</option>@endforeach</select></div>
                <div><label>Dosis</label><input class="input" name="dosage[]" value="500mg x 10 tabs"></div>
            </div>
            <div><label>Qty</label><input class="input" name="qty[]" type="number" value="10" min="1"></div>
            <button class="btn primary">Simpan Resep</button>
        </form>
    </div>
</div>
@endsection
