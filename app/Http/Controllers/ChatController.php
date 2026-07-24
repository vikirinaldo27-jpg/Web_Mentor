<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Notifikasi;
use App\Models\PengajuanMentoring;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(PengajuanMentoring $pengajuan): View
    {
        $user = auth()->user();
        $this->authorizeAccess($pengajuan, $user);

        $pengajuan->load('mahasiswa.mahasiswaProfil', 'mentorProfil.user');

        $partnerName = $user->peran === 'mahasiswa'
            ? $pengajuan->mentorProfil->user->nama . ($pengajuan->mentorProfil->gelar ? ', ' . $pengajuan->mentorProfil->gelar : '')
            : $pengajuan->mahasiswa->nama;

        $messages = Chat::where('pengajuan_id', $pengajuan->id)
            ->with('sender')
            ->oldest()
            ->get();

        $ratedPengajuanIds = $user->peran === 'mahasiswa'
            ? \App\Models\Ulasan::where('mahasiswa_id', $user->id)->pluck('pengajuan_id')->toArray()
            : [];

        return view('chat.index', compact('pengajuan', 'messages', 'partnerName', 'ratedPengajuanIds'));
    }

    public function fetch(Request $request, PengajuanMentoring $pengajuan): JsonResponse
    {
        $user = auth()->user();
        $this->authorizeAccess($pengajuan, $user);

        $lastId = (int) $request->last_id;

        $messages = Chat::where('pengajuan_id', $pengajuan->id)
            ->when($lastId, function ($q) use ($lastId) {
                $q->where('id', '>', $lastId);
            })
            ->with('sender')
            ->oldest()
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($m) {
                return [
                    'id' => $m->id,
                    'sender_id' => $m->sender_id,
                    'sender_name' => $m->sender->nama,
                    'message' => e($m->pesan),
                    'time' => $m->created_at->diffForHumans(),
                ];
            }),
        ]);
    }

    public function send(Request $request, PengajuanMentoring $pengajuan): JsonResponse
    {
        $user = auth()->user();
        $this->authorizeAccess($pengajuan, $user);

        $data = $request->validate([
            'pesan' => 'required|string|max:2000',
        ]);

        $chat = Chat::create([
            'pengajuan_id' => $pengajuan->id,
            'sender_id' => $user->id,
            'pesan' => $data['pesan'],
        ]);

        $chat->load('sender');

        $recipientId = $user->peran === 'mahasiswa'
            ? $pengajuan->mentorProfil->user_id
            : $pengajuan->mahasiswa_id;

        Notifikasi::create([
            'user_id' => $recipientId,
            'judul' => 'Pesan Baru dari ' . $user->nama,
            'pesan' => Str::limit($data['pesan'], 100),
            'url' => route('chat.index', $pengajuan->id),
            'dibaca' => false,
        ]);

        return response()->json([
            'id' => $chat->id,
            'sender_id' => $chat->sender_id,
            'sender_name' => $chat->sender->nama,
            'message' => e($chat->pesan),
            'time' => $chat->created_at->diffForHumans(),
        ]);
    }

    private function authorizeAccess(PengajuanMentoring $pengajuan, $user): void
    {
        $profil = $user->mentorProfil;

        $isMahasiswa = $pengajuan->mahasiswa_id === $user->id;
        $isMentor = $profil && $pengajuan->mentor_id === $profil->id;

        abort_unless($isMahasiswa || $isMentor, 403);
        abort_unless(in_array($pengajuan->status, ['disetujui', 'selesai']), 403);
    }
}
