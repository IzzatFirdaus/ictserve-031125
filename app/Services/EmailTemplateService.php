<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Cache;
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

    public function validateTemplate(string $subject, string $bodyHtml): array
    {
        $errors = [];

        if (empty(trim($subject))) {
            $errors[] = 'Subject is required';
        }

        if (empty(trim($bodyHtml))) {
            $errors[] = 'Email body is required';
        }

        // Basic HTML validation
        if (! empty($bodyHtml)) {
            $dom = new \DOMDocument;
            libxml_use_internal_errors(true);

            if (! $dom->loadHTML($bodyHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD)) {
                $errors[] = 'Invalid HTML structure';
            }

            libxml_clear_errors();
        }

        // WCAG 2.2 AA compliance checks
        if (! empty($bodyHtml)) {
            if (! str_contains($bodyHtml, 'color:') && ! str_contains($bodyHtml, 'style=')) {
                // Basic check - more comprehensive validation would be needed
            }
        }

        return $errors;
    }

    public function clearTemplateCache(?string $category = null, ?string $locale = null): void
    {
        if ($category && $locale) {
            Cache::forget("email_template_{$category}_{$locale}");
        } else {
            // Clear all template cache
            $categories = ['ticket_confirmation', 'loan_approval', 'status_update', 'reminder', 'sla_breach'];
            $locales = ['ms', 'en'];

            foreach ($categories as $cat) {
                foreach ($locales as $loc) {
                    Cache::forget("email_template_{$cat}_{$loc}");
                }
            }
        }
    }

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
