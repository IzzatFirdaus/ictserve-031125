<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

// Language Switcher Route (No Authentication Required)
Route::get('/change-locale/{locale}', [App\Http\Controllers\LanguageController::class, 'change'])
    ->where('locale', 'en|ms')
    ->name('change-locale');

// Guest Helpdesk Routes (No Authentication Required) - Livewire Based
Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
    Route::get('/create', App\Livewire\Helpdesk\SubmitTicket::class)->name('create');
    Route::get('/submit', App\Livewire\Helpdesk\SubmitTicket::class)->name('submit');
    Route::get('/track/{ticketNumber?}', App\Livewire\Helpdesk\TrackTicket::class)->name('track');
});

// Guest Asset Loan Routes (No Authentication Required) - Livewire Based
Route::prefix('loan')->name('loan.guest.')->group(function () {
    Route::get('/apply', App\Livewire\GuestLoanApplication::class)->name('apply');
    Route::get('/create', App\Livewire\GuestLoanApplication::class)->name('create');
    Route::get('/tracking/{applicationNumber?}', App\Livewire\GuestLoanTracking::class)->name('tracking');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Staff Portal Routes (Authenticated)
Route::middleware(['auth', 'verified'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/approvals', App\Livewire\Loans\ApprovalQueue::class)->name('approvals.index');
    Route::get('/dashboard', [App\Http\Controllers\StaffPortalController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\StaffPortalController::class, 'profile'])->name('profile');
    Route::post('/claim-submission', [App\Http\Controllers\StaffPortalController::class, 'claim'])->name('claim-submission');
});

// Email Approval Routes (No Authentication Required)
Route::prefix('loan/approval')->name('loan.approval.')->group(function () {
    Route::get('/{token}', [App\Http\Controllers\EmailApprovalController::class, 'show'])->name('show');
    Route::post('/{token}/approve', [App\Http\Controllers\EmailApprovalController::class, 'approve'])->name('approve');
    Route::post('/{token}/decline', [App\Http\Controllers\EmailApprovalController::class, 'decline'])->name('decline');
    Route::get('/success', [App\Http\Controllers\EmailApprovalController::class, 'success'])->name('success');
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

    // Authenticated users can also use the guest helpdesk form
    Route::get('/create', App\Livewire\Helpdesk\SubmitTicket::class)->name('create');
});

require __DIR__.'/auth.php';
