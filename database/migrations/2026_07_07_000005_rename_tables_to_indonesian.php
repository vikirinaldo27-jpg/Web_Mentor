<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('mahasiswa_profils', 'profil_mahasiswa');
        Schema::rename('mentor_profils', 'profil_mentor');
        Schema::rename('kategori_keahlians', 'kategori_keahlian');
        Schema::rename('keahlians', 'keahlian');
        Schema::rename('jadwals', 'jadwal');
        Schema::rename('pengajuan_mentorings', 'pengajuan_mentoring');
        Schema::rename('sesi_mentorings', 'sesi_mentoring');
        Schema::rename('ulasans', 'ulasan');
        Schema::rename('notifikasis', 'notifikasi');
        Schema::rename('chats', 'chat');
    }

    public function down(): void
    {
        Schema::rename('profil_mahasiswa', 'mahasiswa_profils');
        Schema::rename('profil_mentor', 'mentor_profils');
        Schema::rename('kategori_keahlian', 'kategori_keahlians');
        Schema::rename('keahlian', 'keahlians');
        Schema::rename('jadwal', 'jadwals');
        Schema::rename('pengajuan_mentoring', 'pengajuan_mentorings');
        Schema::rename('sesi_mentoring', 'sesi_mentorings');
        Schema::rename('ulasan', 'ulasans');
        Schema::rename('notifikasi', 'notifikasis');
        Schema::rename('chat', 'chats');
    }
};
