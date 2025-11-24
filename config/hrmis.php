<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HRMIS Grade Configuration
    |--------------------------------------------------------------------------
    |
    | Grade mappings for MOTAC approval matrix and organizational hierarchy.
    |
    | @see D03-FR-002.1 Grade-based approval matrix
    |
    */

    'officer_grades' => ['54', '52', '48', '44', '41'],
    'jusa_grades' => ['JUSA A', 'JUSA B', 'JUSA C'],
    'executive_grades' => ['PTK', 'KSU'],

    /*
    |--------------------------------------------------------------------------
    | Approval Matrix Configuration
    |--------------------------------------------------------------------------
    |
    | Asset value thresholds and grade-based approval routing.
    |
    */

    'asset_value_threshold' => 5000,
    'grade_thresholds' => [
        'max_officer' => 54,
        'min_senior' => 52,
        'max_senior' => 48,
        'min_manager' => 44,
        'max_manager' => 41,
        'min_executive' => 40,
    ],
    'approver_grades' => [
        'junior' => '41',
        'senior' => '44',
        'manager' => '48',
        'executive' => 'JUSA',
    ],

];
