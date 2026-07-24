<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keahlian extends Model
{
    protected $table = 'keahlian';

    protected $fillable = [
        'mentor_id',
        'kategori_id',
        'tingkat_keahlian',
        'deskripsi',
    ];

    public function mentorProfil()
    {
        return $this->belongsTo(MentorProfil::class, 'mentor_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriKeahlian::class, 'kategori_id');
    }
}
