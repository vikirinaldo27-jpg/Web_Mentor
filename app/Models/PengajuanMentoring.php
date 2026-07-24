<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanMentoring extends Model
{
    protected $table = 'pengajuan_mentoring';

    protected $fillable = [
        'mahasiswa_id',
        'mentor_id',
        'kategori_id',
        'judul',
        'deskripsi',
        'tanggal',
        'jam',
        'status',
        'catatan_mentor',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function mentorProfil()
    {
        return $this->belongsTo(MentorProfil::class, 'mentor_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriKeahlian::class, 'kategori_id');
    }

    public function sesiMentorings()
    {
        return $this->hasMany(SesiMentoring::class, 'pengajuan_id');
    }

    public function ulasans()
    {
        return $this->hasMany(Ulasan::class, 'pengajuan_id');
    }
}
