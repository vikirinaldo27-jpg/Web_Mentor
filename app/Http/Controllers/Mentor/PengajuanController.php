<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\PengajuanMentoring;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    public function index(): View
    {
        $profil = auth()->user()->mentorProfil;
        abort_unless($profil, 403, 'Lengkapi profil mentor terlebih dahulu.');

        $pengajuans = PengajuanMentoring::where('mentor_id', $profil->id)
            ->with('mahasiswa.mahasiswaProfil', 'kategori')
            ->latest()
            ->paginate(10);

        return view('mentor.pengajuan-index', compact('pengajuans'));
    }

    public function show(PengajuanMentoring $pengajuan): View
    {
        $profil = auth()->user()->mentorProfil;
        abort_unless($profil, 403);
        abort_if($pengajuan->mentor_id !== $profil->id, 403);

        $pengajuan->load('mahasiswa.mahasiswaProfil', 'kategori');

        return view('mentor.pengajuan-show', compact('pengajuan'));
    }

    public function terima(PengajuanMentoring $pengajuan): RedirectResponse
    {
        $profil = auth()->user()->mentorProfil;
        abort_if($pengajuan->mentor_id !== $profil->id, 403);
        abort_if($pengajuan->status !== 'pending', 422, 'Pengajuan sudah diproses.');

        $pengajuan->update(['status' => 'disetujui']);

        return redirect()->route('mentor.pengajuan.index')
            ->with('success', 'Pengajuan konsultasi telah diterima.');
    }

    public function selesai(PengajuanMentoring $pengajuan): RedirectResponse
    {
        $profil = auth()->user()->mentorProfil;
        abort_if($pengajuan->mentor_id !== $profil->id, 403);
        abort_if($pengajuan->status !== 'disetujui', 422, 'Pengajuan harus dalam status disetujui.');

        $pengajuan->update(['status' => 'selesai']);

        return redirect()->route('mentor.pengajuan.index')
            ->with('success', 'Konsultasi telah diselesaikan.');
    }

    public function tolak(Request $request, PengajuanMentoring $pengajuan): RedirectResponse
    {
        $profil = auth()->user()->mentorProfil;
        abort_if($pengajuan->mentor_id !== $profil->id, 403);
        abort_if($pengajuan->status !== 'pending', 422, 'Pengajuan sudah diproses.');

        $data = $request->validate([
            'catatan_mentor' => 'nullable|string|max:1000',
        ]);

        $pengajuan->update([
            'status' => 'ditolak',
            'catatan_mentor' => $data['catatan_mentor'] ?? null,
        ]);

        return redirect()->route('mentor.pengajuan.index')
            ->with('success', 'Pengajuan konsultasi telah ditolak.');
    }
}
