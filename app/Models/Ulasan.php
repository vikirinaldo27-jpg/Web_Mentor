<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    protected $table = 'ulasan';

    protected $fillable = [
        'pengajuan_id',
        'mahasiswa_id',
        'mentor_id',
        'rating',
        'komentar',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanMentoring::class, 'pengajuan_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function mentorProfil()
    {
        return $this->belongsTo(MentorProfil::class, 'mentor_id');
    }
}
