<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chat';

    protected $fillable = [
        'pengajuan_id',
        'sender_id',
        'pesan',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanMentoring::class, 'pengajuan_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
