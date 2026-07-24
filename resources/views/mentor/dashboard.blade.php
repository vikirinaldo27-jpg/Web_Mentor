@extends('layouts.dashboard')

@section('title', 'Dashboard Mentor')

@section('content')
    {{-- Welcome Section --}}
    <div class="welcome-section">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(255,255,255,.2); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div>
                <h3>Selamat datang, {{ Auth::user()->nama }}!</h3>
                <p>Bantu mahasiswa menyelesaikan skripsi dan mengembangkan karir</p>
            </div>
        </div>
    </div>

    {{-- Statistik Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: #dbeafe; color: #2563eb;">
                            <i class="bi bi-inbox-fill"></i>
                        </div>
                        <div>
                            <div class="stat-label">Pengajuan Masuk</div>
                            <div class="stat-value">{{ $jumlahPengajuanMasuk }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <div class="stat-label">Perlu Ditinjau</div>
                            <div class="stat-value">{{ $pengajuanPending }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: #e0e7ff; color: #4338ca;">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div>
                            <div class="stat-label">Rating</div>
                            <div class="stat-value">{{ $rataRating }} <span style="font-size: .9rem; color: #f59e0b;"><i class="bi bi-star-fill"></i></span></div>
                            <div class="stat-desc">{{ $jumlahUlasan }} ulasan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: #d1fae5; color: #059669;">
                            <i class="bi bi-bookmark-star-fill"></i>
                        </div>
                        <div>
                            <div class="stat-label">Keahlian</div>
                            <div class="stat-value">{{ $jumlahKeahlian }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Pengajuan Terbaru --}}
        <div class="col-12 col-lg-8">
            <div class="card content-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-clock-history me-2"></i>Pengajuan Terbaru</span>
                    <a href="{{ route('mentor.pengajuan.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($pengajuanTerbaru->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 py-3" style="font-size: .8rem;">Mahasiswa</th>
                                        <th style="font-size: .8rem;">Judul</th>
                                        <th style="font-size: .8rem;">Kategori</th>
                                        <th style="font-size: .8rem;">Status</th>
                                        <th class="pe-3" style="font-size: .8rem;">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengajuanTerbaru as $p)
                                        <tr>
                                            <td class="ps-3 py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    @php $mhsFoto = $p->mahasiswa?->mahasiswaProfil?->foto; @endphp
                                                    @if ($mhsFoto)
                                                        <img src="{{ asset('storage/' . $mhsFoto) }}"
                                                             style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                                                    @else
                                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: .75rem; font-weight: 600;">
                                                            {{ strtoupper(substr($p->mahasiswa->nama ?? '?', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <span style="font-size: .875rem; font-weight: 500;">{{ $p->mahasiswa->nama ?? '—' }}</span>
                                                </div>
                                            </td>
                                            <td style="font-size: .85rem;">{{ Str::limit($p->judul, 25) }}</td>
                                            <td><span class="badge bg-light text-dark" style="font-size: .75rem;">{{ $p->kategori->nama ?? '—' }}</span></td>
                                            <td>
                                                @php
                                                    $badge = match($p->status) {
                                                        'pending' => 'badge-pending',
                                                        'disetujui' => 'badge-disetujui',
                                                        'ditolak' => 'badge-ditolak',
                                                        'selesai' => 'badge-selesai',
                                                        default => 'bg-secondary'
                                                    };
                                                    $label = match($p->status) {
                                                        'pending' => 'Pending',
                                                        'disetujui' => 'Disetujui',
                                                        'ditolak' => 'Ditolak',
                                                        'selesai' => 'Selesai',
                                                        default => $p->status
                                                    };
                                                @endphp
                                                <span class="badge badge-status {{ $badge }}">{{ $label }}</span>
                                            </td>
                                            <td class="pe-3" style="font-size: .8rem; color: #64748b;">{{ $p->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                            <p class="mt-2 text-muted">Belum ada pengajuan masuk</p>
                            <p class="text-muted" style="font-size: .85rem;">Pengajuan dari mahasiswa akan muncul di sini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Notifikasi & Info --}}
        <div class="col-12 col-lg-4">
            <div class="card content-card h-100">
                <div class="card-header">
                    <i class="bi bi-bell-fill me-2"></i>Notifikasi
                </div>
                <div class="card-body p-0">
                    @if ($notifikasi->count() > 0)
                        @foreach ($notifikasi as $n)
                            <a href="{{ route('notifikasi.baca', $n->id) }}" class="text-decoration-none">
                                <div class="activity-item px-3">
                                    <div class="activity-icon {{ $n->dibaca ? 'bg-light text-muted' : 'bg-primary bg-opacity-10 text-primary' }}">
                                        <i class="bi {{ $n->dibaca ? 'bi-envelope-open' : 'bi-envelope-fill' }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">{{ $n->judul }}</div>
                                        <div style="font-size: .8rem; color: #64748b;">{{ Str::limit($n->pesan, 50) }}</div>
                                        <div class="activity-time">{{ $n->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                            <p class="mt-2 text-muted" style="font-size: .875rem;">Tidak ada notifikasi</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card content-card mt-4">
                <div class="card-header">
                    <i class="bi bi-lightning-fill me-2"></i>Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('mentor.pengajuan.index') }}" class="btn btn-primary rounded-pill">
                            <i class="bi bi-inbox me-1"></i> Lihat Pengajuan
                        </a>
                        <a href="{{ route('mentor.jadwal.index') }}" class="btn btn-outline-primary rounded-pill">
                            <i class="bi bi-calendar-week me-1"></i> Kelola Jadwal
                        </a>
                        <a href="{{ route('mentor.profil') }}" class="btn btn-outline-secondary rounded-pill">
                            <i class="bi bi-person me-1"></i> Lengkapi Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
