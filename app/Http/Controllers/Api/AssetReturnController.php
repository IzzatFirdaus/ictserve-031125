<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetReturnController extends Controller
{
    /**
     * Notify about damaged asset return and create maintenance ticket
     */
    public function notifyDamage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => 'required|exists:assets,id',
            'loan_application_id' => 'required|exists:loan_applications,id',
            'damage_description' => 'required|string|max:1000',
            'damage_type' => 'required|string|max:255',
            'severity' => 'nullable|in:minor,moderate,severe,critical',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            /** @var Asset $asset */
            $asset = Asset::findOrFail($request->asset_id);
            /** @var LoanApplication $loanApplication */
            $loanApplication = LoanApplication::findOrFail($request->loan_application_id);

            // Create maintenance ticket
            $ticket = HelpdeskTicket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'title' => "Asset Damage Report: {$asset->name}",
                'description' => $request->damage_description,
                'category' => 'maintenance',
                'priority' => $this->mapSeverityToPriority($request->severity ?? 'moderate'),
                'status' => 'open',
                'damage_type' => $request->damage_type,
                'asset_id' => $asset->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Damage notification processed successfully',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'asset_id' => $asset->id,
                    'loan_application_id' => $loanApplication->id,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process damage notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create maintenance ticket for asset
     */
    public function createMaintenanceTicket(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => 'required|exists:assets,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'priority' => 'nullable|in:low,medium,high,critical',
            'damage_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            /** @var Asset $asset */
            $asset = Asset::findOrFail($request->asset_id);

            $ticket = HelpdeskTicket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'title' => $request->title,
                'description' => $request->description,
                'category' => 'maintenance',
                'priority' => $request->priority ?? 'medium',
                'status' => 'open',
                'damage_type' => $request->damage_type,
                'asset_id' => $asset->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Maintenance ticket created successfully',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'asset_id' => $asset->id,
                    'priority' => $ticket->priority,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create maintenance ticket',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate unique ticket number
     */
    private function generateTicketNumber(): string
    {
        $year = now()->year;
        $lastTicket = HelpdeskTicket::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTicket ? ((int) substr($lastTicket->ticket_number, -6)) + 1 : 1;

        return sprintf('HD%d%06d', $year, $sequence);
    }

    /**
     * Map severity to priority
     */
    private function mapSeverityToPriority(string $severity): string
    {
        return match ($severity) {
            'critical' => 'critical',
            'severe' => 'high',
            'moderate' => 'medium',
            'minor' => 'low',
            default => 'medium',
        };
    }
}
