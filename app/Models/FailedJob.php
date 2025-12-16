<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $uuid
 * @property string $connection
 * @property string $queue
 * @property array<array-key, mixed> $payload
 * @property string $exception
 * @property \Illuminate\Support\Carbon $failed_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob whereConnection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob whereQueue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob whereUuid($value)
 * @mixin \Eloquent
 */
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
