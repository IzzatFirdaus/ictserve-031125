<?php

/**
 * Translation file: Loan Module (English)
 * Description: English language translations for loan application module
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D15 (Language Support)
 *
 * @version 2.0.0
 *
 * @created 2025-11-04
 */

return [
    'form' => [
        'title' => 'ICT Equipment Loan Application Form',
        'subtitle' => 'For Official Use of Ministry of Tourism, Arts & Culture',
        'section_label' => 'FORM',
        'of_4_pages' => 'of 4 pages',
        'required_fields_note' => 'Fields marked with * are REQUIRED.',

        // Step labels
        'step_1_label' => 'Applicant Info',
        'step_2_label' => 'Responsible Officer',
        'step_3_label' => 'Equipment List',
        'step_4_label' => 'Confirmation',

        // Section headers
        'section_1_applicant' => 'SECTION 1 | APPLICANT INFORMATION',
        'section_2_responsible_officer' => 'SECTION 2 | RESPONSIBLE OFFICER INFORMATION',
        'section_3_equipment_list' => 'SECTION 3 | EQUIPMENT INFORMATION',
        'section_4_applicant_confirmation' => 'SECTION 4 | APPLICANT CONFIRMATION (RESPONSIBLE OFFICER)',
        'section_5_approval' => 'SECTION 5 | DEPARTMENT / UNIT / SECTION CONFIRMATION',

        // Notes and descriptions
        'select_equipment_note' => 'Please select required equipment and specify quantity.',
        'confirmation_statement' => 'I hereby confirm and certify that all borrowed equipment is for official use and under my responsibility and supervision throughout the period.',
        'approval_note' => 'Application completed by applicant must be SUPPORTED BY AN OFFICER AT LEAST GRADE 41 AND ABOVE.',
        'approval_process_title' => 'Approval Process',
        'approval_process_description' => 'Your application will be sent to the relevant officer for approval. You will receive an email notification when your application has been processed.',
        'review_summary' => 'Application Summary',
        'your_information' => 'Your Information',
    ],

    'fields' => [
        'applicant_name' => 'Full Name',
        'position_grade' => 'Position & Grade',
        'phone' => 'Phone Number',
        'division_unit' => 'Division/Unit',
        'purpose' => 'Purpose of Application',
        'location' => 'Location',
        'loan_start_date' => 'Loan Date',
        'loan_end_date' => 'Expected Return Date',
        'is_responsible_officer' => 'Check ✓ if Applicant is the Responsible Officer. This section only needs to be filled if the Responsible Officer is not the Applicant.',
        'responsible_officer_name' => 'Full Name',
        'date' => 'Date',
        'signature' => 'Signature & Stamp',
        'approval_status' => 'Approval Status',
        'submission_date' => 'Application Date',
        'accept_terms' => 'I agree to the terms and conditions',
        'loan_period' => 'Loan Period',
        'total_equipment' => 'Total Equipment',
    ],

    'placeholders' => [
        'applicant_name' => 'Enter your full name',
        'position' => 'Example: Administrative Officer N41',
        'phone' => 'Example: 03-12345678',
        'select_division' => 'Select division/unit',
        'purpose' => 'Example: Tourism Unit Conference in Kuala Lumpur',
        'location' => 'Example: MOTAC Ministry Auditorium, Putrajaya',
        'responsible_officer_name' => 'Enter responsible officer name',
        'select_equipment' => 'Select equipment type',
        'quantity' => '1',
        'notes' => 'Additional notes (if any)',
        'signature' => 'Full name',
    ],

    'table' => [
        'no' => 'No.',
        'equipment_type' => 'Equipment Type',
        'quantity' => 'Quantity',
        'notes' => 'Notes',
    ],

    'actions' => [
        'previous' => 'Previous',
        'next' => 'Next',
        'submit_application' => 'Submit Application',
        'add_equipment' => 'Add Equipment',
        'remove_equipment' => 'Remove Equipment',
    ],

    'status' => [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'under_review' => 'Under Review',
        'pending_info' => 'Pending Information',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'ready_issuance' => 'Ready for Issuance',
        'issued' => 'Issued',
        'in_use' => 'In Use',
        'return_due' => 'Return Due Soon',
        'returning' => 'Returning',
        'returned' => 'Returned',
        'completed' => 'Completed',
        'overdue' => 'Overdue',
        'maintenance_required' => 'Maintenance Required',
        // Legacy statuses (for backwards compatibility)
        'pending_approval' => 'Pending Approval',
        'in_progress' => 'In Progress',
    ],

    'help' => [
        'need_assistance' => 'Need Assistance?',
        'contact_info' => 'For any inquiries, please contact:',
        'email' => 'bpm@motac.gov.my',
        'phone' => '03-2161 2345',
        'if_applicable' => 'if applicable',
        'is_responsible_officer' => 'Check if you are the officer responsible for this equipment',
    ],

    'units' => [
        'items' => 'items',
    ],

    'messages' => [
        'application_submitted' => 'Your application has been successfully submitted. Application number: :application_number',
        'submission_failed' => 'Application submission failed. Please try again.',
        'not_provided' => 'Not provided',
        'info_from_profile' => 'This information is retrieved from your user profile.',
    ],

    'validation' => [
        'equipment_type_required' => 'Select an equipment type for each row before continuing.',
        'equipment_type_exists' => 'Choose a valid equipment category from the list.',
        'quantity_required' => 'Enter the quantity for every equipment item.',
        'quantity_integer' => 'Equipment quantities must be whole numbers.',
        'quantity_min' => 'Equipment quantity must be at least 1.',
    ],

    // Realistic purpose examples for forms and testing
    'purpose_examples' => [
        'conference' => 'Tourism Unit Conference in Kuala Lumpur',
        'training' => 'Human Resources Division staff training program',
        'presentation' => 'System development project presentation to senior management',
        'workshop' => 'Digital transformation workshop for technical staff',
        'meeting' => 'Monthly management meeting at Main Conference Room',
        'event' => 'Launch of Malaysia Tourism Campaign 2025',
        'fieldwork' => 'Field survey of tourism locations in Sabah and Sarawak',
    ],

    // Dashboard translations
    'dashboard' => [
        'title' => 'Asset Loan Dashboard',
        'description' => 'Manage your loan applications and assets',
        'active_loans' => 'Active Loans',
        'pending_applications' => 'Pending Applications',
        'overdue_items' => 'Overdue Items',
        'total_applications' => 'Total Applications',
        'quick_actions' => 'Quick Actions',
        'new_application' => 'New Application',
        'new_application_desc' => 'Apply for new asset loan',
        'view_history' => 'View History',
        'view_history_desc' => 'Check previous applications',
        'browse_assets' => 'Browse Assets',
        'browse_assets_desc' => 'View available assets',
        'tabs' => [
            'overview' => 'Overview',
            'active_loans' => 'Active Loans',
            'pending' => 'Pending Approval',
        ],
        'overview_text' => 'Welcome to your asset loan dashboard. Use the tabs above to manage your applications.',
        'loan_period' => 'Loan Period',
        'submitted' => 'Submitted',
        'no_active_loans' => 'No active loans at this time',
        'no_pending_applications' => 'No pending applications',
    ],
];
