<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'email',
        'kata_sandi',
        'peran',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'kata_sandi' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    public function mahasiswaProfil()
    {
        return $this->hasOne(MahasiswaProfil::class);
    }

    public function mentorProfil()
    {
        return $this->hasOne(MentorProfil::class);
    }

    public function pengajuanSebagaiMahasiswa()
    {
        return $this->hasMany(PengajuanMentoring::class, 'mahasiswa_id');
    }

    public function ulasanSebagaiMahasiswa()
    {
        return $this->hasMany(Ulasan::class, 'mahasiswa_id');
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }
}
