@extends('layouts.dashboard')

@section('title', 'Profil ' . $mentor->user->nama)

@section('content')
<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card content-card">
            <div class="card-body text-center p-4">
                @if ($mentor->foto)
                    <img src="{{ asset('storage/' . $mentor->foto) }}"
                         class="rounded-circle border border-3 border-primary d-block mx-auto"
                         style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto border border-3 border-primary"
                         style="width: 120px; height: 120px; font-size: 2.5rem; color: #fff;">
                        {{ strtoupper(substr($mentor->user->nama, 0, 1)) }}
                    </div>
                @endif

                <h5 class="fw-bold mt-3 mb-0">
                    {{ $mentor->user->nama }}{{ $mentor->gelar ? ', ' . $mentor->gelar : '' }}
                </h5>

                @if ($mentor->status_verifikasi === 'terverifikasi')
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill mt-1">
                        <i class="bi bi-patch-check-fill me-1"></i>Terverifikasi
                    </span>
                @endif

                <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                    <div class="text-warning">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($mentor->rata_rating) ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <span class="fw-bold">{{ number_format($mentor->rata_rating, 1) }}</span>
                    <span class="text-muted" style="font-size: .85rem;">({{ $mentor->jumlah_ulasan }})</span>
                </div>

                @if ($mentor->keahlians->isNotEmpty())
                    <div class="d-flex flex-wrap justify-content-center gap-1 my-3">
                        @foreach ($mentor->keahlians as $keahlian)
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                {{ $keahlian->kategori->nama }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <a href="{{ route('mahasiswa.pengajuan.create', $mentor->id) }}"
                   class="btn btn-primary rounded-pill w-100 mt-2">
                    <i class="bi bi-send me-1"></i> Ajukan Konsultasi
                </a>
                <a href="{{ route('mahasiswa.cari-mentor') }}" class="btn btn-outline-secondary rounded-pill w-100 mt-2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card content-card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi Profil
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12 col-sm-6">
                        <label class="text-muted d-block" style="font-size: .8rem;">Universitas</label>
                        <span class="fw-semibold">{{ $mentor->universitas }}</span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="text-muted d-block" style="font-size: .8rem;">Tahun Lulus</label>
                        <span class="fw-semibold">{{ $mentor->tahun_lulus ?? '—' }}</span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="text-muted d-block" style="font-size: .8rem;">Pekerjaan</label>
                        <span class="fw-semibold">{{ $mentor->pekerjaan ?? '—' }}</span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="text-muted d-block" style="font-size: .8rem;">Perusahaan</label>
                        <span class="fw-semibold">{{ $mentor->perusahaan ?? '—' }}</span>
                    </div>
                    @if ($mentor->pengalaman)
                        <div class="col-12">
                            <label class="text-muted d-block" style="font-size: .8rem;">Pengalaman</label>
                            <p class="mb-0 mt-1" style="font-size: .9rem; white-space: pre-wrap;">{{ $mentor->pengalaman }}</p>
                        </div>
                    @endif
                    @if ($mentor->bio)
                        <div class="col-12">
                            <label class="text-muted d-block" style="font-size: .8rem;">Bio</label>
                            <p class="mb-0 mt-1" style="font-size: .9rem; white-space: pre-wrap;">{{ $mentor->bio }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ($ulasans->isNotEmpty())
            <div class="card content-card">
                <div class="card-header">
                    <i class="bi bi-star-fill text-warning me-2"></i>Ulasan dari Mahasiswa
                </div>
                <div class="card-body p-0">
                    @foreach ($ulasans as $u)
                        <div class="p-3 border-bottom">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 36px; height: 36px; font-weight: 600;">
                                    {{ strtoupper(substr($u->mahasiswa->nama, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-semibold" style="font-size: .9rem;">{{ $u->mahasiswa->nama }}</span>
                                        <div class="text-warning" style="font-size: .8rem;">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $u->rating ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="text-muted" style="font-size: .75rem;">
                                        {{ $u->created_at->diffForHumans() }}
                                    </div>
                                    @if ($u->komentar)
                                        <p class="mb-0 mt-1" style="font-size: .85rem;">{{ $u->komentar }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
