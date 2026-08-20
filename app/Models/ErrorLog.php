<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    use HasFactory;

    protected $table = 'error_logs';

    protected $fillable = [
        'user_id',
        'user_details',
        'exception_type',
        'message',
        'file',
        'line',
        'url',
        'method',
        'request_details',
        'stack_trace',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'user_details' => 'array',
        'request_details' => 'array',
        'line' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
