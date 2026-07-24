<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiMentoring extends Model
{
    protected $table = 'sesi_mentoring';

    protected $fillable = [
        'pengajuan_id',
        'judul_sesi',
        'jadwal',
        'durasi_menit',
        'link_meeting',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jadwal' => 'datetime',
            'durasi_menit' => 'integer',
        ];
    }

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanMentoring::class, 'pengajuan_id');
    }
}
