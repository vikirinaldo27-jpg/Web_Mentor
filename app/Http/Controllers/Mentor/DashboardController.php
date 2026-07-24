<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\PengajuanMentoring;
use App\Models\Keahlian;
use App\Models\Ulasan;
use App\Models\Notifikasi;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $profil = $user->mentorProfil;

        if (!$profil) {
            $jumlahPengajuanMasuk = 0;
            $pengajuanPending = 0;
            $pengajuanDisetujui = 0;
            $pengajuanSelesai = 0;
            $jumlahKeahlian = 0;
            $rataRating = 0;
            $jumlahUlasan = 0;
            $pengajuanTerbaru = collect();
        } else {
            $pengajuansQuery = PengajuanMentoring::where('mentor_id', $profil->id);
            $jumlahPengajuanMasuk = (clone $pengajuansQuery)->count();
            $pengajuanPending = (clone $pengajuansQuery)->where('status', 'pending')->count();
            $pengajuanDisetujui = (clone $pengajuansQuery)->where('status', 'disetujui')->count();
            $pengajuanSelesai = (clone $pengajuansQuery)->where('status', 'selesai')->count();
            $jumlahKeahlian = Keahlian::where('mentor_id', $profil->id)->count();
            $rataRating = round(Ulasan::where('mentor_id', $profil->id)->avg('rating') ?? 0, 1);
            $jumlahUlasan = Ulasan::where('mentor_id', $profil->id)->count();

            $pengajuanTerbaru = (clone $pengajuansQuery)
                ->with('mahasiswa.mahasiswaProfil', 'kategori')
                ->latest()
                ->take(5)
                ->get();
        }

        $notifikasi = Notifikasi::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('mentor.dashboard', compact(
            'jumlahPengajuanMasuk',
            'pengajuanPending',
            'pengajuanDisetujui',
            'pengajuanSelesai',
            'jumlahKeahlian',
            'rataRating',
            'jumlahUlasan',
            'pengajuanTerbaru',
            'notifikasi'
        ));
    }
}
