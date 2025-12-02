<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class EmailTemplate extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\EmailTemplateFactory> */
    use AuditableTrait, HasFactory;

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
