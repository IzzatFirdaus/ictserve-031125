<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HelpdeskTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * API Ticket Controller
 *
 * Provides JSON API endpoints for helpdesk ticket management with Laravel Sanctum authentication.
 * Implements fine-grained permissions using token abilities (read:tickets, write:tickets, admin:all).
 *
 * @see D03 SRS-API-001 - API Authentication Requirements
 * @see Requirement 37.3 - API Routes and Controllers
 */
class ApiTicketController extends Controller
{
    /**
     * Get tickets for authenticated user
     *
     * Requires ability: read:tickets or admin:all
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => __('api.tickets.unauthorized'),
                'message_ms' => __('api.tickets.unauthorized', [], 'ms'),
                'data' => [],
            ], 401);
        }

        // Get tickets for authenticated user (both user_id and guest_email matches)
        $tickets = HelpdeskTicket::with(['user', 'division', 'category', 'assignedUser'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('guest_email', $user->email);
            })
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => __('api.tickets.index_success'),
            'message_ms' => __('api.tickets.index_success', [], 'ms'),
            'data' => $tickets,
        ], 200);
    }

    /**
     * Create a new ticket
     *
     * Requires ability: write:tickets or admin:all
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => __('api.tickets.unauthorized'),
                'message_ms' => __('api.tickets.unauthorized', [], 'ms'),
                'data' => null,
            ], 401);
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:helpdesk_categories,id'],
            'priority' => ['required', Rule::in(['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'guest_name' => ['nullable', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
            'guest_phone' => ['nullable', 'string', 'max:50'],
            'guest_division' => ['nullable', 'string', 'max:100'],
            'guest_grade' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('api.tickets.validation_error'),
                'message_ms' => __('api.tickets.validation_error', [], 'ms'),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Create ticket with authenticated user
            $ticketData = $validator->validated();
            $ticketData['user_id'] = $user->id;
            $ticketData['status'] = 'OPEN';

            // Auto-fill guest fields from authenticated user if not provided
            if (empty($ticketData['guest_name'])) {
                $ticketData['guest_name'] = $user->name;
            }
            if (empty($ticketData['guest_email'])) {
                $ticketData['guest_email'] = $user->email;
            }
            if (empty($ticketData['guest_phone'])) {
                $ticketData['guest_phone'] = $user->phone ?? null;
            }
            if (empty($ticketData['guest_division'])) {
                $ticketData['guest_division'] = $user->division ?? null;
            }
            if (empty($ticketData['guest_grade'])) {
                $ticketData['guest_grade'] = $user->grade ?? null;
            }

            $ticket = HelpdeskTicket::create($ticketData);

            // Load relationships for response
            $ticket->load(['user', 'division', 'category', 'assignedUser']);

            return response()->json([
                'success' => true,
                'message' => __('api.tickets.store_success'),
                'message_ms' => __('api.tickets.store_success', [], 'ms'),
                'data' => $ticket,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('api.tickets.store_error'),
                'message_ms' => __('api.tickets.store_error', [], 'ms'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
