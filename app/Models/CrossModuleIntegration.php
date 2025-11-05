<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrossModuleIntegration extends Model
{
    protected $fillable = [
        'helpdesk_ticket_id',
        'loan_application_id',
        'integration_type',
        'trigger_event',
        'integration_data',
        'processed_at',
        'processed_by',
    ];
}