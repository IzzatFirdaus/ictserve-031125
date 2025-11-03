<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class HelpdeskComment extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'helpdesk_ticket_id',
        'user_id',
        'commenter_name',
        'commenter_email',
        'comment',
        'is_internal',
        'is_resolution',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_resolution' => 'boolean',
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
