<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Email Template Version Model
 *
 * Stores historical versions of email templates for audit and rollback purposes.
 *
 * @property int $id
 * @property int $email_template_id
 * @property int $version_number
 * @property string $subject
 * @property string $body_html
 * @property string|null $body_text
 * @property array<array-key, mixed>|null $variables
 * @property string|null $change_summary
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read EmailTemplate $template
 * @property-read User|null $creator
 *
 * @method static Builder<static>|EmailTemplateVersion forTemplate(int $templateId)
 * @method static Builder<static>|EmailTemplateVersion newModelQuery()
 * @method static Builder<static>|EmailTemplateVersion newQuery()
 * @method static Builder<static>|EmailTemplateVersion query()
 *
 * @mixin \Eloquent
 */
class EmailTemplateVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_template_id',
        'version_number',
        'subject',
        'body_html',
        'body_text',
        'variables',
        'change_summary',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'version_number' => 'integer',
        ];
    }

    /**
     * Get the parent email template.
     *
     * @return BelongsTo<EmailTemplate, $this>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }

    /**
     * Get the user who created this version.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter versions by template.
     *
     * @param  Builder<EmailTemplateVersion>  $query
     * @return Builder<EmailTemplateVersion>
     */
    public function scopeForTemplate(Builder $query, int $templateId): Builder
    {
        return $query->where('email_template_id', $templateId);
    }

    /**
     * Render the subject with variable substitution.
     *
     * @param  array<string, mixed>  $data
     */
    public function renderSubject(array $data = []): string
    {
        return $this->replaceVariables($this->subject, $data);
    }

    /**
     * Render the body with variable substitution.
     *
     * @param  array<string, mixed>  $data
     */
    public function renderBody(array $data = []): string
    {
        return $this->replaceVariables($this->body_html, $data);
    }

    /**
     * Replace template variables with actual values.
     *
     * @param  array<string, mixed>  $data
     */
    private function replaceVariables(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace("{{{$key}}}", (string) $value, $content);
        }

        return $content;
    }
}
