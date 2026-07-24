<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorProfil extends Model
{
    protected $table = 'profil_mentor';

    protected $fillable = [
        'user_id',
        'gelar',
        'universitas',
        'tahun_lulus',
        'pekerjaan',
        'perusahaan',
        'pengalaman',
        'bio',
        'no_hp',
        'foto',
        'status_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'tahun_lulus' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function keahlians()
    {
        return $this->hasMany(Keahlian::class, 'mentor_id');
    }

    public function kategoriKeahlians()
    {
        return $this->belongsToMany(KategoriKeahlian::class, 'keahlian', 'mentor_id', 'kategori_id')
            ->withPivot('tingkat_keahlian', 'deskripsi')
            ->withTimestamps();
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'mentor_id');
    }

    public function pengajuanMasuk()
    {
        return $this->hasMany(PengajuanMentoring::class, 'mentor_id');
    }

    public function ulasanSebagaiMentor()
    {
        return $this->hasMany(Ulasan::class, 'mentor_id');
    }

    public function getRataRatingAttribute()
    {
        return $this->ulasanSebagaiMentor()->avg('rating');
    }

    public function getJumlahUlasanAttribute()
    {
        return $this->ulasanSebagaiMentor()->count();
    }
}
