<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengajuanRequest;
use App\Models\Jadwal;
use App\Models\MentorProfil;
use App\Models\PengajuanMentoring;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();

        $pengajuans = PengajuanMentoring::where('mahasiswa_id', $userId)
            ->with('mentorProfil.user')
            ->latest()
            ->paginate(10);

        $ratedPengajuanIds = \App\Models\Ulasan::where('mahasiswa_id', $userId)
            ->pluck('pengajuan_id')
            ->toArray();

        return view('mahasiswa.pengajuan-index', compact('pengajuans', 'ratedPengajuanIds'));
    }

    public function create(MentorProfil $mentor): View
    {
        $mentor->load('keahlians');
        $jadwals = Jadwal::where('mentor_id', $mentor->id)
            ->where('tersedia', true)
            ->where('tanggal', '>=', now()->today())
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        $existingBookings = \App\Models\PengajuanMentoring::where('mentor_id', $mentor->id)
            ->whereIn('status', ['pending', 'disetujui', 'selesai'])
            ->whereNotNull('jam')
            ->get()
            ->groupBy(fn ($p) => \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d') . '|' . \Carbon\Carbon::parse($p->jam)->format('H:00'));

        $jadwalSlots = collect();
        foreach ($jadwals as $j) {
            $mulai = \Carbon\Carbon::parse($j->jam_mulai);
            $selesai = \Carbon\Carbon::parse($j->jam_selesai);
            while ($mulai->copy()->addHour()->lessThanOrEqualTo($selesai)) {
                $slotJam = $mulai->format('H:00');
                $key = \Carbon\Carbon::parse($j->tanggal)->format('Y-m-d') . '|' . $slotJam;
                if (!$existingBookings->has($key)) {
                    $jadwalSlots->push((object) [
                        'jadwal_id' => $j->id,
                        'tanggal' => $j->tanggal,
                        'jam_mulai' => $slotJam,
                        'jam_selesai' => $mulai->copy()->addHour()->format('H:00'),
                        'tanggal_label' => \Carbon\Carbon::parse($j->tanggal)->isoFormat('dddd, D MMMM YYYY'),
                    ]);
                }
                $mulai->addHour();
            }
        }

        $jadwals = $jadwalSlots->groupBy('tanggal_label');

        return view('mahasiswa.pengajuan-create', compact('mentor', 'jadwals'));
    }

    public function store(PengajuanRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['mahasiswa_id'] = auth()->id();
        $data['status'] = 'pending';

        if (empty($data['kategori_id'])) {
            $mentor = MentorProfil::with('keahlians')->find($data['mentor_id']);
            $data['kategori_id'] = $mentor?->keahlians->first()?->kategori_id;
        }

        PengajuanMentoring::create($data);

        return redirect()->route('mahasiswa.pengajuan.index')
            ->with('success', 'Pengajuan konsultasi berhasil dikirim.');
    }
}
