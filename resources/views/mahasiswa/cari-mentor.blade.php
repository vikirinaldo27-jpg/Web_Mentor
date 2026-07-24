@extends('layouts.dashboard')

@section('title', 'Cari Mentor')

@section('content')
<div class="card content-card mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('mahasiswa.cari-mentor') }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-semibold" style="font-size: .85rem;">
                        <i class="bi bi-search me-1"></i>Cari Mentor
                    </label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nama, keahlian, instansi, atau topik..."
                           value="{{ old('search', $keyword) }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold" style="font-size: .85rem;">
                        <i class="bi bi-funnel me-1"></i>Filter Keahlian
                    </label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua Keahlian</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if ($keyword || $kategoriId)
    <p class="text-muted mb-3" style="font-size: .9rem;">
        <i class="bi bi-info-circle me-1"></i>
        Menampilkan hasil pencarian
        @if ($keyword)
            untuk "<strong>{{ $keyword }}</strong>"
        @endif
        @if ($kategoriId)
            di kategori <strong>{{ $kategoris->firstWhere('id', $kategoriId)?->nama }}</strong>
        @endif
        — <strong>{{ $mentors->total() }}</strong> mentor ditemukan
    </p>
@endif

@if ($mentors->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-emoji-frown" style="font-size: 3.5rem; color: #cbd5e1;"></i>
        <h5 class="mt-3 text-muted">Tidak ada mentor ditemukan</h5>
        <p class="text-muted" style="font-size: .9rem;">
            Coba gunakan kata kunci lain atau ubah filter keahlian.
        </p>
        <a href="{{ route('mahasiswa.cari-mentor') }}" class="btn btn-outline-primary rounded-pill mt-2">
            <i class="bi bi-x-circle me-1"></i> Reset Filter
        </a>
    </div>
@else
    <div class="row g-4">
        @foreach ($mentors as $mentor)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card content-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            @if ($mentor->foto)
                                <img src="{{ asset('storage/' . $mentor->foto) }}"
                                     class="rounded-circle border border-2 border-primary d-block mx-auto"
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto border border-2 border-primary"
                                     style="width: 80px; height: 80px; font-size: 1.8rem; color: #fff;">
                                    {{ strtoupper(substr($mentor->user->nama, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <h6 class="fw-bold mb-0">
                            {{ $mentor->user->nama }}{{ $mentor->gelar ? ', ' . $mentor->gelar : '' }}
                        </h6>

                        @if ($mentor->status_verifikasi === 'terverifikasi')
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill mt-1" style="font-size: .7rem;">
                                <i class="bi bi-patch-check-fill me-1"></i>Terverifikasi
                            </span>
                        @endif

                        <p class="text-muted mt-2 mb-1" style="font-size: .8rem;">
                            @if ($mentor->perusahaan)
                                <i class="bi bi-building me-1"></i>{{ $mentor->perusahaan }}
                            @else
                                <i class="bi bi-mortarboard me-1"></i>{{ $mentor->universitas }}
                            @endif
                        </p>

                        @if ($mentor->keahlians->isNotEmpty())
                            <div class="d-flex flex-wrap justify-content-center gap-1 my-2">
                                @foreach ($mentor->keahlians as $keahlian)
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" style="font-size: .7rem;">
                                        {{ $keahlian->kategori->nama }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if ($mentor->bio)
                            <p class="text-muted mt-2 mb-3" style="font-size: .8rem; line-height: 1.4;">
                                {{ Str::limit($mentor->bio, 100) }}
                            </p>
                        @endif

                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <div>
                                <i class="bi bi-star-fill text-warning me-1" style="font-size: .8rem;"></i>
                                <span class="fw-bold" style="font-size: .9rem;">
                                    {{ number_format($mentor->rata_rating, 1) }}
                                </span>
                                <span class="text-muted" style="font-size: .75rem;">
                                    ({{ $mentor->jumlah_ulasan }})
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-2">
                            <a href="{{ route('mahasiswa.mentor.show', $mentor->id) }}"
                               class="btn btn-outline-primary rounded-pill flex-grow-1">
                                <i class="bi bi-person me-1"></i> Lihat Profil
                            </a>
                            <a href="{{ route('mahasiswa.pengajuan.create', $mentor->id) }}"
                               class="btn btn-primary rounded-pill flex-grow-1">
                                <i class="bi bi-send me-1"></i> Ajukan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $mentors->links() }}
    </div>
@endif

@push('styles')
<style>
    .pagination { margin-bottom: 0; }
    .page-link {
        border-radius: .5rem !important;
        margin: 0 2px;
        border: none;
        color: #1e293b;
        font-weight: 500;
    }
    .page-item.active .page-link {
        background: #0ea5e9;
        color: #fff;
    }
    .page-item.disabled .page-link {
        color: #94a3b8;
        background: transparent;
    }
</style>
@endpush
@endsection
