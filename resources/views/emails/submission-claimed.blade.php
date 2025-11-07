<x-mail::message>
    # Submission Successfully Claimed

    Hello {{ $userName }},

    You have successfully claimed your {{ $submissionType }} submission.

    **Submission Number:** {{ $submissionNumber }}

    You can now view and manage this submission through your authenticated portal account.

    <x-mail::button :url="$submissionUrl">
        View Submission
    </x-mail::button>

    All future updates for this submission will be available in your dashboard.

    <x-mail::button :url="$dashboardUrl" color="secondary">
        Go to Dashboard
    </x-mail::button>

    If you have any questions, please contact ICT Support.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
