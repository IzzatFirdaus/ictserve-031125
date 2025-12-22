<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Minimalist Interface Trait
 *
 * Implements MyGovEA "Antara Muka Minimalis dan Mudah" (Minimalist Interface)
 * design principles per Requirement 23.2.
 *
 * Features:
 * - Remove unnecessary components
 * - Consistent navigation patterns
 * - Intuitive user flows
 * - Progressive disclosure
 * - Clean visual hierarchy
 *
 * @see Requirements 23.2
 *
 * @trace MyGovEA §5, D12 §3
 */
trait MinimalistInterface
{
    /**
     * Filter out unnecessary form fields based on context
     *
     * @param  array  $fields  All available fields
     * @param  array  $context  Current context (user type, step, etc.)
     * @return array Filtered essential fields only
     */
    

/**
 * @param array<string, mixed> $context
 */
protected function showEssentialFieldsOnly(array $fields, array $context): array
    {
        return array_filter($fields, function ($field) use ($context) {
            // Always show required fields
            if ($field['required'] ?? false) {
                return true;
            }

            // Show optional fields only if relevant to context
            if (isset($field['show_when'])) {
                return $this->evaluateCondition($field['show_when'], $context);
            }

            // Hide fields marked as advanced unless explicitly requested
            if ($field['advanced'] ?? false) {
                return $context['show_advanced'] ?? false;
            }

            return true;
        });
    }

    /**
     * Implement progressive disclosure - show details only when needed
     *
     * @param  string  $section  Section identifier
     * @param  bool  $isExpanded  Whether section is expanded
     * @return array Section configuration
     */
    protected function progressiveDisclosure(string $section, bool $isExpanded = false): array
    {
        return [
            'section' => $section,
            'expanded' => $isExpanded,
            'collapsible' => true,
            'icon' => $isExpanded ? 'heroicon-o-chevron-up' : 'heroicon-o-chevron-down',
        ];
    }

    /**
     * Simplify navigation by showing only relevant actions
     *
     * @param  array  $allActions  All available actions
     * @param  string  $userRole  Current user role
     * @param  string  $context  Current context
     * @return array Filtered actions
     */
    

/**
 * @param array<string, mixed> $allActions
 */
protected function showRelevantActionsOnly(array $allActions, string $userRole, string $context): array
    {
        return array_filter($allActions, function ($action) use ($userRole, $context) {
            // Check role permission
            if (isset($action['roles']) && ! in_array($userRole, $action['roles'])) {
                return false;
            }

            // Check context relevance
            if (isset($action['contexts']) && ! in_array($context, $action['contexts'])) {
                return false;
            }

            return true;
        });
    }

    /**
     * Maintain consistent navigation structure
     *
     * @return array Standard navigation structure
     */
    protected function getConsistentNavigation(): array
    {
        return [
            'primary' => [
                ['label' => __('common.home'), 'route' => 'home', 'icon' => 'heroicon-o-home'],
                ['label' => __('helpdesk.helpdesk'), 'route' => 'helpdesk.create', 'icon' => 'heroicon-o-ticket'],
                ['label' => __('loan.loans'), 'route' => 'loan.create', 'icon' => 'heroicon-o-cube'],
            ],
            'secondary' => [
                ['label' => __('common.dashboard'), 'route' => 'dashboard', 'icon' => 'heroicon-o-squares-2x2', 'auth_required' => true],
                ['label' => __('common.profile'), 'route' => 'profile', 'icon' => 'heroicon-o-user', 'auth_required' => true],
            ],
        ];
    }

    /**
     * Evaluate a condition against context
     *
     * @param  mixed  $condition  Condition to evaluate
     * @param  array  $context  Current context
     */
    

/**
 * @param array<string, mixed> $context
 */
protected function evaluateCondition(mixed $condition, array $context): bool
    {
        if (is_callable($condition)) {
            return $condition($context);
        }

        if (is_string($condition)) {
            return $context[$condition] ?? false;
        }

        return (bool) $condition;
    }
}
