@extends('layouts.dashboard')

@section('title', 'Ajukan Konsultasi')

@section('content')
<div class="row g-4">
    <div class="col-12 col-lg-8 mx-auto">
        <div class="card content-card mb-4">
            <div class="card-header">
                <i class="bi bi-send-fill me-2"></i>Ajukan Konsultasi
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-4">
                    @if ($mentor->foto)
                        <img src="{{ asset('storage/' . $mentor->foto) }}"
                             class="rounded-circle border border-2 border-primary"
                             style="width: 56px; height: 56px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center border border-2 border-primary"
                             style="width: 56px; height: 56px; font-size: 1.3rem; color: #fff;">
                            {{ strtoupper(substr($mentor->user->nama, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h6 class="fw-bold mb-0">
                            {{ $mentor->user->nama }}{{ $mentor->gelar ? ', ' . $mentor->gelar : '' }}
                        </h6>
                        <p class="text-muted mb-0" style="font-size: .85rem;">
                            @if ($mentor->perusahaan)
                                {{ $mentor->perusahaan }}
                            @else
                                {{ $mentor->universitas }}
                            @endif
                        </p>
                    </div>
                </div>

                @if ($jadwals->isEmpty())
                    <div class="text-center py-5">
                        <div class="mb-3" style="font-size: 3rem; color: #cbd5e1;">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <h6 class="fw-bold">Belum Ada Jadwal Tersedia</h6>
                        <p class="text-muted mx-auto" style="max-width: 400px; font-size: .9rem;">
                            <strong>{{ $mentor->user->nama }}{{ $mentor->gelar ? ', ' . $mentor->gelar : '' }}</strong>
                            belum menambahkan jadwal konsultasi. Silakan cek kembali nanti atau cari mentor lain.
                        </p>
                        <div class="d-flex gap-2 justify-content-center mt-3">
                            <a href="{{ route('mahasiswa.cari-mentor') }}" class="btn btn-primary rounded-pill">
                                <i class="bi bi-search me-1"></i> Cari Mentor Lain
                            </a>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('mahasiswa.pengajuan.store') }}">
                        @csrf
                        <input type="hidden" name="mentor_id" value="{{ $mentor->id }}">
                        <input type="hidden" name="kategori_id" value="{{ $mentor->keahlians->first()?->kategori_id }}">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Topik Konsultasi <span class="text-danger">*</span></label>
                                <input type="text" name="judul"
                                       class="form-control @error('judul') is-invalid @enderror"
                                       value="{{ old('judul') }}"
                                       placeholder="Ketik topik konsultasi..." required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Pilih Jadwal <span class="text-danger">*</span></label>
                                <p class="text-muted mb-2" style="font-size: .8rem;">
                                    <i class="bi bi-info-circle me-1"></i>Klik salah satu slot waktu yang tersedia di bawah ini.
                                </p>
                                @error('slot')
                                    <div class="text-danger mb-2" style="font-size: .85rem;">{{ $message }}</div>
                                @enderror

                                <input type="hidden" name="jadwal_id" id="selectedJadwalId" value="{{ old('jadwal_id') }}">
                                <input type="hidden" name="jam" id="selectedJam" value="{{ old('jam') }}">
                                <input type="hidden" name="tanggal" id="selectedTanggal" value="{{ old('tanggal') }}">

                                <div class="row g-2">
                                    @foreach ($jadwals as $tanggalLabel => $slots)
                                        <div class="col-12">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="bi bi-calendar3 text-primary"></i>
                                                <span class="fw-semibold" style="font-size: .9rem;">{{ $tanggalLabel }}</span>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                @foreach ($slots as $slot)
                                                    @php
                                                        $slotKey = $slot->jadwal_id . '|' . $slot->jam_mulai . '|' . $slot->tanggal->format('Y-m-d');
                                                    @endphp
                                                    <label class="jadwal-option">
                                                        <input type="radio" name="slot" value="{{ $slotKey }}"
                                                               class="slot-radio"
                                                               {{ old('slot') == $slotKey ? 'checked' : '' }}>
                                                        <span class="jadwal-time">
                                                            <i class="bi bi-clock me-1"></i>
                                                            {{ $slot->jam_mulai }}
                                                            –
                                                            {{ $slot->jam_selesai }}
                                                            <i class="bi bi-check-lg ms-1 check-icon"></i>
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($jadwals->isNotEmpty())
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-lightbulb me-1"></i>Tip: Pilih waktu yang sesuai dengan jadwal Anda.
                                        </small>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Deskripsi Konsultasi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" rows="5"
                                          class="form-control @error('deskripsi') is-invalid @enderror"
                                          placeholder="Jelaskan apa yang ingin Anda konsultasikan..." required>{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-muted mt-1" style="font-size: .8rem;">
                                    Minimal 10 karakter
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('mahasiswa.cari-mentor') }}" class="btn btn-outline-secondary rounded-pill">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-send me-1"></i> Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.slot-radio').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (this.checked) {
                var parts = this.value.split('|');
                document.getElementById('selectedJadwalId').value = parts[0];
                document.getElementById('selectedJam').value = parts[1];
                document.getElementById('selectedTanggal').value = parts[2];
            }
        });
    });
    @if (old('slot'))
        document.querySelectorAll('.slot-radio').forEach(function (radio) {
            if (radio.value === '{{ old('slot') }}') {
                radio.checked = true;
                var parts = radio.value.split('|');
                document.getElementById('selectedJadwalId').value = parts[0];
                document.getElementById('selectedJam').value = parts[1];
                document.getElementById('selectedTanggal').value = parts[2];
            }
        });
    @endif
</script>
@endpush

@push('styles')
<style>
    .jadwal-option {
        display: inline-block;
        cursor: pointer;
    }
    .jadwal-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .jadwal-time {
        display: flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: .75rem;
        font-size: .875rem;
        font-weight: 500;
        color: #1e293b;
        transition: all .2s;
    }
    .jadwal-time .check-icon {
        display: none;
        font-size: .8rem;
    }
    .jadwal-option:hover .jadwal-time {
        border-color: #0ea5e9;
        background: #f0f9ff;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(14, 165, 233, .15);
    }
    .jadwal-option input:checked + .jadwal-time {
        border-color: #0ea5e9;
        background: #0ea5e9;
        color: #fff;
    }
    .jadwal-option input:checked + .jadwal-time .check-icon {
        display: inline-block;
    }
</style>
@endpush
@endsection
