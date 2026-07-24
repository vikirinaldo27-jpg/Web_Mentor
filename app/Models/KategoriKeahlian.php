<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKeahlian extends Model
{
    protected $table = 'kategori_keahlian';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'icon',
    ];

    public function keahlians()
    {
        return $this->hasMany(Keahlian::class, 'kategori_id');
    }

    public function mentorProfils()
    {
        return $this->belongsToMany(MentorProfil::class, 'keahlian', 'kategori_id', 'mentor_id')
            ->withPivot('tingkat_keahlian', 'deskripsi')
            ->withTimestamps();
    }

    public function pengajuanMentorings()
    {
        return $this->hasMany(PengajuanMentoring::class, 'kategori_id');
    }

    public function getJumlahMentorAttribute()
    {
        return $this->keahlians()->distinct('mentor_id')->count('mentor_id');
    }
}
