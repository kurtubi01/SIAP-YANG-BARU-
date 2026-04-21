@extends('layouts.sidebarmenu')

@section('content')
@php($prefix = strtolower(Auth::user()->role) === 'admin' ? 'admin' : 'operator')

<style>
    .monitoring-shell {
        font-family: 'Inter', 'Nunito', sans-serif;
    }

    .monitoring-toolbar {
        margin-bottom: 1.5rem;
    }

    .monitoring-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 20px;
        box-shadow: 0 22px 46px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .monitoring-card .card-body {
        padding: 1.75rem;
    }

    .monitoring-card .form-label {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.65rem;
    }

    .monitoring-card .form-select,
    .monitoring-card .form-control {
        border-radius: 12px;
        border: 1px solid #dbe4f0;
        padding: 0.82rem 0.95rem;
        box-shadow: none;
        color: #334155;
    }

    .monitoring-card .form-select:focus,
    .monitoring-card .form-control:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.12);
    }

    .monitoring-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        border: 1px solid #cbd5e1;
        font-size: 0.93rem;
    }

    .monitoring-table th,
    .monitoring-table td {
        border: 1px solid #cbd5e1;
        padding: 0.85rem 0.8rem;
        vertical-align: top;
        color: #1e293b;
    }

    .monitoring-table thead th {
        background: #f8fafc;
        text-align: center;
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 0.03em;
    }

    .monitoring-table .col-no {
        width: 60px;
        text-align: center;
    }

    .monitoring-table .col-procedure {
        width: 25%;
    }

    .monitoring-table .col-check {
        width: 28%;
    }

    .monitoring-table .col-result,
    .monitoring-table .col-action {
        width: 23%;
    }

    .procedure-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.35rem;
    }

    .procedure-note {
        color: #64748b;
        font-size: 0.82rem;
        line-height: 1.5;
    }

    .choice-stack {
        display: grid;
        gap: 0.7rem;
        padding-top: 0.1rem;
    }

    .choice-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        font-weight: 600;
        color: #1e293b;
        cursor: pointer;
    }

    .choice-item input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .choice-box {
        width: 18px;
        height: 18px;
        border: 2px solid #94a3b8;
        border-radius: 4px;
        background: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .choice-box::after {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 2px;
        background: #2563eb;
        transform: scale(0);
        transition: transform 0.2s ease;
    }

    .choice-item input:checked + .choice-box {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
    }

    .choice-item input:checked + .choice-box::after {
        transform: scale(1);
    }

    .cell-textarea {
        min-height: 150px;
        resize: vertical;
    }

    .action-row {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.25rem;
        flex-wrap: wrap;
    }

    .btn-soft {
        border-radius: 14px;
        padding: 0.8rem 1.35rem;
        font-weight: 700;
    }

    @media (max-width: 992px) {
        .monitoring-table {
            min-width: 980px;
        }
    }

    @media (max-width: 768px) {
        .monitoring-card .card-body {
            padding: 1rem;
        }
    }
</style>

<div class="container-fluid monitoring-shell py-2">
    <div class="monitoring-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">Tambah Monitoring SOP</h4>
            <p class="text-muted mb-0">Format lembar monitoring disusun mengikuti tampilan form pada dokumen contoh.</p>
        </div>

        <a href="{{ route($prefix . '.monitoring.index') }}" class="btn btn-outline-secondary btn-soft">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="monitoring-card">
        <div class="card-body">
            <form method="POST" action="{{ route($prefix . '.monitoring.store') }}" id="monitoringForm">
                @csrf

                <div class="mb-4">
                    <label class="form-label">SOP</label>
                    <select name="id_sop" class="form-select" required>
                        <option value="">Pilih SOP</option>
                        @foreach($sops as $sop)
                            <option value="{{ $sop->id_sop }}" {{ old('id_sop') == $sop->id_sop ? 'selected' : '' }}>{{ $sop->nama_sop }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="kriteria_penilaian" id="kriteria_penilaian" value="{{ old('kriteria_penilaian') }}">

                <div class="table-responsive">
                    <table class="monitoring-table">
                        <thead>
                            <tr>
                                <th class="col-no">No</th>
                                <th class="col-procedure">Prosedur</th>
                                <th class="col-check">Penilaian terhadap penerapan</th>
                                <th class="col-result">Catatan hasil</th>
                                <th class="col-action">Tindakan yang harus di ambil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center fw-semibold">1</td>
                                <td>
                                    <div class="procedure-title">Pelaksanaan SOP</div>
                                    <div class="procedure-note">Amati penerapan SOP pada unit kerja dan tentukan apakah prosedur berjalan dengan baik atau belum berjalan dengan baik.</div>
                                </td>
                                <td>
                                    <div class="choice-stack">
                                        <label class="choice-item">
                                            <input type="radio" class="monitoring-choice" name="monitoring_choice" value="Berjalan dengan baik" {{ old('kriteria_penilaian') === 'Berjalan dengan baik' ? 'checked' : '' }}>
                                            <span class="choice-box"></span>
                                            <span>Berjalan dengan baik</span>
                                        </label>
                                        <label class="choice-item">
                                            <input type="radio" class="monitoring-choice" name="monitoring_choice" value="Tidak berjalan dengan baik" {{ old('kriteria_penilaian') === 'Tidak berjalan dengan baik' ? 'checked' : '' }}>
                                            <span class="choice-box"></span>
                                            <span>Tidak berjalan dengan baik</span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <textarea name="hasil_monitoring" class="form-control cell-textarea" rows="6" required>{{ old('hasil_monitoring') }}</textarea>
                                </td>
                                <td>
                                    <textarea name="catatan" class="form-control cell-textarea" rows="6">{{ old('catatan') }}</textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="action-row">
                    <a href="{{ route($prefix . '.monitoring.index') }}" class="btn btn-light btn-soft border">Batal</a>
                    <button type="submit" class="btn btn-primary btn-soft">
                        <i class="bi bi-check2-circle me-2"></i>Simpan Monitoring
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hiddenInput = document.getElementById('kriteria_penilaian');
    const choices = document.querySelectorAll('.monitoring-choice');
    const form = document.getElementById('monitoringForm');

    function syncChoice() {
        const selected = Array.from(choices).find((choice) => choice.checked);
        hiddenInput.value = selected ? selected.value : '';
    }

    choices.forEach((choice) => {
        choice.addEventListener('change', syncChoice);
    });

    form?.addEventListener('submit', function (event) {
        syncChoice();

        if (!hiddenInput.value) {
            event.preventDefault();
            alert('Pilih penilaian monitoring terlebih dahulu.');
        }
    });

    syncChoice();
});
</script>
@endsection
