<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $name
 * @property string $category
 * @property string $locale
 * @property string $subject
 * @property string $body_html
 * @property string|null $body_text
 * @property array<array-key, mixed>|null $variables
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static Builder<static>|EmailTemplate active()
 * @method static Builder<static>|EmailTemplate forCategory(string $category)
 * @method static Builder<static>|EmailTemplate forLocale(string $locale)
 * @method static Builder<static>|EmailTemplate newModelQuery()
 * @method static Builder<static>|EmailTemplate newQuery()
 * @method static Builder<static>|EmailTemplate query()
 * @method static Builder<static>|EmailTemplate whereBodyHtml($value)
 * @method static Builder<static>|EmailTemplate whereBodyText($value)
 * @method static Builder<static>|EmailTemplate whereCategory($value)
 * @method static Builder<static>|EmailTemplate whereCreatedAt($value)
 * @method static Builder<static>|EmailTemplate whereId($value)
 * @method static Builder<static>|EmailTemplate whereIsActive($value)
 * @method static Builder<static>|EmailTemplate whereLocale($value)
 * @method static Builder<static>|EmailTemplate whereName($value)
 * @method static Builder<static>|EmailTemplate whereSubject($value)
 * @method static Builder<static>|EmailTemplate whereUpdatedAt($value)
 * @method static Builder<static>|EmailTemplate whereVariables($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @mixin \Eloquent
 */
class EmailTemplate extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\EmailTemplateFactory> */
    use AuditableTrait;

    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'category',
        'locale',
        'subject',
        'body_html',
        'body_text',
        'variables',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'category',
                'locale',
                'subject',
                'is_active',
            ])
            ->logOnlyDirty()
            ->useLogName('email_template')
            ->setDescriptionForEvent(fn (string $eventName) => "Email template {$eventName}");
    }

    /**
     * @param  Builder<EmailTemplate>  $query
     * @return Builder<EmailTemplate>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<EmailTemplate>  $query
     * @return Builder<EmailTemplate>
     */
    public function scopeForCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * @param  Builder<EmailTemplate>  $query
     * @return Builder<EmailTemplate>
     */
    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }

    public function renderSubject(array $data = []): string
    {
        return $this->replaceVariables($this->subject, $data);
    }

    public function renderBody(array $data = []): string
    {
        return $this->replaceVariables($this->body_html, $data);
    }

    private function replaceVariables(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace("{{{$key}}}", (string) $value, $content);
        }

        return $content;
    }
}
