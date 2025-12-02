<x-mail::message>
# {{ __('loan.email.collection_greeting', ['name' => $applicantName]) }}

{{ __('loan.email.collection_message') }}

**{{ __('loan.fields.application_number') }}:** {{ $applicationNumber }}
**{{ __('loan.fields.start_date') }}:** {{ $issueDate }}
**{{ __('loan.fields.end_date') }}:** {{ $returnDate }}

## {{ __('loan.email.collection_details') }}

**{{ __('loan.email.collection_location_label') }}:**
{{ $collectionLocation }}

**{{ __('loan.email.required_documents_label') }}:**
{{ $requiredDocuments }}

<x-mail::panel>
**{{ __('loan.email.important_notice') }}**

- {{ __('loan.email.collection_notice_1') }}
- {{ __('loan.email.collection_notice_2') }}
- {{ __('loan.email.collection_notice_3') }}
</x-mail::panel>

<x-mail::button :url="route('dashboard')">
{{ __('loan.email.view_details') }}
</x-mail::button>

{{ __('loan.email.thanks') }},
{{ config('app.name') }}
</x-mail::message>
