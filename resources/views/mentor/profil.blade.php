@extends('layouts.dashboard')

@section('title', 'Profil Mentor')

@section('content')
<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card content-card">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block">
                    @if ($profil && $profil->foto)
                        <img src="{{ asset('storage/' . $profil->foto) }}" alt="Foto"
                             class="rounded-circle border border-3 border-primary"
                             style="width: 130px; height: 130px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto border border-3 border-primary"
                             style="width: 130px; height: 130px; font-size: 3rem; color: #fff;">
                            {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <h5 class="mt-3 mb-1 fw-bold">
                    {{ auth()->user()->nama }}{{ $profil && $profil->gelar ? ', ' . $profil->gelar : '' }}
                </h5>
                <p class="text-muted mb-2" style="font-size: .85rem;">
                    <i class="bi bi-envelope me-1"></i>{{ auth()->user()->email }}
                </p>
                @if ($profil && $profil->status_verifikasi === 'terverifikasi')
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                        <i class="bi bi-patch-check-fill me-1"></i>Terverifikasi
                    </span>
                @else
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">
                        <i class="bi bi-clock me-1"></i>Belum Terverifikasi
                    </span>
                @endif
                <div class="mt-3">
                    <a href="{{ route('mentor.profil.edit') }}" class="btn btn-primary rounded-pill w-100">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profil
                    </a>
                </div>
            </div>
        </div>

        @if ($profil && $profil->pengalaman)
        <div class="card content-card mt-4">
            <div class="card-header">
                <i class="bi bi-briefcase-fill me-2"></i>Pengalaman
            </div>
            <div class="card-body">
                <p style="font-size: .9rem; white-space: pre-wrap;">{{ $profil->pengalaman }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-12 col-lg-8">
        <div class="card content-card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi Profil
            </div>
            <div class="card-body">
                @if ($profil)
                    <div class="row g-4">
                        <div class="col-12 col-sm-6">
                            <label class="text-muted d-block" style="font-size: .8rem;">Gelar</label>
                            <span class="fw-semibold">{{ $profil->gelar ?? '—' }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="text-muted d-block" style="font-size: .8rem;">Universitas / Almamater</label>
                            <span class="fw-semibold">{{ $profil->universitas }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="text-muted d-block" style="font-size: .8rem;">Tahun Lulus</label>
                            <span class="fw-semibold">{{ $profil->tahun_lulus ?? '—' }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="text-muted d-block" style="font-size: .8rem;">Pekerjaan</label>
                            <span class="fw-semibold">{{ $profil->pekerjaan ?? '—' }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="text-muted d-block" style="font-size: .8rem;">Perusahaan</label>
                            <span class="fw-semibold">{{ $profil->perusahaan ?? '—' }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="text-muted d-block" style="font-size: .8rem;">No. HP</label>
                            <span class="fw-semibold">{{ $profil->no_hp ?? '—' }}</span>
                        </div>
                    </div>
                    @if ($profil->bio)
                        <hr class="my-3">
                        <label class="text-muted d-block mb-2" style="font-size: .8rem;">Bio</label>
                        <p class="mb-0" style="font-size: .9rem;">{{ $profil->bio }}</p>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-person-plus" style="font-size: 3rem; color: #cbd5e1;"></i>
                        <p class="mt-2 text-muted">Profil mentor belum lengkap</p>
                        <a href="{{ route('mentor.profil.edit') }}" class="btn btn-primary rounded-pill">
                            <i class="bi bi-pencil me-1"></i> Lengkapi Profil
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
