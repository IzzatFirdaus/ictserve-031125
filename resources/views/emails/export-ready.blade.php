<x-mail::message>
    # Your Export is Ready

    Hello {{ $userName }},

    Your submission history export has been generated and is ready for download.

    **File:** {{ $filename }}
    **Expires:** {{ $expiresAt }}

    <x-mail::button :url="$downloadUrl">
        Download Export
    </x-mail::button>

    Please note that this file will be automatically deleted after 7 days for security reasons.

    If you have any questions, please contact ICT Support.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
