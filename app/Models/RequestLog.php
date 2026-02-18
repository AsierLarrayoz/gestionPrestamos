<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    protected $table = 'request_logs';

    // Para que Laravel trate los campos JSON como arrays automáticamente
    protected $casts = [
        'payload' => 'array',
        'session_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
