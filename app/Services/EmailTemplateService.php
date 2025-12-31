<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmailTemplateService
{
    public function getTemplate(string $category, string $locale = 'ms'): ?EmailTemplate
    {
        return Cache::remember(
            "email_template_{$category}_{$locale}",
            now()->addHours(1),
            fn () => EmailTemplate::active()
                ->forCategory($category)
                ->forLocale($locale)
                ->first()
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function renderTemplate(string $category, array $data, string $locale = 'ms'): array
    {
        $template = $this->getTemplate($category, $locale);

        if (! $template) {
            Log::warning('Email template not found', [
                'category' => $category,
                'locale' => $locale,
            ]);

            return [
                'subject' => 'ICTServe Notification',
                'body_html' => '<p>Template not found</p>',
                'body_text' => 'Template not found',
            ];
        }

        return [
            'subject' => $template->renderSubject($data),
            'body_html' => $template->renderBody($data),
            'body_text' => strip_tags($template->renderBody($data)),
        ];
    }

    /**
     * @param  array<string, mixed>  $sampleData
     * @return array<string, mixed>
     */
    public function previewTemplate(EmailTemplate $template, array $sampleData = []): array
    {
        $defaultData = $this->getDefaultSampleData($template->category);
        $data = array_merge($defaultData, $sampleData);

        return [
            'subject' => $template->renderSubject($data),
            'body_html' => $template->renderBody($data),
            'body_text' => strip_tags($template->renderBody($data)),
            'sample_data' => $data,
        ];
    }

    /**
     * Create a new version of an email template.
     */
    public function createVersion(
        EmailTemplate $template,
        string $subject,
        string $bodyHtml,
        ?string $bodyText = null,
        ?array $variables = null,
        ?string $changeSummary = null
    ): EmailTemplateVersion {
        return DB::transaction(function () use ($template, $subject, $bodyHtml, $bodyText, $variables, $changeSummary) {
            $nextVersion = $template->current_version + 1;

            $version = EmailTemplateVersion::create([
                'email_template_id' => $template->id,
                'version_number' => $nextVersion,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'body_text' => $bodyText ?? strip_tags($bodyHtml),
                'variables' => $variables ?? $template->variables,
                'change_summary' => $changeSummary,
                'created_by' => Auth::id(),
            ]);

            $template->update([
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'body_text' => $bodyText ?? strip_tags($bodyHtml),
                'variables' => $variables ?? $template->variables,
                'current_version' => $nextVersion,
                'updated_by' => Auth::id(),
            ]);

            $this->clearTemplateCache($template->category, $template->locale);

            Log::info('Email template version created', [
                'template_id' => $template->id,
                'version' => $nextVersion,
                'user_id' => Auth::id(),
            ]);

            return $version;
        });
    }

    /**
     * Restore a previous version of an email template.
     */
    public function restoreVersion(EmailTemplate $template, int $versionNumber): bool
    {
        $version = $template->getVersion($versionNumber);

        if (! $version) {
            Log::warning('Email template version not found for restore', [
                'template_id' => $template->id,
                'version' => $versionNumber,
            ]);

            return false;
        }

        $this->createVersion(
            $template,
            $version->subject,
            $version->body_html,
            $version->body_text,
            $version->variables,
            "Restored from version {$versionNumber}"
        );

        return true;
    }

    /**
     * Compare two versions of an email template.
     *
     * @return array<string, mixed>
     */
    public function compareVersions(EmailTemplate $template, int $version1, int $version2): array
    {
        $v1 = $template->getVersion($version1);
        $v2 = $template->getVersion($version2);

        if (! $v1 || ! $v2) {
            return ['error' => 'One or both versions not found'];
        }

        return [
            'version1' => [
                'number' => $v1->version_number,
                'subject' => $v1->subject,
                'body_html' => $v1->body_html,
                'created_at' => $v1->created_at?->toDateTimeString(),
                'created_by' => $v1->creator?->name,
            ],
            'version2' => [
                'number' => $v2->version_number,
                'subject' => $v2->subject,
                'body_html' => $v2->body_html,
                'created_at' => $v2->created_at?->toDateTimeString(),
                'created_by' => $v2->creator?->name,
            ],
            'subject_changed' => $v1->subject !== $v2->subject,
            'body_changed' => $v1->body_html !== $v2->body_html,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getAvailableVariables(string $category): array
    {
        $variables = [
            'ticket_confirmation' => [
                'ticket_number' => 'Ticket number (e.g., TKT-2025-001)',
                'applicant_name' => 'Name of person who submitted ticket',
                'title' => 'Ticket title/subject',
                'description' => 'Ticket description',
                'priority' => 'Ticket priority (urgent, high, medium, low)',
                'category' => 'Ticket category',
                'created_at' => 'Submission date and time',
                'sla_deadline' => 'SLA deadline for resolution',
            ],
            'loan_approval' => [
                'application_number' => 'Loan application number',
                'applicant_name' => 'Name of loan applicant',
                'asset_name' => 'Name of requested asset',
                'loan_start_date' => 'Loan start date',
                'loan_end_date' => 'Expected return date',
                'approver_name' => 'Name of approving authority',
                'approval_date' => 'Date of approval',
            ],
            'status_update' => [
                'item_number' => 'Ticket or application number',
                'old_status' => 'Previous status',
                'new_status' => 'New status',
                'updated_by' => 'Person who updated status',
                'update_date' => 'Date of status update',
                'comments' => 'Additional comments',
            ],
            'reminder' => [
                'item_number' => 'Ticket or application number',
                'item_type' => 'Type (ticket/loan)',
                'due_date' => 'Due date',
                'days_overdue' => 'Number of days overdue',
                'action_required' => 'Required action',
            ],
            'sla_breach' => [
                'ticket_number' => 'Ticket number',
                'sla_deadline' => 'Original SLA deadline',
                'breach_time' => 'Time of SLA breach',
                'assigned_to' => 'Currently assigned staff member',
                'escalation_level' => 'Escalation level',
            ],
        ];

        return $variables[$category] ?? [];
    }

    /**
     * Validate template content and syntax.
     *
     * @param  array<string>  $requiredVariables
     * @return array<string, mixed>
     */
    public function validateTemplate(
        string $subject,
        string $bodyHtml,
        array $requiredVariables = []
    ): array {
        $errors = [];
        $warnings = [];

        // Basic validation
        if (empty(trim($subject))) {
            $errors[] = 'Subject is required';
        }

        if (empty(trim($bodyHtml))) {
            $errors[] = 'Email body is required';
        }

        // HTML validation
        if (! empty($bodyHtml)) {
            $dom = new \DOMDocument;
            libxml_use_internal_errors(true);

            if (! $dom->loadHTML($bodyHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) {
                $errors[] = 'Invalid HTML structure';
            }

            $htmlErrors = libxml_get_errors();
            foreach ($htmlErrors as $error) {
                if ($error->level === LIBXML_ERR_ERROR || $error->level === LIBXML_ERR_FATAL) {
                    $warnings[] = "HTML warning: {$error->message}";
                }
            }
            libxml_clear_errors();
        }

        // Variable validation
        $foundVariables = $this->extractVariables($subject.' '.$bodyHtml);
        $missingVariables = array_diff($requiredVariables, $foundVariables);

        if (! empty($missingVariables)) {
            $errors[] = 'Missing required variables: '.implode(', ', $missingVariables);
        }

        // Check for unclosed variable tags
        if (preg_match('/\{\{[^}]*$/', $subject.$bodyHtml)) {
            $errors[] = 'Unclosed variable tag detected';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'found_variables' => $foundVariables,
        ];
    }

    /**
     * Extract variable names from template content.
     *
     * @return array<string>
     */
    public function extractVariables(string $content): array
    {
        preg_match_all('/\{\{(\w+)\}\}/', $content, $matches);

        return array_unique($matches[1] ?? []);
    }

    public function clearTemplateCache(?string $category = null, ?string $locale = null): void
    {
        if ($category && $locale) {
            Cache::forget("email_template_{$category}_{$locale}");
        } else {
            $categories = ['ticket_confirmation', 'loan_approval', 'status_update', 'reminder', 'sla_breach'];
            $locales = ['ms', 'en'];

            foreach ($categories as $cat) {
                foreach ($locales as $loc) {
                    Cache::forget("email_template_{$cat}_{$loc}");
                }
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getDefaultSampleData(string $category): array
    {
        $sampleData = [
            'ticket_confirmation' => [
                'ticket_number' => 'TKT-2025-001',
                'applicant_name' => 'Ahmad bin Ali',
                'title' => 'Laptop tidak boleh boot',
                'description' => 'Laptop Dell tidak dapat dihidupkan selepas update Windows.',
                'priority' => 'high',
                'category' => 'hardware',
                'created_at' => now()->format('d/m/Y H:i'),
                'sla_deadline' => now()->addHours(24)->format('d/m/Y H:i'),
            ],
            'loan_approval' => [
                'application_number' => 'LN-2025-001',
                'applicant_name' => 'Siti Nurhaliza',
                'asset_name' => 'Projector Epson EB-X41',
                'loan_start_date' => now()->format('d/m/Y'),
                'loan_end_date' => now()->addDays(7)->format('d/m/Y'),
                'approver_name' => 'Encik Rahman',
                'approval_date' => now()->format('d/m/Y H:i'),
            ],
            'status_update' => [
                'item_number' => 'TKT-2025-001',
                'old_status' => 'open',
                'new_status' => 'in_progress',
                'updated_by' => 'Teknisi ICT',
                'update_date' => now()->format('d/m/Y H:i'),
                'comments' => 'Sedang memeriksa hardware laptop.',
            ],
            'reminder' => [
                'item_number' => 'LN-2025-001',
                'item_type' => 'loan',
                'due_date' => now()->subDays(1)->format('d/m/Y'),
                'days_overdue' => '1',
                'action_required' => 'Sila pulangkan aset yang dipinjam',
            ],
            'sla_breach' => [
                'ticket_number' => 'TKT-2025-001',
                'sla_deadline' => now()->subHours(2)->format('d/m/Y H:i'),
                'breach_time' => now()->format('d/m/Y H:i'),
                'assigned_to' => 'Teknisi ICT',
                'escalation_level' => 'Level 1',
            ],
        ];

        return $sampleData[$category] ?? [];
    }
}
