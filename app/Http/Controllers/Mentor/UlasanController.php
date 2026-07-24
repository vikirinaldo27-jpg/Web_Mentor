<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use Illuminate\View\View;

class UlasanController extends Controller
{
    public function index(): View
    {
        $profil = auth()->user()->mentorProfil;
        abort_unless($profil, 403, 'Lengkapi profil mentor terlebih dahulu.');

        $ulasans = Ulasan::where('mentor_id', $profil->id)
            ->with('mahasiswa.mahasiswaProfil', 'pengajuan.kategori')
            ->latest()
            ->paginate(10);

        $rataRating = round($ulasans->avg('rating'), 1);
        $jumlahUlasan = $ulasans->total();

        return view('mentor.ulasan-index', compact('ulasans', 'rataRating', 'jumlahUlasan'));
    }
}
