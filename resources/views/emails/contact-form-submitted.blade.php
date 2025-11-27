@component('mail::message')
    # {{ __('Your Message Has Been Received') }}

    {{ __('Dear :name,', ['name' => $ticket->guest_name]) }}

    {{ __('Thank you for contacting us. Your message has been received and converted to a support ticket for tracking and follow-up.') }}

    @component('mail::panel')
        **{{ __('Ticket Number') }}:** {{ $ticket->ticket_number }}

        **{{ __('Subject') }}:** {{ $ticket->subject }}

        **{{ __('Status') }}:** {{ __('Open') }}

        **{{ __('Submitted') }}:** {{ $ticket->created_at->format('d M Y, H:i') }}
    @endcomponent

    {{ __('You can track the status of your enquiry using the ticket number above.') }}

    @component('mail::button', ['url' => route('helpdesk.guest.track')])
        {{ __('Track Your Ticket') }}
    @endcomponent

    {{ __('Our support team will review your message and respond as soon as possible.') }}

    ---

    {{ __('If you have any urgent matters, please contact us directly:') }}

    - **{{ __('Phone') }}:** +603-8891 7000
    - **{{ __('Email') }}:** helpdesk@motac.gov.my

    {{ __('Thank you for your patience.') }}

    {{ __('Best regards,') }}<br>
    {{ __('ICT Support Team') }}<br>
    {{ __('Bahagian Pengurusan Maklumat') }}<br>
    {{ __('Kementerian Pelancongan, Seni dan Budaya') }}
@endcomponent
