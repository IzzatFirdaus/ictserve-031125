<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

// Public Information Pages (No Authentication Required)
Route::view('/accessibility', 'pages.accessibility')->name('accessibility');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/services', 'pages.services')->name('services');

// Language Switcher Route (No Authentication Required)
Route::get('/change-locale/{locale}', [App\Http\Controllers\LanguageController::class, 'change'])
    ->where('locale', 'en|ms')
    ->name('change-locale');

// Guest Helpdesk Routes (No Authentication Required) - Livewire Based
Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
    Route::get('/create', App\Livewire\Helpdesk\SubmitTicket::class)->name('create');
    Route::get('/submit', App\Livewire\Helpdesk\SubmitTicket::class)->name('submit');
    Route::get('/track/{ticketNumber?}', App\Livewire\Helpdesk\TrackTicket::class)->name('track');
    Route::get('/success', App\Livewire\Helpdesk\TicketSuccess::class)->name('guest.success');
});

// Guest Asset Loan Routes (No Authentication Required) - Livewire Based
Route::prefix('loan')->name('loan.guest.')->group(function () {
    Route::get('/apply', App\Livewire\GuestLoanApplication::class)->name('apply');
    Route::get('/create', App\Livewire\GuestLoanApplication::class)->name('create');
    Route::get('/tracking/{applicationNumber?}', App\Livewire\GuestLoanTracking::class)->name('tracking');
});

Route::get('dashboard', App\Livewire\Staff\AuthenticatedDashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Portal Routes (Alias for Staff Routes)
Route::middleware(['auth', 'verified'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', App\Livewire\Staff\AuthenticatedDashboard::class)->name('dashboard');
    Route::get('/profile', App\Livewire\Staff\UserProfile::class)->name('profile');
    Route::get('/submissions', App\Livewire\Staff\SubmissionHistory::class)->name('submissions');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Staff Portal Routes (Staff Role Required)
Route::middleware(['auth', 'verified', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    // Dashboard & Profile
    Route::get('/dashboard', App\Livewire\Staff\AuthenticatedDashboard::class)->name('dashboard');
    Route::get('/profile', App\Livewire\Staff\UserProfile::class)->name('profile');

    // Submission Management
    Route::get('/history', App\Livewire\Staff\SubmissionHistory::class)->name('history');
    Route::get('/claim-submissions', App\Livewire\Staff\ClaimSubmissions::class)->name('claim-submissions');

    // Approvals (Approver role via policy)
    Route::get('/approvals', App\Livewire\Staff\ApprovalInterface::class)->name('approvals.index');

    // Notifications
    Route::get('/notifications', App\Livewire\NotificationCenter::class)->name('notifications');

    // Helpdesk Tickets
    Route::get('/tickets', App\Livewire\Helpdesk\MyTickets::class)->name('tickets.index');
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

// Email Approval Routes (No Authentication Required)
Route::prefix('loan/approval')->name('loan.approval.')->group(function () {
    Route::get('/approve/{token}', [App\Http\Controllers\LoanApprovalController::class, 'showApprovalForm'])->name('approve');
    Route::post('/approve', [App\Http\Controllers\LoanApprovalController::class, 'approve'])->name('approve.process');
    Route::get('/decline/{token}', [App\Http\Controllers\LoanApprovalController::class, 'showDeclineForm'])->name('decline');
    Route::post('/decline', [App\Http\Controllers\LoanApprovalController::class, 'decline'])->name('decline.process');
});

// Authenticated Loan Management Routes (Livewire Based)
Route::middleware(['auth', 'verified'])->prefix('loans')->name('loan.authenticated.')->group(function () {
    Route::get('/dashboard', App\Livewire\Loans\AuthenticatedDashboard::class)->name('dashboard');
    Route::get('/history', App\Livewire\Loans\LoanHistory::class)->name('history');
    Route::get('/applications/{application}', App\Livewire\Loans\LoanDetails::class)->name('show');
    Route::get('/applications/{application}/extend', App\Livewire\Loans\LoanExtension::class)->name('extend');

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

require __DIR__.'/auth.php';
