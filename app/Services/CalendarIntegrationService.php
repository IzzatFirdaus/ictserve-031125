<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\LoanApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Calendar Integration Service
 *
 * Manages integration with calendar systems (Outlook/Google Calendar)
 * for booking management, scheduling, and conflict detection.
 *
 * @see D03-FR-006.4 Calendar integration
 * @see D04 §6.3 External system integration
 */
class CalendarIntegrationService
{
    private string $provider;

    private array $config;

    private bool $enabled;

    public function __construct()
    {
        $this->provider = config('services.calendar.provider', 'outlook');
        $this->config = config("services.calendar.{$this->provider}", []);
        $this->enabled = config('services.calendar.enabled', false);
    }

    /**
     * Create calendar event for loan booking
     *
     * @param  LoanApplication  $loanApplication  Loan application
     * @return array Calendar event details
     *
     * @throws \Exception If calendar API is unavailable
     */
    public function createLoanBookingEvent(LoanApplication $loanApplication): array
    {
        if (! $this->enabled) {
            Log::info('Calendar integration is disabled');

            return ['status' => 'disabled'];
        }

        try {
            $eventData = $this->prepareLoanEventData($loanApplication);

            $event = match ($this->provider) {
                'outlook' => $this->createOutlookEvent($eventData),
                'google' => $this->createGoogleEvent($eventData),
                default => throw new \Exception("Unsupported calendar provider: {$this->provider}"),
            };

            // Store calendar event ID in loan application
            $loanApplication->update([
                'calendar_event_id' => $event['id'] ?? null,
                'calendar_provider' => $this->provider,
            ]);

            Log::info('Calendar event created for loan booking', [
                'application_number' => $loanApplication->application_number,
                'event_id' => $event['id'] ?? null,
                'provider' => $this->provider,
            ]);

            return $event;
        } catch (\Exception $e) {
            Log::error('Failed to create calendar event', [
                'application_number' => $loanApplication->application_number,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Update calendar event for loan modification
     *
     * @param  LoanApplication  $loanApplication  Loan application
     * @return array Updated event details
     */
    public function updateLoanBookingEvent(LoanApplication $loanApplication): array
    {
        if (! $this->enabled || ! $loanApplication->calendar_event_id) {
            return ['status' => 'skipped'];
        }

        try {
            $eventData = $this->prepareLoanEventData($loanApplication);

            $event = match ($loanApplication->calendar_provider) {
                'outlook' => $this->updateOutlookEvent($loanApplication->calendar_event_id, $eventData),
                'google' => $this->updateGoogleEvent($loanApplication->calendar_event_id, $eventData),
                default => throw new \Exception("Unsupported calendar provider: {$loanApplication->calendar_provider}"),
            };

            Log::info('Calendar event updated for loan booking', [
                'application_number' => $loanApplication->application_number,
                'event_id' => $loanApplication->calendar_event_id,
            ]);

            return $event;
        } catch (\Exception $e) {
            Log::error('Failed to update calendar event', [
                'application_number' => $loanApplication->application_number,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Delete calendar event for cancelled loan
     *
     * @param  LoanApplication  $loanApplication  Loan application
     * @return bool Success status
     */
    public function deleteLoanBookingEvent(LoanApplication $loanApplication): bool
    {
        if (! $this->enabled || ! $loanApplication->calendar_event_id) {
            return false;
        }

        try {
            $success = match ($loanApplication->calendar_provider) {
                'outlook' => $this->deleteOutlookEvent($loanApplication->calendar_event_id),
                'google' => $this->deleteGoogleEvent($loanApplication->calendar_event_id),
                default => false,
            };

            if ($success) {
                $loanApplication->update([
                    'calendar_event_id' => null,
                    'calendar_provider' => null,
                ]);

                Log::info('Calendar event deleted for cancelled loan', [
                    'application_number' => $loanApplication->application_number,
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Failed to delete calendar event', [
                'application_number' => $loanApplication->application_number,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Export loan schedule to calendar file (iCal format)
     *
     * @param  LoanApplication  $loanApplication  Loan application
     * @return string iCal file content
     */
    public function exportToICalendar(LoanApplication $loanApplication): string
    {
        $startDate = $loanApplication->loan_start_date->format('Ymd\THis\Z');
        $endDate = $loanApplication->loan_end_date->format('Ymd\THis\Z');
        $now = now()->format('Ymd\THis\Z');

        $assets = $loanApplication->loanItems->map(fn ($item) => $item->asset->name)->join(', ');

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//ICTServe//Asset Loan System//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:{$loanApplication->application_number}@ictserve.motac.gov.my\r\n";
        $ical .= "DTSTAMP:{$now}\r\n";
        $ical .= "DTSTART:{$startDate}\r\n";
        $ical .= "DTEND:{$endDate}\r\n";
        $ical .= "SUMMARY:Asset Loan: {$loanApplication->application_number}\r\n";
        $ical .= "DESCRIPTION:Loan Application: {$loanApplication->application_number}\\n";
        $ical .= "Assets: {$assets}\\n";
        $ical .= "Purpose: {$loanApplication->purpose}\\n";
        $ical .= "Location: {$loanApplication->location}\r\n";
        $ical .= "LOCATION:{$loanApplication->location}\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "SEQUENCE:0\r\n";
        $ical .= "BEGIN:VALARM\r\n";
        $ical .= "TRIGGER:-PT48H\r\n";
        $ical .= "ACTION:DISPLAY\r\n";
        $ical .= "DESCRIPTION:Asset loan return reminder - 48 hours\r\n";
        $ical .= "END:VALARM\r\n";
        $ical .= "END:VEVENT\r\n";
        $ical .= "END:VCALENDAR\r\n";

        return $ical;
    }

    /**
     * Check for calendar conflicts with existing bookings
     *
     * @param  Carbon  $startDate  Start date
     * @param  Carbon  $endDate  End date
     * @param  array  $assetIds  Asset IDs to check
     * @return array Conflict details
     */
    public function checkBookingConflicts(Carbon $startDate, Carbon $endDate, array $assetIds): array
    {
        $conflicts = [];

        foreach ($assetIds as $assetId) {
            $asset = Asset::find($assetId);

            if (! $asset) {
                continue;
            }

            // Check existing loan bookings
            $conflictingLoans = LoanApplication::whereHas('loanItems', function ($query) use ($assetId) {
                $query->where('asset_id', $assetId);
            })
                ->whereIn('status', ['approved', 'ready_issuance', 'issued', 'in_use'])
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('loan_start_date', [$startDate, $endDate])
                        ->orWhereBetween('loan_end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('loan_start_date', '<=', $startDate)
                                ->where('loan_end_date', '>=', $endDate);
                        });
                })
                ->get();

            if ($conflictingLoans->isNotEmpty()) {
                $conflicts[] = [
                    'asset_id' => $assetId,
                    'asset_name' => $asset->name,
                    'asset_tag' => $asset->asset_tag,
                    'conflicting_bookings' => $conflictingLoans->map(function ($loan) {
                        return [
                            'application_number' => $loan->application_number,
                            'start_date' => $loan->loan_start_date->format('Y-m-d'),
                            'end_date' => $loan->loan_end_date->format('Y-m-d'),
                            'borrower' => $loan->applicant_name,
                        ];
                    })->toArray(),
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Get alternative available assets for conflicting dates
     *
     * @param  int  $categoryId  Asset category ID
     * @param  Carbon  $startDate  Start date
     * @param  Carbon  $endDate  End date
     * @return array Available alternative assets
     */
    public function getAlternativeAssets(int $categoryId, Carbon $startDate, Carbon $endDate): array
    {
        $availableAssets = Asset::where('category_id', $categoryId)
            ->where('status', 'available')
            ->whereDoesntHave('loanItems.loanApplication', function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', ['approved', 'ready_issuance', 'issued', 'in_use'])
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('loan_start_date', [$startDate, $endDate])
                            ->orWhereBetween('loan_end_date', [$startDate, $endDate])
                            ->orWhere(function ($q2) use ($startDate, $endDate) {
                                $q2->where('loan_start_date', '<=', $startDate)
                                    ->where('loan_end_date', '>=', $endDate);
                            });
                    });
            })
            ->get();

        return $availableAssets->map(function ($asset) {
            return [
                'id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'name' => $asset->name,
                'brand' => $asset->brand,
                'model' => $asset->model,
                'specifications' => $asset->specifications,
                'condition' => $asset->condition->label(),
            ];
        })->toArray();
    }

    /**
     * Prepare event data for calendar systems
     *
     * @param  LoanApplication  $loanApplication  Loan application
     * @return array Event data
     */
    private function prepareLoanEventData(LoanApplication $loanApplication): array
    {
        $assets = $loanApplication->loanItems->map(fn ($item) => $item->asset->name)->join(', ');

        return [
            'subject' => "Asset Loan: {$loanApplication->application_number}",
            'body' => [
                'contentType' => 'HTML',
                'content' => $this->buildEventDescription($loanApplication),
            ],
            'start' => [
                'dateTime' => $loanApplication->loan_start_date->toIso8601String(),
                'timeZone' => 'Asia/Kuala_Lumpur',
            ],
            'end' => [
                'dateTime' => $loanApplication->loan_end_date->toIso8601String(),
                'timeZone' => 'Asia/Kuala_Lumpur',
            ],
            'location' => [
                'displayName' => $loanApplication->location,
            ],
            'isReminderOn' => true,
            'reminderMinutesBeforeStart' => 2880, // 48 hours
        ];
    }

    /**
     * Build event description HTML
     *
     * @param  LoanApplication  $loanApplication  Loan application
     * @return string HTML description
     */
    private function buildEventDescription(LoanApplication $loanApplication): string
    {
        $assets = $loanApplication->loanItems->map(fn ($item) => $item->asset->name)->join('<br>');

        return "<h3>Asset Loan Booking</h3>
                <p><strong>Application Number:</strong> {$loanApplication->application_number}</p>
                <p><strong>Borrower:</strong> {$loanApplication->applicant_name}</p>
                <p><strong>Assets:</strong><br>{$assets}</p>
                <p><strong>Purpose:</strong> {$loanApplication->purpose}</p>
                <p><strong>Location:</strong> {$loanApplication->location}</p>
                <p><strong>Return Location:</strong> {$loanApplication->return_location}</p>";
    }

    /**
     * Create Outlook calendar event
     *
     * @param  array  $eventData  Event data
     * @return array Created event
     */
    private function createOutlookEvent(array $eventData): array
    {
        $accessToken = $this->getOutlookAccessToken();

        $response = Http::withToken($accessToken)
            ->post('https://graph.microsoft.com/v1.0/me/events', $eventData);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Failed to create Outlook event: {$response->status()}");
    }

    /**
     * Update Outlook calendar event
     *
     * @param  string  $eventId  Event ID
     * @param  array  $eventData  Event data
     * @return array Updated event
     */
    private function updateOutlookEvent(string $eventId, array $eventData): array
    {
        $accessToken = $this->getOutlookAccessToken();

        $response = Http::withToken($accessToken)
            ->patch("https://graph.microsoft.com/v1.0/me/events/{$eventId}", $eventData);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Failed to update Outlook event: {$response->status()}");
    }

    /**
     * Delete Outlook calendar event
     *
     * @param  string  $eventId  Event ID
     * @return bool Success status
     */
    private function deleteOutlookEvent(string $eventId): bool
    {
        $accessToken = $this->getOutlookAccessToken();

        $response = Http::withToken($accessToken)
            ->delete("https://graph.microsoft.com/v1.0/me/events/{$eventId}");

        return $response->successful();
    }

    /**
     * Get Outlook access token
     *
     * @return string Access token
     */
    private function getOutlookAccessToken(): string
    {
        // Implementation would use OAuth2 flow to get access token
        // This is a placeholder - actual implementation would need proper OAuth2 handling
        return $this->config['access_token'] ?? '';
    }

    /**
     * Create Google Calendar event
     *
     * @param  array  $eventData  Event data
     * @return array Created event
     */
    private function createGoogleEvent(array $eventData): array
    {
        $accessToken = $this->getGoogleAccessToken();

        // Convert event data to Google Calendar format
        $googleEventData = [
            'summary' => $eventData['subject'],
            'description' => strip_tags($eventData['body']['content']),
            'start' => [
                'dateTime' => $eventData['start']['dateTime'],
                'timeZone' => $eventData['start']['timeZone'],
            ],
            'end' => [
                'dateTime' => $eventData['end']['dateTime'],
                'timeZone' => $eventData['end']['timeZone'],
            ],
            'location' => $eventData['location']['displayName'],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 2880],
                    ['method' => 'popup', 'minutes' => 2880],
                ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $googleEventData);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Failed to create Google Calendar event: {$response->status()}");
    }

    /**
     * Update Google Calendar event
     *
     * @param  string  $eventId  Event ID
     * @param  array  $eventData  Event data
     * @return array Updated event
     */
    private function updateGoogleEvent(string $eventId, array $eventData): array
    {
        $accessToken = $this->getGoogleAccessToken();

        $googleEventData = [
            'summary' => $eventData['subject'],
            'description' => strip_tags($eventData['body']['content']),
            'start' => [
                'dateTime' => $eventData['start']['dateTime'],
                'timeZone' => $eventData['start']['timeZone'],
            ],
            'end' => [
                'dateTime' => $eventData['end']['dateTime'],
                'timeZone' => $eventData['end']['timeZone'],
            ],
            'location' => $eventData['location']['displayName'],
        ];

        $response = Http::withToken($accessToken)
            ->put("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$eventId}", $googleEventData);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Failed to update Google Calendar event: {$response->status()}");
    }

    /**
     * Delete Google Calendar event
     *
     * @param  string  $eventId  Event ID
     * @return bool Success status
     */
    private function deleteGoogleEvent(string $eventId): bool
    {
        $accessToken = $this->getGoogleAccessToken();

        $response = Http::withToken($accessToken)
            ->delete("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$eventId}");

        return $response->successful();
    }

    /**
     * Get Google Calendar access token
     *
     * @return string Access token
     */
    private function getGoogleAccessToken(): string
    {
        // Implementation would use OAuth2 flow to get access token
        // This is a placeholder - actual implementation would need proper OAuth2 handling
        return $this->config['access_token'] ?? '';
    }
}
