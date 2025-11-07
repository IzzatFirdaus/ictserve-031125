<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\WorkflowRule;
use Illuminate\Support\Facades\Log;

class WorkflowAutomationService
{
    public function executeRules(string $module, string $event, $entity): void
    {
        $rules = WorkflowRule::active()
            ->forModule($module)
            ->byPriority()
            ->get();

        foreach ($rules as $rule) {
            if ($this->evaluateConditions($rule->conditions, $entity, $event)) {
                $this->executeActions($rule->actions, $entity);

                Log::info('Workflow rule executed', [
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'entity_type' => get_class($entity),
                    'entity_id' => $entity->id,
                ]);
            }
        }
    }

    public function testRule(WorkflowRule $rule, array $sampleData): array
    {
        $results = [];

        foreach ($sampleData as $data) {
            $conditionResult = $this->evaluateConditions($rule->conditions, (object) $data, 'test');
            $results[] = [
                'data' => $data,
                'condition_result' => $conditionResult,
                'actions_would_execute' => $conditionResult ? $rule->actions : [],
            ];
        }

        return $results;
    }

    public function getAvailableConditions(string $module): array
    {
        $conditions = [
            'helpdesk' => [
                'priority' => ['urgent', 'high', 'medium', 'low'],
                'status' => ['open', 'assigned', 'in_progress', 'resolved', 'closed'],
                'category' => ['hardware', 'software', 'network', 'other'],
                'created_hours_ago' => 'number',
                'assigned_to' => 'user_id',
            ],
            'loans' => [
                'status' => ['pending', 'approved', 'rejected', 'issued', 'returned'],
                'asset_value' => 'number',
                'loan_duration_days' => 'number',
                'applicant_grade' => 'number',
            ],
            'assets' => [
                'status' => ['available', 'on_loan', 'maintenance', 'retired'],
                'condition' => ['excellent', 'good', 'fair', 'poor', 'damaged'],
                'category' => ['laptop', 'desktop', 'monitor', 'printer', 'other'],
            ],
        ];

        return $conditions[$module] ?? [];
    }

    public function getAvailableActions(string $module): array
    {
        return [
            'send_email' => [
                'type' => 'email',
                'fields' => ['recipient', 'template', 'subject', 'body'],
            ],
            'update_status' => [
                'type' => 'status_update',
                'fields' => ['new_status'],
            ],
            'assign_user' => [
                'type' => 'assignment',
                'fields' => ['user_id'],
            ],
            'create_notification' => [
                'type' => 'notification',
                'fields' => ['message', 'type', 'recipients'],
            ],
        ];
    }

    private function evaluateConditions(array $conditions, $entity, string $event): bool
    {
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? '';
            $operator = $condition['operator'] ?? '=';
            $value = $condition['value'] ?? '';

            $entityValue = $this->getEntityValue($entity, $field);

            if (! $this->compareValues($entityValue, $operator, $value)) {
                return false;
            }
        }

        return true;
    }

    private function executeActions(array $actions, $entity): void
    {
        foreach ($actions as $action) {
            $type = $action['type'] ?? '';

            switch ($type) {
                case 'send_email':
                    $this->sendEmail($action, $entity);
                    break;
                case 'update_status':
                    $this->updateStatus($action, $entity);
                    break;
                case 'assign_user':
                    $this->assignUser($action, $entity);
                    break;
                case 'create_notification':
                    $this->createNotification($action, $entity);
                    break;
            }
        }
    }

    private function getEntityValue($entity, string $field)
    {
        if (is_object($entity)) {
            return $entity->{$field} ?? null;
        }

        if (is_array($entity)) {
            return $entity[$field] ?? null;
        }

        return null;
    }

    private function compareValues($entityValue, string $operator, $expectedValue): bool
    {
        return match ($operator) {
            '=' => $entityValue == $expectedValue,
            '!=' => $entityValue != $expectedValue,
            '>' => $entityValue > $expectedValue,
            '<' => $entityValue < $expectedValue,
            '>=' => $entityValue >= $expectedValue,
            '<=' => $entityValue <= $expectedValue,
            'contains' => str_contains((string) $entityValue, (string) $expectedValue),
            'in' => in_array($entityValue, (array) $expectedValue),
            default => false,
        };
    }

    private function sendEmail(array $action, $entity): void
    {
        // Email sending logic would be implemented here
        Log::info('Workflow email action executed', [
            'action' => $action,
            'entity_id' => $entity->id ?? null,
        ]);
    }

    private function updateStatus(array $action, $entity): void
    {
        if (method_exists($entity, 'update')) {
            $entity->update(['status' => $action['new_status']]);
        }
    }

    private function assignUser(array $action, $entity): void
    {
        if (method_exists($entity, 'update') && isset($action['user_id'])) {
            $entity->update(['assigned_to' => $action['user_id']]);
        }
    }

    private function createNotification(array $action, $entity): void
    {
        // Notification creation logic would be implemented here
        Log::info('Workflow notification action executed', [
            'action' => $action,
            'entity_id' => $entity->id ?? null,
        ]);
    }
}
