<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\HelpdeskTicket;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class QueryHelpdeskTicketsTool extends Tool
{
    protected string $description = 'Query helpdesk tickets with optional status filter and limit';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'status' => 'nullable|in:open,in_progress,resolved,closed',
            'limit' => 'integer|min:1|max:50',
        ]);

        $tickets = HelpdeskTicket::query()
            ->with(['user', 'category'])
            ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->limit($validated['limit'] ?? 10)
            ->latest()
            ->get(['id', 'subject', 'status', 'priority', 'created_at'])
            ->map(fn ($t) => [
                'id' => $t->id,
                'title' => $t->subject,
                'status' => $t->status,
                'priority' => $t->priority,
                'created_at' => $t->created_at->format('Y-m-d H:i'),
            ]);

        return Response::text(json_encode($tickets, JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()
                ->enum(['open', 'in_progress', 'resolved', 'closed'])
                ->description('Filter tickets by status'),
            'limit' => $schema->integer()
                ->description('Maximum tickets to return (1-50)')
                ->default(10),
        ];
    }
}
