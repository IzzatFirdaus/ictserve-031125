<x-mail::message>
    # Tiket Ditugaskan Kepada Anda

    Salam {{ $assignedUser->name }},

    Tiket berikut telah ditugaskan kepada anda:

    **Nombor Tiket:** {{ $ticket->ticket_number }}
    **Subjek:** {{ $ticket->subject }}
    **Keutamaan:** {{ ucfirst($ticket->priority) }}
    **Status:** {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
    **Tarikh Akhir SLA:** {{ $ticket->sla_resolution_due_at?->format('d M Y, h:i A') }}

    @if ($ticket->description)
        **Penerangan:**
        {{ $ticket->description }}
    @endif

    <x-mail::button :url="$ticketUrl">
        Lihat Tiket
    </x-mail::button>

    Sila ambil tindakan yang sewajarnya untuk menyelesaikan tiket ini sebelum tarikh akhir SLA.

    Terima kasih,<br>
    {{ config('app.name') }}
</x-mail::message>
