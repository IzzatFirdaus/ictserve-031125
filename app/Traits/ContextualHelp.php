<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Contextual Help Trait
 *
 * Implements MyGovEA "Panduan dan Dokumentasi" (Guidance and Documentation)
 * design principles per Requirement 23.4.
 *
 * Features:
 * - Tooltips for complex form fields
 * - FAQ section accessible from footer
 * - User manual links
 * - Inline help text
 * - Video tutorials (future)
 *
 * @see Requirements 23.4
 *
 * @trace MyGovEA §18, D12 §7
 */
trait ContextualHelp
{
    /**
     * Add tooltip to a form field
     *
     * @param  string  $fieldName  Field identifier
     * @param  string  $helpText  Help text content
     * @param  string  $position  Tooltip position (top, right, bottom, left)
     * @return array Tooltip configuration
     */
    protected function addTooltip(string $fieldName, string $helpText, string $position = 'right'): array
    {
        return [
            'field' => $fieldName,
            'help' => $helpText,
            'position' => $position,
            'icon' => 'heroicon-o-question-mark-circle',
            'type' => 'tooltip',
        ];
    }

    /**
     * Add inline help text below a field
     *
     * @param  string  $fieldName  Field identifier
     * @param  string  $helpText  Help text content
     * @return array Inline help configuration
     */
    protected function addInlineHelp(string $fieldName, string $helpText): array
    {
        return [
            'field' => $fieldName,
            'help' => $helpText,
            'type' => 'inline',
            'class' => 'text-sm text-gray-600 mt-1',
        ];
    }

    /**
     * Get FAQ items for a specific section
     *
     * @param  string  $section  Section identifier (helpdesk, loan, general)
     * @return array FAQ items
     */
    protected function getFAQItems(string $section): array
    {
        $faqs = [
            'helpdesk' => [
                [
                    'question' => __('faq.helpdesk.how_to_submit'),
                    'answer' => __('faq.helpdesk.how_to_submit_answer'),
                ],
                [
                    'question' => __('faq.helpdesk.how_to_track'),
                    'answer' => __('faq.helpdesk.how_to_track_answer'),
                ],
                [
                    'question' => __('faq.helpdesk.response_time'),
                    'answer' => __('faq.helpdesk.response_time_answer'),
                ],
            ],
            'loan' => [
                [
                    'question' => __('faq.loan.how_to_apply'),
                    'answer' => __('faq.loan.how_to_apply_answer'),
                ],
                [
                    'question' => __('faq.loan.approval_process'),
                    'answer' => __('faq.loan.approval_process_answer'),
                ],
                [
                    'question' => __('faq.loan.return_process'),
                    'answer' => __('faq.loan.return_process_answer'),
                ],
            ],
            'general' => [
                [
                    'question' => __('faq.general.account_registration'),
                    'answer' => __('faq.general.account_registration_answer'),
                ],
                [
                    'question' => __('faq.general.forgot_password'),
                    'answer' => __('faq.general.forgot_password_answer'),
                ],
            ],
        ];

        return $faqs[$section] ?? [];
    }

    /**
     * Get user manual link for a specific topic
     *
     * @param  string  $topic  Topic identifier
     * @return array Manual link configuration
     */
    protected function getUserManualLink(string $topic): array
    {
        $manualLinks = [
            'helpdesk_submission' => [
                'title' => __('help.manual.helpdesk_submission'),
                'url' => route('help.manual', ['section' => 'helpdesk-submission']),
                'icon' => 'heroicon-o-document-text',
            ],
            'loan_application' => [
                'title' => __('help.manual.loan_application'),
                'url' => route('help.manual', ['section' => 'loan-application']),
                'icon' => 'heroicon-o-document-text',
            ],
            'account_management' => [
                'title' => __('help.manual.account_management'),
                'url' => route('help.manual', ['section' => 'account-management']),
                'icon' => 'heroicon-o-document-text',
            ],
        ];

        return $manualLinks[$topic] ?? [
            'title' => __('help.manual.general'),
            'url' => route('help.manual'),
            'icon' => 'heroicon-o-document-text',
        ];
    }

    /**
     * Show contextual help modal
     *
     * @param  string  $topic  Help topic
     * @param  string  $content  Help content
     */
    protected function showHelpModal(string $topic, string $content): void
    {
        $this->dispatch('show-help-modal', [
            'topic' => $topic,
            'content' => $content,
            'icon' => 'heroicon-o-information-circle',
        ]);
    }

    /**
     * Get help resources for footer
     *
     * @return array Help resources
     */
    protected function getFooterHelpResources(): array
    {
        return [
            [
                'label' => __('help.faq'),
                'url' => route('help.faq'),
                'icon' => 'heroicon-o-question-mark-circle',
            ],
            [
                'label' => __('help.user_manual'),
                'url' => route('help.manual'),
                'icon' => 'heroicon-o-document-text',
            ],
            [
                'label' => __('help.contact_support'),
                'url' => route('help.contact'),
                'icon' => 'heroicon-o-envelope',
            ],
        ];
    }
}
