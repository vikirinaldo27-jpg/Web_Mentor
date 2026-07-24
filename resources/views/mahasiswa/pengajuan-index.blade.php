@extends('layouts.dashboard')

@section('title', 'Pengajuan Saya')

@section('content')
<div class="card content-card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span><i class="bi bi-send-fill me-2"></i>Pengajuan Saya</span>
        <span class="badge bg-primary rounded-pill">{{ $pengajuans->total() }} pengajuan</span>
    </div>
    <div class="card-body p-0">
        @if ($pengajuans->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                <p class="mt-2 text-muted">Belum ada pengajuan.</p>
                <a href="{{ route('mahasiswa.cari-mentor') }}" class="btn btn-primary rounded-pill">
                    <i class="bi bi-search me-1"></i> Cari Mentor
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="padding-left: 1.5rem;">Mentor</th>
                            <th>Topik</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th class="text-end" style="padding-right: 1.5rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengajuans as $p)
                            <tr>
                                <td style="padding-left: 1.5rem;">
                                    <div class="d-flex align-items-center gap-2">
                                                @if ($p->mentorProfil->foto)
                                                    <img src="{{ asset('storage/' . $p->mentorProfil->foto) }}"
                                                         class="rounded-circle border border-2 border-primary"
                                                         style="width: 36px; height: 36px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                                                         style="width: 36px; height: 36px; font-size: .8rem; font-weight: 600;">
                                                        {{ strtoupper(substr($p->mentorProfil->user->nama, 0, 1)) }}
                                                    </div>
                                                @endif
                                        <div>
                                            <div class="fw-semibold" style="font-size: .9rem;">
                                                {{ $p->mentorProfil->user->nama }}{{ $p->mentorProfil->gelar ? ', ' . $p->mentorProfil->gelar : '' }}
                                            </div>
                                            <div class="text-muted" style="font-size: .75rem;">{{ $p->mentorProfil->perusahaan ?? $p->mentorProfil->universitas }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                        {{ $p->judul }}
                                    </span>
                                </td>
                                <td style="font-size: .9rem;">
                                    @if ($p->tanggal)
                                        {{ \Carbon\Carbon::parse($p->tanggal)->isoFormat('D MMM YYYY') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td style="font-size: .9rem;">
                                    @if ($p->jam)
                                        {{ \Carbon\Carbon::parse($p->jam)->format('H:i') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'pending' => ['label' => 'Menunggu', 'class' => 'badge-pending'],
                                            'disetujui' => ['label' => 'Disetujui', 'class' => 'badge-disetujui'],
                                            'ditolak' => ['label' => 'Ditolak', 'class' => 'badge-ditolak'],
                                            'selesai' => ['label' => 'Selesai', 'class' => 'badge-selesai'],
                                            'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'badge-ditolak'],
                                        ];
                                        $s = $statusMap[$p->status] ?? ['label' => ucfirst($p->status), 'class' => 'badge-pending'];
                                    @endphp
                                    <span class="badge-status {{ $s['class'] }}">{{ $s['label'] }}</span>
                                </td>
                                <td class="text-end" style="padding-right: 1.5rem;">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @if ($p->status === 'selesai' && !in_array($p->id, $ratedPengajuanIds))
                                            <button type="button" class="btn btn-sm btn-warning rounded-pill"
                                                    title="Beri Rating" data-bs-toggle="modal"
                                                    data-bs-target="#ulasanModal{{ $p->id }}">
                                                <i class="bi bi-star-fill"></i>
                                            </button>
                                        @endif
                                        @if (in_array($p->status, ['disetujui', 'selesai']))
                                            <a href="{{ route('chat.index', $p->id) }}"
                                               class="btn btn-sm btn-primary rounded-pill" title="Chat">
                                                <i class="bi bi-chat-dots-fill"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal Ulasan --}}
                            @if ($p->status === 'selesai' && !in_array($p->id, $ratedPengajuanIds))
                                <div class="modal fade" id="ulasanModal{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="border-radius: 1rem; border: none;">
                                            <form method="POST" action="{{ route('mahasiswa.ulasan.store', $p->id) }}">
                                                @csrf
                                                <div class="modal-header border-0" style="padding: 1.25rem 1.5rem 0;">
                                                    <h6 class="modal-title fw-bold">
                                                        <i class="bi bi-star-fill text-warning me-1"></i>Beri Rating & Ulasan
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body" style="padding: 1rem 1.5rem;">
                                                    <p class="text-muted mb-3" style="font-size: .9rem;">
                                                        Konsultasi dengan
                                                        <strong>{{ $p->mentorProfil->user->nama }}{{ $p->mentorProfil->gelar ? ', ' . $p->mentorProfil->gelar : '' }}</strong>
                                                        sudah selesai. Berikan penilaian Anda.
                                                    </p>

                                                    <label class="form-label fw-semibold" style="font-size: .85rem;">Rating <span class="text-danger">*</span></label>
                                                    <div class="star-rating mb-3 text-center">
                                                        <div class="stars d-inline-flex flex-row-reverse gap-1">
                                                            @for ($i = 5; $i >= 1; $i--)
                                                                <input type="radio" name="rating" value="{{ $i }}" id="star{{ $p->id }}_{{ $i }}"
                                                                       {{ old('rating') == $i ? 'checked' : '' }} required>
                                                                <label for="star{{ $p->id }}_{{ $i }}" title="{{ $i }} bintang">
                                                                    <i class="bi bi-star fs-3"></i>
                                                                </label>
                                                            @endfor
                                                        </div>
                                                        @error('rating')
                                                            <div class="text-danger" style="font-size: .85rem;">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <label class="form-label fw-semibold" style="font-size: .85rem;">Komentar (opsional)</label>
                                                    <textarea name="komentar" rows="3" class="form-control"
                                                              placeholder="Tulis ulasan Anda..." maxlength="1000">{{ old('komentar') }}</textarea>
                                                </div>
                                                <div class="modal-footer border-0" style="padding: 0 1.5rem 1.25rem;">
                                                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning rounded-pill px-4">
                                                        <i class="bi bi-star-fill me-1"></i> Kirim Rating
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center p-3">
                {{ $pengajuans->links() }}
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .pagination { margin-bottom: 0; }
    .page-link { border-radius: .5rem !important; margin: 0 2px; border: none; color: #1e293b; font-weight: 500; }
    .page-item.active .page-link { background: #0ea5e9; color: #fff; }
    .page-item.disabled .page-link { color: #94a3b8; background: transparent; }

    .star-rating .stars input { display: none; }
    .star-rating .stars label { cursor: pointer; color: #cbd5e1; transition: color .15s; }
    .star-rating .stars label:hover,
    .star-rating .stars label:hover ~ label,
    .star-rating .stars input:checked ~ label { color: #f59e0b; }
</style>
@endpush
@endsection
