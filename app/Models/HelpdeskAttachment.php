<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class HelpdeskAttachment extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'helpdesk_ticket_id',
        'user_id',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'file_path',
        'disk',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function helpdeskTicket(): BelongsTo
    {
        return $this->belongsTo(HelpdeskTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
