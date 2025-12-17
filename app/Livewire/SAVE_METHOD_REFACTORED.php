public function save()
{
$this->validate();

// 3-Day Rule Validation (Enforce on save)
$calculator = new WorkingDayCalculator();
if (!$calculator->validateLeadTime(now(), $this->loan_start_date, 3)) {
$nextAvailable = $calculator->getNextAvailableDate(now(), 3)->format('d/m/Y');
$this->addError('loan_start_date', "Permohonan mesti dibuat sekurang-kurangnya 3 hari bekerja sebelum tarikh pinjaman. Tarikh terawal yang boleh dipilih ialah $nextAvailable.");
return;
}

try {
DB::transaction(function () {
// Create Loan Application
$application = LoanApplication::create([
'application_number' => 'LA' . now()->format('Ym') . \random_int(1000, 9999),
'applicant_name' => $this->applicant_name,
'applicant_email' => $this->applicant_email,
'applicant_phone' => $this->applicant_phone,
'staff_id' => $this->applicant_staff_id,
'division_id' => $this->division_id,
'applicant_position' => $this->applicant_position,
'applicant_grade' => $this->applicant_grade,
'grade' => $this->applicant_grade,

'is_applicant_responsible' => $this->is_applicant_responsible,
'responsible_officer_name' => $this->is_applicant_responsible ? null : $this->responsible_officer_name,
'responsible_officer_email' => $this->is_applicant_responsible ? null : $this->responsible_officer_email,
'responsible_officer_phone' => $this->is_applicant_responsible ? null : $this->responsible_officer_phone,
'responsible_officer_position' => $this->is_applicant_responsible ? null : $this->responsible_officer_position,
'responsible_officer_grade' => $this->is_applicant_responsible ? null : $this->responsible_officer_grade,

'purpose' => $this->purpose,
'location' => $this->location,
'loan_start_date' => $this->loan_start_date,
'loan_end_date' => $this->loan_end_date,
'expected_return_date' => $this->loan_end_date,
'declared_at' => now(),
'status' => 'submitted',
]);

// Eager load assets with categories to prevent N+1
$assets = Asset::with('category')->findMany($this->selected_assets);

// Create Loan Items for selected assets
foreach ($assets as $asset) {
LoanItem::create([
'loan_application_id' => $application->id,
'asset_id' => $asset->id,
'equipment_type' => $asset->category->name ?? 'General Equipment',
'quantity' => 1,
'unit_value' => $asset->purchase_value,
'total_value' => $asset->purchase_value,
'condition_before' => $asset->condition,
]);
}

// Update total value and return location
$application->update([
'total_value' => $application->loanItems()->sum('total_value'),
'return_location' => $this->return_location,
]);

// Handle Delegation
if (!$this->is_applicant_responsible) {
$service = new ResponsibleOfficerService();
$service->handleDelegatedApplication($application);
}

Log::info("Loan application created", [
'application_number' => $application->application_number,
'applicant' => $this->applicant_name,
'items_count' => count($this->selected_assets),
]);

session()->flash('message', __('loan.messages.application_submitted_check_email'));
$this->reset();
});
} catch (\Exception $e) {
Log::error("Failed to create loan application", [
'error' => $e->getMessage(),
'applicant' => $this->applicant_name,
]);

$this->addError('save', 'Ralat berlaku semasa menyimpan permohonan. Sila cuba lagi.');
}
}