<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Staff Portal Routes (Authenticated)
Route::middleware(['auth', 'verified'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\StaffPortalController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\StaffPortalController::class, 'profile'])->name('profile');
    Route::post('/claim-submission', [App\Http\Controllers\StaffPortalController::class, 'claim'])->name('claim-submission');
});

// Guest Loan Application Routes (No Authentication Required)
Route::prefix('loan')->name('loan.guest.')->group(function () {
    Route::get('/apply', [App\Http\Controllers\GuestLoanApplicationController::class, 'create'])->name('create');
    Route::post('/apply', [App\Http\Controllers\GuestLoanApplicationController::class, 'store'])->name('store');
    Route::post('/check-availability', [App\Http\Controllers\GuestLoanApplicationController::class, 'checkAvailability'])->name('check-availability');
    Route::get('/tracking/{applicationNumber}', [App\Http\Controllers\GuestLoanApplicationController::class, 'tracking'])->name('tracking');
});

// Email Approval Routes (No Authentication Required)
Route::prefix('loan/approval')->name('loan.approval.')->group(function () {
    Route::get('/{token}', [App\Http\Controllers\EmailApprovalController::class, 'show'])->name('show');
    Route::post('/{token}/approve', [App\Http\Controllers\EmailApprovalController::class, 'approve'])->name('approve');
    Route::post('/{token}/decline', [App\Http\Controllers\EmailApprovalController::class, 'decline'])->name('decline');
    Route::get('/success', [App\Http\Controllers\EmailApprovalController::class, 'success'])->name('success');
});

// Authenticated Loan Management Routes
Route::middleware(['auth', 'verified'])->prefix('loans')->name('loan.authenticated.')->group(function () {
    Route::get('/', [App\Http\Controllers\AuthenticatedLoanController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\AuthenticatedLoanController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\AuthenticatedLoanController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\AuthenticatedLoanController::class, 'show'])->name('show');
    Route::post('/{id}/extend', [App\Http\Controllers\AuthenticatedLoanController::class, 'requestExtension'])->name('extend');
});

require __DIR__ . '/auth.php';
