@extends('layouts.sidebarmenu')

@section('content')
@php($prefix = strtolower(Auth::user()->role ?? 'admin'))
@php($pageRole = strtolower($role ?? Auth::user()->role ?? 'admin'))
@php($theme = [
    'admin' => ['accent' => '#0d47a1', 'soft' => '#dbeafe', 'label' => 'Seluruh repositori SOP'],
    'operator' => ['accent' => '#0f766e', 'soft' => '#ccfbf1', 'label' => 'Fokus pada SOP tim kerja Anda'],
    'viewer' => ['accent' => '#7c3aed', 'soft' => '#ede9fe', 'label' => 'Mode baca dokumen aktif'],
][$pageRole])

<style>
    .quick-hero {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.9), transparent 26%),
            linear-gradient(135deg, {{ $theme['soft'] }} 0%, #ffffff 52%, #f8fbff 100%);
        border: 1px solid rgba(15, 23, 42, 0.05);
        border-radius: 30px;
        padding: 34px;
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.07);
    }
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #ffffff;
        color: {{ $theme['accent'] }};
        font-size: 0.82rem;
        font-weight: 700;
        border: 1px solid rgba(15, 23, 42, 0.06);
    }
    .hero-note {
        max-width: 640px;
    }
    .summary-card {
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        border: 1px solid rgba(15, 23, 42, 0.05);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.05);
        height: 100%;
    }
    .summary-icon,
    .hero-icon,
    .subject-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, {{ $theme['soft'] }} 0%, #ffffff 100%);
        color: {{ $theme['accent'] }};
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.85), 0 10px 18px rgba(15, 23, 42, 0.05);
    }
    .summary-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        font-size: 1.4rem;
        margin-bottom: 16px;
    }
    .hero-icon {
        width: 72px;
        height: 72px;
        border-radius: 24px;
        font-size: 1.8rem;
    }
    .subject-card {
        border-radius: 28px;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.95), transparent 22%),
            linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        border: 1px solid rgba(15, 23, 42, 0.05);
        position: relative;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.06);
        height: 100%;
    }
    .subject-card:hover {
        transform: translateY(-10px);
        border-color: {{ $theme['accent'] }};
        box-shadow: 0 28px 48px rgba(15, 23, 42, 0.12);
    }
    .subject-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 6px;
        background: linear-gradient(90deg, {{ $theme['accent'] }} 0%, #0f172a 100%);
    }
    .subject-card-body {
        padding: 28px;
    }
    .subject-icon {
        width: 68px;
        height: 68px;
        border-radius: 22px;
        font-size: 1.7rem;
    }
    .subject-count {
        min-width: 78px;
        padding: 10px 14px;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid rgba(15, 23, 42, 0.08);
        color: #0f172a;
        font-weight: 800;
        text-align: center;
        box-shadow: 0 10px 18px rgba(15, 23, 42, 0.04);
    }
    .subject-title {
        color: #0f172a;
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 10px;
    }
    .subject-desc {
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.6;
        min-height: 52px;
        margin-bottom: 18px;
    }
    .subject-action {
        color: #ffffff;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 11px 16px;
        border-radius: 999px;
        background: linear-gradient(135deg, {{ $theme['accent'] }} 0%, #0f172a 100%);
        box-shadow: 0 14px 26px rgba(15, 23, 42, 0.12);
    }
    .subject-arrow {
        width: 44px;
        height: 44px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: {{ $theme['accent'] }};
        font-size: 1.15rem;
        border: 1px solid rgba(15, 23, 42, 0.05);
    }
    .fade-up {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
        .quick-hero {
            padding: 24px;
        }
        .subject-card-body {
            padding: 22px;
        }
        .subject-title {
            font-size: 1.22rem;
        }
    }
</style>

<div class="container-fluid app-page-shell py-4">
    <div class="app-page-header">
        <div>
            <h1 class="app-page-title">Dashboard Akses Cepat</h1>
            <p class="app-page-subtitle">Halaman ini tetap bagian dari dashboard agar navigasi terasa konsisten. Pilih subjek untuk langsung membuka daftar SOP yang sesuai dengan hak akses Anda.</p>
        </div>
    </div>

    <div class="quick-hero mb-4 fade-up">
        <div class="d-flex justify-content-between align-items-start gap-4 flex-wrap">
            <div>
                <div class="role-badge mb-3">
                    <i class="bi bi-lightning-charge-fill"></i>
                    {{ strtoupper($pageRole) }} | {{ $theme['label'] }}
                </div>
                <h3 class="fw-bold text-dark mb-2">Akses Cepat SOP</h3>
                <p class="text-muted mb-0 hero-note">Pilih subjek untuk langsung membuka daftar SOP yang relevan dengan hak akses Anda. Tampilan ini dirapikan supaya lebih clean, lebih nyaman dilihat, dan tetap terasa profesional.</p>
            </div>
            <div class="hero-icon d-none d-md-inline-flex">
                <i class="bi bi-grid-1x2-fill"></i>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4 fade-up" style="animation-delay: 0.05s;">
            <div class="summary-card">
                <div class="summary-icon"><i class="bi bi-tags-fill"></i></div>
                <div class="text-muted small fw-semibold">TOTAL SUBJEK</div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $summary['total_subjek'] ?? 0 }}</div>
                <div class="text-muted small">Kategori yang bisa Anda buka sekarang.</div>
            </div>
        </div>
        <div class="col-md-4 fade-up" style="animation-delay: 0.1s;">
            <div class="summary-card">
                <div class="summary-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div class="text-muted small fw-semibold">TOTAL SOP AKTIF</div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $summary['total_sop'] ?? 0 }}</div>
                <div class="text-muted small">Dihitung dari SOP aktif pada setiap subjek.</div>
            </div>
        </div>
        <div class="col-md-4 fade-up" style="animation-delay: 0.15s;">
            <div class="summary-card">
                <div class="summary-icon"><i class="bi bi-stars"></i></div>
                <div class="text-muted small fw-semibold">TAMPILAN</div>
                <div class="h4 fw-bold text-dark mb-2">Ringkas dan Bersih</div>
                <div class="text-muted small">Setiap kartu langsung mengarahkan ke daftar SOP berdasarkan subjek pilihan.</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($subjek as $index => $s)
            <div class="col-xl-4 col-lg-6 fade-up" style="animation-delay: {{ 0.2 + ($index * 0.05) }}s;">
                <a href="{{ route($prefix . '.sop.index', ['nama_subjek' => $s->nama_subjek]) }}" class="text-decoration-none">
                    <div class="subject-card">
                        <div class="subject-card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div class="subject-icon">
                                    <i class="bi bi-file-earmark-richtext-fill"></i>
                                </div>
                                <div class="subject-count">
                                    {{ $s->visible_sop_count ?? 0 }}
                                    <div class="small fw-semibold text-muted">Aktif</div>
                                </div>
                            </div>

                            <h5 class="subject-title">{{ $s->nama_subjek }}</h5>

                            <div class="subject-desc">
                                {{ $s->deskripsi ?: 'Buka subjek ini untuk melihat daftar SOP aktif yang sudah tersusun rapi sesuai kategori.' }}
                            </div>

                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <span class="subject-action">
                                    Buka Dokumen SOP
                                    <i class="bi bi-arrow-right-short fs-5"></i>
                                </span>
                                <span class="subject-arrow">
                                    <i class="bi bi-arrow-up-right"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="summary-card p-5">
                    <div class="summary-icon mx-auto"><i class="bi bi-folder-x"></i></div>
                    <h5 class="fw-bold text-dark mt-3">Belum ada subjek tersedia</h5>
                    <p class="text-muted mb-0">Data akses cepat akan muncul di sini setelah subjek dan SOP aktif tersedia.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.fade-up').forEach((item) => {
            item.style.opacity = '1';
        });
    });
</script>
@endsection
