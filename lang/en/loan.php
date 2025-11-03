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
        'purpose' => 'State the purpose of equipment loan',
        'location' => 'State the location of use',
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
        'pending_approval' => 'Pending Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
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
    ],
];
