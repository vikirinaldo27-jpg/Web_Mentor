<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MahasiswaProfil extends Model
{
    protected $table = 'profil_mahasiswa';

    protected $fillable = [
        'user_id',
        'universitas',
        'jurusan',
        'semester',
        'no_hp',
        'foto',
        'biodata',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
