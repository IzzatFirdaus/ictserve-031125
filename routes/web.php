<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dev/components', function () {
    return view('dev.components');
})->name('dev.components');

// Temporary Verification Route
Route::get('/admin-login-verify', function () {
    return view('admin.admin_login_full');
});
// Public Information Pages (No Authentication Required)
Route::view('/accessibility', 'pages.accessibility')->name('accessibility');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/services', 'pages.services')->name('services');

// Language Switcher Route (No Authentication Required)
Route::get('/change-locale/{locale}', [App\Http\Controllers\LanguageController::class, 'change'])
    ->where('locale', 'en|ms')
    ->name('change-locale');

// Public Pages
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/contact', 'pages.contact')->name('contact');

// Guest Helpdesk Routes (No Authentication Required) - Livewire Based
Route::prefix('helpdesk')->name('helpdesk.')->middleware(['guest.ratelimit'])->group(function () {
    Route::get('/guest/create', App\Livewire\Helpdesk\GuestTicketForm::class)->name('guest.create');
    Route::get('/create', App\Livewire\Helpdesk\SubmitTicket::class)->name('create');
    Route::get('/submit', App\Livewire\Helpdesk\SubmitTicket::class)->name('submit');
    Route::get('/track/{ticketNumber?}', App\Livewire\Helpdesk\TrackTicket::class)->name('track');
    Route::get('/success', App\Livewire\Helpdesk\TicketSuccess::class)->name('guest.success');
});

// Guest Asset Loan Routes (No Authentication Required) - Livewire Based
Route::prefix('loan')->name('loan.guest.')->middleware(['guest.ratelimit'])->group(function () {
    Route::get('/apply', App\Livewire\GuestLoanApplication::class)->name('apply');
    Route::get('/create', App\Livewire\GuestLoanApplication::class)->name('create');
    Route::get('/tracking/{applicationNumber?}', App\Livewire\GuestLoanTracking::class)->name('tracking');
    Route::get('/track-application', App\Livewire\GuestLoanTracking::class)->name('track-token');
});

// Compatibility alias for legacy permission checks and error page quick links
Route::get('/loan/create', App\Livewire\GuestLoanApplication::class)
    ->middleware(['guest.ratelimit'])
    ->name('loan.create');

// Loan Application Wizard (v3.5.0 True Hybrid - Multi-step wizard)
// @see Requirements 3.1, 3.2, 3.4, 24.2, 25.1, 25.2, 25.3, 25.6
Route::prefix('loan')->name('loan.')->middleware(['guest.ratelimit'])->group(function () {
    Route::get('/wizard', App\Livewire\GuestLoanApplication::class)->name('wizard');
    Route::get('/success', fn() => view('loan.success'))->name('success');
});

// Unified Status Checker (Token-based lookup for tickets and loans) - v3.5.0 True Hybrid
// @see Requirements 2.1, 2.2
Route::prefix('status')->name('status.')->middleware(['guest.ratelimit'])->group(function () {
    Route::get('/', App\Livewire\Status\StatusChecker::class)->name('check');
    Route::get('/{token}', App\Livewire\Status\StatusChecker::class)->name('check.token');
});

// ICT Staff Directory - Contact information for BPM ICT support
// @see figma-ui-redesign Requirements 31
Route::get('/directory', App\Livewire\Directory\StaffDirectory::class)->name('directory');

/*
|--------------------------------------------------------------------------
| URL-Based Locale Routes (Task 3.1.7)
|--------------------------------------------------------------------------
|
| These routes support URL-based locale prefixes for guest forms:
| - /ms/helpdesk/create → Bahasa Melayu
| - /en/helpdesk/create → English
|
| The UrlBasedLocale middleware extracts the locale from the URL prefix
| and sets the application locale accordingly.
|
| @trace Task 3.1.7 - Implement URL-based locale
| @requirements R13 (Bilingual Support)
*/

// Localized Guest Helpdesk Routes
Route::prefix('{locale}')->where(['locale' => 'en|ms'])->middleware(['url.locale', 'guest.ratelimit'])->group(function () {
    // Helpdesk routes with locale prefix
    Route::prefix('helpdesk')->name('helpdesk.localized.')->group(function () {
        Route::get('/create', App\Livewire\Helpdesk\GuestTicketForm::class)->name('create');
        Route::get('/submit', App\Livewire\Helpdesk\SubmitTicket::class)->name('submit');
        Route::get('/track/{ticketNumber?}', App\Livewire\Helpdesk\TrackTicket::class)->name('track');
        Route::get('/success', App\Livewire\Helpdesk\TicketSuccess::class)->name('success');
    });

    // Loan routes with locale prefix
    Route::prefix('loan')->name('loan.localized.')->group(function () {
        Route::get('/apply', App\Livewire\GuestLoanApplication::class)->name('apply');
        Route::get('/create', App\Livewire\GuestLoanApplication::class)->name('create');
        Route::get('/tracking/{applicationNumber?}', App\Livewire\GuestLoanTracking::class)->name('tracking');
    });

    // Ticket routes with locale prefix (alias)
    Route::prefix('ticket')->name('ticket.localized.')->group(function () {
        Route::get('/create', App\Livewire\Helpdesk\GuestTicketForm::class)->name('create');
        Route::get('/track/{ticketNumber?}', App\Livewire\Helpdesk\TrackTicket::class)->name('track');
    });
});

Route::get('dashboard', App\Livewire\Staff\AuthenticatedDashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Account Linking at /dashboard/link-submissions per Requirement 18.1
Route::get('dashboard/link-submissions', App\Livewire\Staff\AccountLinking::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard.link-submissions');

// Portal Routes (Alias for Staff Routes)
Route::middleware(['auth', 'verified'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', App\Livewire\Staff\AuthenticatedDashboard::class)->name('dashboard');
    Route::get('/search', App\Livewire\Staff\CrossModuleSearch::class)->name('search');
    Route::get('/profile', App\Livewire\Staff\UserProfile::class)->name('profile');
    Route::get('/submissions', App\Livewire\Staff\SubmissionHistory::class)->name('submissions');
    Route::get('/submissions/{id}', App\Livewire\SubmissionDetail::class)->name('submissions.show');
    Route::get('/submissions/export-pdf/{type}', [App\Http\Controllers\Portal\SubmissionExportController::class, 'exportPDF'])
        ->where('type', 'tickets|loans')
        ->name('submissions.export-pdf');
    Route::get('/approvals', App\Livewire\Staff\ApprovalInterface::class)->name('approvals');
    Route::get('/delegations', App\Livewire\Staff\DelegationManager::class)->name('delegations');

    // Account Linking (v3.5.0 True Hybrid - Link historical guest submissions)
    // @see Requirements 18.1, 18.2, 18.3, 18.4, 18.5
    Route::get('/link-submissions', App\Livewire\Staff\AccountLinking::class)->name('link-submissions');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Profile update endpoint for test compatibility (Livewire component delegates actual UI)
Route::get('profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])
    ->middleware(['auth'])
    ->name('profile.edit');

Route::patch('profile', [App\Http\Controllers\ProfileController::class, 'update'])
    ->middleware(['auth'])
    ->name('profile.update');

// Staff Portal Routes (Staff Role Required)
Route::middleware(['auth', 'verified', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    // Dashboard & Profile
    Route::get('/dashboard', App\Livewire\Staff\AuthenticatedDashboard::class)->name('dashboard');
    Route::get('/profile', App\Livewire\Staff\UserProfile::class)->name('profile');

    // Submission Management
    Route::get('/history', App\Livewire\Staff\SubmissionHistory::class)->name('history');
    Route::get('/claim-submissions', App\Livewire\Staff\ClaimSubmissions::class)->name('claim-submissions');

    // Account Linking (v3.5.0 True Hybrid - Link historical guest submissions)
    // @see Requirements 18.1, 18.2, 18.3, 18.4, 18.5
    Route::get('/link-submissions', App\Livewire\Staff\AccountLinking::class)->name('link-submissions');

    // My Submissions (alias)
    Route::get('/my-submissions', App\Livewire\Staff\SubmissionHistory::class)->name('my-submissions');

    // Approvals (Approver role required)
    Route::middleware('approver')->group(function () {
        Route::get('/approvals', App\Livewire\Staff\ApprovalInterface::class)->name('approvals.index');
        Route::get('/approval-queue', App\Livewire\Approver\ApprovalQueue::class)->name('approval-queue');
    });

    // Notifications
    Route::get('/notifications', App\Livewire\NotificationCenter::class)->name('notifications');

    // Helpdesk Tickets
    Route::get('/tickets', App\Livewire\Helpdesk\MyTickets::class)->name('tickets.index');
    Route::get('/tickets/create', App\Livewire\Helpdesk\SubmitTicket::class)->name('tickets.create');
    Route::get('/tickets/{ticket}', App\Livewire\Helpdesk\TicketDetails::class)->name('tickets.show');

    // Loan Applications
    Route::get('/loans', App\Livewire\Loans\LoanHistory::class)->name('loans.index');
    Route::get('/loans/{application}', App\Livewire\Loans\LoanDetails::class)->name('loans.show');
    Route::get('/loans/{application}/extend', App\Livewire\Loans\LoanExtension::class)->name('loans.extend');

    // Data Subject Rights (PDPA Compliance)
    Route::prefix('data-rights')->name('data-rights.')->group(function () {
        Route::get('/', [App\Http\Controllers\Portal\DataSubjectRightsController::class, 'index'])->name('index');
        Route::get('/export', [App\Http\Controllers\Portal\DataSubjectRightsController::class, 'exportData'])->name('export');
        Route::post('/correction', [App\Http\Controllers\Portal\DataSubjectRightsController::class, 'requestCorrection'])->name('correction');
        Route::post('/deletion', [App\Http\Controllers\Portal\DataSubjectRightsController::class, 'requestDeletion'])->name('deletion');
        Route::get('/consent-history', [App\Http\Controllers\Portal\DataSubjectRightsController::class, 'consentHistory'])->name('consent-history');
        Route::post('/consent', [App\Http\Controllers\Portal\DataSubjectRightsController::class, 'updateConsent'])->name('consent.update');
    });
});

// Tickets Routes (Alias for Staff Helpdesk Routes) - NO NAMESPACE PREFIX
Route::middleware(['auth', 'verified'])->name('tickets.')->group(function () {
    Route::get('/tickets/create', App\Livewire\Helpdesk\SubmitTicket::class)->name('create');
});

// Helpdesk Routes (Alias for Staff Helpdesk Routes) - NO NAMESPACE PREFIX
Route::middleware(['auth', 'verified'])->prefix('helpdesk')->name('helpdesk.')->group(function () {
    Route::get('/tickets', App\Livewire\Helpdesk\MyTickets::class)->name('tickets.index');
});

// Loans Routes (Alias for Staff Loans Routes) - NO NAMESPACE PREFIX
Route::middleware(['auth', 'verified'])->prefix('loans')->name('loans.')->group(function () {
    Route::get('/history', App\Livewire\Loans\LoanHistory::class)->name('history');
});

// Loan history alias for compatibility with dashboard links and tests
Route::middleware(['auth', 'verified'])
    ->get('/loan/history', App\Livewire\Loans\LoanHistory::class)
    ->name('loan.history');

// Email Approval Routes (No Authentication Required)
Route::prefix('loan/approval')->name('loan.approval.')->group(function () {
    // v3.5.0 Volt Component - Guest-accessible approval page per Requirements 4.2, 4.3
    Route::get('/review/{token}', fn(string $token) => view('livewire.loan.approval-page', ['token' => $token]))
        ->name('review');

    // Legacy controller-based routes (kept for backward compatibility)
    Route::get('/approve/{token}', [App\Http\Controllers\LoanApprovalController::class, 'showApprovalForm'])->name('approve');
    Route::post('/approve', [App\Http\Controllers\LoanApprovalController::class, 'approve'])->name('approve.process');
    Route::get('/decline/{token}', [App\Http\Controllers\LoanApprovalController::class, 'showDeclineForm'])->name('decline');
    Route::post('/decline', [App\Http\Controllers\LoanApprovalController::class, 'decline'])->name('decline.process');
});

// Responsible Officer Sponsorship Routes (No Authentication Required)
Route::get('/loan/sponsorship/acknowledge/{token}', [App\Http\Controllers\LoanSponsorshipController::class, 'acknowledge'])
    ->name('loan.sponsorship.acknowledge');

// Email Approval Workflow Routes (Test Support)
Route::get('/loan/approve', [App\Http\Controllers\LoanApprovalController::class, 'processApproval'])->name('loan.approve');

// Portal Approval Routes (Authenticated Approver Actions)
Route::middleware(['auth', 'verified', 'approver'])
    ->prefix('loan/approvals')
    ->name('loan.approvals.')
    ->group(function () {
        Route::post('/{application}/approve', [App\Http\Controllers\Portal\PortalLoanApprovalController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [App\Http\Controllers\Portal\PortalLoanApprovalController::class, 'reject'])->name('reject');
    });

// Loan Management Routes (Traditional Controller for performance testing)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/loans/dashboard', App\Livewire\Loans\AuthenticatedDashboard::class)->name('loans.dashboard');
    Route::get('/loans', [App\Http\Controllers\LoanController::class, 'index'])->name('loans.index');
    Route::post('/loans', [App\Http\Controllers\LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/assets/available', [App\Http\Controllers\LoanController::class, 'availableAssets'])->name('loans.assets.available');
});

// Authenticated Loan Management Routes (Livewire Based)
Route::middleware(['auth', 'verified'])->prefix('loans')->name('loan.authenticated.')->group(function () {
    Route::get('/history', App\Livewire\Loans\LoanHistory::class)->name('history');
    Route::get('/applications/{application}', App\Livewire\Loans\LoanDetails::class)->name('show');
    Route::get('/applications/{application}/extend', App\Livewire\Loans\LoanExtension::class)->name('extend');
    Route::post('/applications/{application}/extend', [App\Http\Controllers\LoanExtensionController::class, 'store'])->name('extend.process');

    // Authenticated users can also use the guest form
    Route::get('/create', App\Livewire\GuestLoanApplication::class)->name('create');

    // Additional components for loan history and extensions will be registered once completed.
});

// Authenticated Helpdesk Routes (Livewire Based)
Route::middleware(['auth', 'verified'])->prefix('helpdesk')->name('helpdesk.authenticated.')->group(function () {
    Route::get('/dashboard', App\Livewire\Helpdesk\Dashboard::class)->name('dashboard');
    Route::get('/tickets', App\Livewire\Helpdesk\MyTickets::class)->name('tickets');
    Route::get('/tickets/{ticket}', App\Livewire\Helpdesk\TicketDetails::class)->name('ticket.show');
    Route::post('/tickets/{ticket}/claim', [App\Http\Controllers\HelpdeskTicketController::class, 'claim'])->name('ticket.claim');
});

// Impersonation Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/impersonate/stop', [App\Http\Controllers\ImpersonationController::class, 'stop'])->name('impersonate.stop');
    Route::get('/impersonate/{user}', [App\Http\Controllers\ImpersonationController::class, 'impersonate'])->name('impersonate.start');
});

// Admin Analytics Export Routes
Route::middleware(['auth', 'verified'])->prefix('admin/analytics')->name('admin.analytics.')->group(function () {
    Route::get('/export/csv', [App\Http\Controllers\Admin\AnalyticsExportController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/json', [App\Http\Controllers\Admin\AnalyticsExportController::class, 'exportJson'])->name('export.json');
});

Route::get('/bedrock-chat/{id?}', App\Livewire\BedrockChat::class)->name('bedrock.chat');

require __DIR__ . '/auth.php';

Route::get('/two-factor-challenge', App\Livewire\Auth\TwoFactorChallenge::class)
    ->middleware(['auth'])
    ->name('two-factor.challenge');

// Privacy Policy Route (PDPA Compliance)
Route::view('/privacy-policy', 'pages.privacy-policy')->name('privacy-policy');

// Development-only: quick endpoint to trigger a broadcast for testing Reverb
if (app()->environment('local')) {
    Route::any('/reverb-test', function () {
        $loan = \App\Models\LoanApplication::factory()->create(['status' => \App\Enums\LoanStatus::IN_USE]);

        event(new \App\Events\StatusUpdated($loan, 'in_use', 'returned', 1));

        return response()->json(['ok' => true, 'loan_id' => $loan->id]);
    })->name('reverb.test');
}
