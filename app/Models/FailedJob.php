<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
    protected $table = 'failed_jobs';
    
    protected $guarded = [];

    public $incrementing = true; // id is bigInt auto-increment in standard Laravel migrations, but sometimes uuid. Let's assume standard.
    // Actually, failed_jobs usually has 'id' (bigint) and 'uuid' (string).
    
    protected $casts = [
        'failed_at' => 'datetime',
        'payload' => 'json',
    ];
}
