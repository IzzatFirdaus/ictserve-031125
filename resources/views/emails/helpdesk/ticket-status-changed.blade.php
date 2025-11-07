<x-mail::message>
    # Status Tiket Dikemaskini

    Salam,

    Status tiket anda telah dikemaskini:

    **Nombor Tiket:** {{ $ticket->ticket_number }}
    **Subjek:** {{ $ticket->subject }}
    **Status Lama:** {{ ucfirst(str_replace('_', ' ', $oldStatus)) }}
    **Status Baru:** {{ ucfirst(str_replace('_', ' ', $newStatus)) }}

    {{ $description }}

    @if ($ticket->assigned_to_user)
        **Ditugaskan Kepada:** {{ $ticket->assignedUser->name }}
    @endif

    <x-mail::button :url="$ticketUrl">
        Lihat Tiket
    </x-mail::button>

    Terima kasih,<br>
    {{ config('app.name') }}
</x-mail::message>
