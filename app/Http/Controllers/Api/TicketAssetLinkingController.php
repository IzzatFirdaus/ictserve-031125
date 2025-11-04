<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketAssetLinkingController extends Controller
{
    /**
     * Link a ticket to an asset (via asset_id field on ticket)
     */
    public function linkTicketToAsset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:helpdesk_tickets,id',
            'asset_id' => 'required|exists:assets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            /** @var HelpdeskTicket $ticket */
            $ticket = HelpdeskTicket::findOrFail($request->ticket_id);
            /** @var Asset $asset */
            $asset = Asset::findOrFail($request->asset_id);

            // Update ticket's asset_id field
            $ticket->asset_id = $asset->id;
            $ticket->save();

            return response()->json([
                'success' => true,
                'message' => 'Ticket linked to asset successfully',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'asset_id' => $asset->id,
                    'asset_name' => $asset->name,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to link ticket to asset',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unlink a ticket from an asset
     */
    public function unlinkTicketFromAsset(HelpdeskTicket $ticket): JsonResponse
    {
        try {
            $assetId = $ticket->asset_id;

            $ticket->asset_id = null;
            $ticket->save();

            return response()->json([
                'success' => true,
                'message' => 'Ticket unlinked from asset successfully',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'asset_id' => $assetId,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlink ticket from asset',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get asset linked to a ticket
     */
    public function getTicketAssets(HelpdeskTicket $ticket): JsonResponse
    {
        try {
            $asset = $ticket->relatedAsset;

            return response()->json([
                'success' => true,
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'asset' => $asset ? [
                        'asset_id' => $asset->id,
                        'asset_name' => $asset->name,
                        'asset_tag' => $asset->asset_tag,
                        'status' => $asset->status,
                    ] : null,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ticket asset',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all tickets linked to an asset
     */
    public function getAssetTickets(Asset $asset): JsonResponse
    {
        try {
            $tickets = HelpdeskTicket::where('asset_id', $asset->id)
                ->select('id', 'ticket_number', 'title', 'status', 'priority', 'created_at')
                ->get()
                ->map(function ($ticket) {
                    return [
                        'ticket_id' => $ticket->id,
                        'ticket_number' => $ticket->ticket_number,
                        'ticket_title' => $ticket->title,
                        'ticket_status' => $ticket->status,
                        'ticket_priority' => $ticket->priority,
                        'created_at' => $ticket->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'asset_id' => $asset->id,
                    'asset_name' => $asset->name,
                    'asset_tag' => $asset->asset_tag,
                    'tickets' => $tickets,
                    'total' => $tickets->count(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve asset tickets',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
