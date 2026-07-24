<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $fillable = [
        'mentor_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'tersedia',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tersedia' => 'boolean',
        ];
    }

    public function mentorProfil()
    {
        return $this->belongsTo(MentorProfil::class, 'mentor_id');
    }
}
