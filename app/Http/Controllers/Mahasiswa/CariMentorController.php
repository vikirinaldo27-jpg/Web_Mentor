<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KategoriKeahlian;
use App\Models\MentorProfil;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CariMentorController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->search;
        $kategoriId = $request->kategori;
        $perPage = 9;

        $query = DB::table('profil_mentor')
            ->select('profil_mentor.id', 'profil_mentor.user_id')
            ->join('users', 'users.id', '=', 'profil_mentor.user_id')
            ->leftJoin('keahlian', 'keahlian.mentor_id', '=', 'profil_mentor.id')
            ->leftJoin('kategori_keahlian', 'kategori_keahlian.id', '=', 'keahlian.kategori_id')
            ->where('users.peran', 'mentor');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('users.nama', 'like', "%{$keyword}%")
                  ->orWhere('kategori_keahlian.nama', 'like', "%{$keyword}%")
                  ->orWhere('profil_mentor.universitas', 'like', "%{$keyword}%")
                  ->orWhere('profil_mentor.perusahaan', 'like', "%{$keyword}%")
                  ->orWhere('profil_mentor.pengalaman', 'like', "%{$keyword}%");
            });
        }

        if ($kategoriId) {
            $query->where('keahlian.kategori_id', $kategoriId);
        }

        $idsQuery = $query->distinct()->select('profil_mentor.id');

        $mentors = MentorProfil::with(['user', 'keahlians.kategori'])
            ->whereIn('id', $idsQuery)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $kategoris = KategoriKeahlian::orderBy('nama')->get();

        return view('mahasiswa.cari-mentor', compact('mentors', 'keyword', 'kategoriId', 'kategoris'));
    }

    public function show(MentorProfil $mentor)
    {
        $mentor->load(['user', 'keahlians.kategori']);

        $ulasans = Ulasan::where('mentor_id', $mentor->id)
            ->with('mahasiswa', 'pengajuan')
            ->latest()
            ->take(10)
            ->get();

        return view('mahasiswa.mentor-profil', compact('mentor', 'ulasans'));
    }
}
