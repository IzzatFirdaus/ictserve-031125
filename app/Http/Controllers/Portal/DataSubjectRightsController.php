<?php

declare(strict_types=1);

// name: DataSubjectRightsController
// description: Controller for PDPA data subject rights (access, correction, deletion)
// author: dev-team@motac.gov.my
// trace: D03 SRS-NFR-005, D09 §9, D11 §10 (Requirements 14.4)
// last-updated: 2025-11-06

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\DataComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DataSubjectRightsController extends Controller
{
    public function __construct(
        private DataComplianceService $complianceService
    ) {}

    /**
     * Display data subject rights information page
     */
    public function index()
    {
        return view('portal.data-rights.index');
    }

    /**
     * Export user's personal data (PDPA Right to Access)
     */
    public function exportData(Request $request)
    {
        $user = Auth::user();

        // Log the data export request
        $this->complianceService->recordConsent($user, 'data_export_requested', true);

        // Generate data export
        $data = $this->complianceService->exportUserData($user);

        // Create JSON file
        $filename = "user_data_export_{$user->id}_".now()->format('Y-m-d_His').'.json';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Store temporarily (will be deleted after download)
        Storage::disk('local')->put("exports/{$filename}", $json);

        // Return download response
        return response()->download(
            storage_path("app/exports/{$filename}"),
            $filename,
            [
                'Content-Type' => 'application/json',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]
        )->deleteFileAfterSend(true);
    }

    /**
     * Request data correction
     */
    public function requestCorrection(Request $request)
    {
        $validated = $request->validate([
            'field' => 'required|string|in:name,phone,email',
            'current_value' => 'required|string|max:255',
            'requested_value' => 'required|string|max:255',
            'reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        // Log the correction request
        $this->complianceService->recordConsent($user, 'data_correction_requested', true);

        // Create a portal activity for the correction request
        \App\Models\PortalActivity::create([
            'user_id' => $user->id,
            'action' => 'data_correction_requested',
            'subject_type' => null,
            'subject_id' => null,
            'subject_title' => 'Data Correction Request',
            'metadata' => [
                'field' => $validated['field'],
                'current_value' => $validated['current_value'],
                'requested_value' => $validated['requested_value'],
                'reason' => $validated['reason'],
                'status' => 'pending',
                'timestamp' => now()->toIso8601String(),
            ],
        ]);

        // TODO: Send notification to admin for review

        return redirect()->back()->with('success', __('portal.data_rights.correction_requested'));
    }

    /**
     * Request data deletion (PDPA Right to Erasure)
     */
    public function requestDeletion(Request $request)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'confirmation' => 'required|accepted',
        ]);

        $user = Auth::user();

        // Log the deletion request
        $this->complianceService->recordConsent($user, 'data_deletion_requested', true);

        // Create a portal activity for the deletion request
        \App\Models\PortalActivity::create([
            'user_id' => $user->id,
            'action' => 'data_deletion_requested',
            'subject_type' => null,
            'subject_id' => null,
            'subject_title' => 'Data Deletion Request',
            'metadata' => [
                'reason' => $validated['reason'],
                'status' => 'pending_review',
                'timestamp' => now()->toIso8601String(),
            ],
        ]);

        // TODO: Send notification to admin for review and approval

        return redirect()->back()->with('warning', __('portal.data_rights.deletion_requested'));
    }

    /**
     * View consent history
     */
    public function consentHistory()
    {
        $user = Auth::user();

        $consents = \App\Models\PortalActivity::where('user_id', $user->id)
            ->where('action', 'consent_recorded')
            ->latest()
            ->paginate(20);

        return view('portal.data-rights.consent-history', compact('consents'));
    }

    /**
     * Update consent preferences
     */
    public function updateConsent(Request $request)
    {
        $validated = $request->validate([
            'consent_type' => 'required|string|in:data_processing,marketing,analytics',
            'granted' => 'required|boolean',
        ]);

        $user = Auth::user();

        $this->complianceService->recordConsent(
            $user,
            $validated['consent_type'],
            $validated['granted']
        );

        return redirect()->back()->with('success', __('portal.data_rights.consent_updated'));
    }
}
