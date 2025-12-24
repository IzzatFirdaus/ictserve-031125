<?php

declare(strict_types=1);

/**
 * Compliance Configuration
 *
 * Configuration for KRISA PPSM PKS compliance validation framework
 * per requirements.md and design.md specifications
 */

return [
    /*
    |--------------------------------------------------------------------------
    | PKS Policy Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for PKS (Polisi Keselamatan Siber) compliance validation
    | References: _reference/Polisi_PKS.md sections 5.2.1, 9.2.1, 4.2, 5.4.3
    |
    */
    'pks' => [
        'policy_file' => '_reference/Polisi_PKS.md',
        'required_sections' => [
            '5.2.1' => 'Accountability and Non-repudiation principles',
            '9.2.1' => 'Data transfer procedures and confidentiality protection',
            '4.2' => 'Data sovereignty and jurisdiction requirements',
            '5.4.3' => 'Password policy requirements (8 chars, 90-day expiry, 3 attempts)',
        ],
        'prohibited_terms' => [
            'guest mode',
            'guest access',
            'anonymous user',
            'true hybrid',
        ],
        'required_terms' => [
            'SSO authentication',
            'LDAP',
            'Active Directory',
            'Walk-in/Kiosk Mode',
        ],
        'data_sovereignty_terms' => [
            'MyGovCloud',
            'data sovereignty',
            'intranet-only',
            'local processing',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | PSPM Strategic Plan Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for PSPM (Pelan Strategik Pendigitalan MOTAC) compliance
    | References: _reference/Ringkasan_Eksekutif_PSPM.md
    |
    */
    'pspm' => [
        'policy_file' => '_reference/Ringkasan_Eksekutif_PSPM.md',
        'strategic_objectives' => [
            'MyGovCloud prioritization',
            'Digital transformation',
            'Intranet deployment',
            'Data residency compliance',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | KRISA Template Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for KRISA template compliance validation
    | References: docs/KRISA/OFFICIAL-TEMPLATE-AND-SAMPLES/
    |
    */
    'krisa' => [
        'template_directory' => 'docs/KRISA/OFFICIAL-TEMPLATE-AND-SAMPLES',
        'required_sections' => [
            'i. Keterangan Dokumen',
            'ii. Semakan dan Pengesahan Dokumen',
            'iii. Kawalan Dokumen',
            'iv. Kandungan',
            'v. Senarai Gambarajah',
            'vi. Senarai Jadual',
            'vii. Definisi dan Akronim',
            'viii. Sumber Rujukan',
        ],
        'required_headers' => [
            'NAMA AGENSI',
            'NAMA AGENSI INDUK',
            'TARIKH DOKUMEN',
            'VERSI DOKUMEN',
        ],
        'methodology_references' => [
            'D02' => ['Pemodelan Fungsi Bisnes [F1.3]'],
            'D03' => [
                'Pemodelan Fungsi Sistem [F2.2]',
                'Pemodelan Use Case [F2.1]',
                'Pemodelan Keperluan Data [F2.3]',
            ],
        ],
        'notation_format' => 'BF-ICT-XX-YY',
        'crud_indicators' => ['(C)', '(R)', '(U)', '(D)'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for KRISA document paths and target versions
    |
    */
    'documents' => [
        'paths' => [
            'D01' => 'docs/KRISA/D01_KRISA_ICTSERVE_PELAN_PEMBANGUNAN_SISTEM.md',
            'D02' => 'docs/KRISA/D02_KRISA_ICTSERVE_SPESIFIKASI_KEPERLUAN_BISNES.md',
            'D03' => 'docs/KRISA/D03_KRISA_ICTSERVE_SPESIFIKASI_KEPERLUAN_SISTEM.md',
            'D04' => 'docs/KRISA/D04_KRISA_ICTSERVE_SPESIFIKASI_REKABENTUK_SISTEM.md',
            'D05' => 'docs/KRISA/D05_KRISA_ICTSERVE_PELAN_MIGRASI_DATA.md',
            'D06' => 'docs/KRISA/D06_KRISA_ICTSERVE_SPESIFIKASI_MIGRASI_DATA.md',
            'D07' => 'docs/KRISA/D07_KRISA_ICTSERVE_PELAN_INTEGRASI_SISTEM.md',
            'D08' => 'docs/KRISA/D08_KRISA_ICTSERVE_SPESIFIKASI_INTEGRASI_DATA.md',
            'D09' => 'docs/KRISA/D09_KRISA_ICTSERVE_DOKUMENTASI_PANGKALAN_DATA.md',
            'D10' => 'docs/KRISA/D10_KRISA_ICTSERVE_DOKUMENTASI_KOD_SUMBER.md',
            'D15' => 'docs/KRISA/D15_KRISA_ICTSERVE_LAPORAN_MIGRASI_DATA.md',
            'D17' => 'docs/KRISA/D17_KRISA_ICTSERVE_MANUAL_PENGGUNA_SISTEM.md',
        ],
        'target_versions' => [
            'D01' => '2.0', // Major compliance update
            'D02' => '4.0', // Major compliance update
            'D03' => '3.0', // Major compliance update
            'D04' => '3.0', // Major compliance update
            'D05' => '2.0',
            'D06' => '2.0',
            'D07' => '2.0',
            'D08' => '2.0',
            'D09' => '2.0',
            'D10' => '2.0',
            'D15' => '2.0',
            'D17' => '2.0',
        ],
        'expected_references' => [
            'D01' => ['D02', 'D03', 'D04', 'D17'], // Development plan references subsequent documents
            'D02' => ['D03'], // BRS requirements traced to SRS specifications
            'D03' => ['D04'], // SRS specifications linked to design
            'D04' => ['D09'], // Design specifications linked to database documentation
            'D17' => ['D01', 'D02', 'D03', 'D04'], // User manuals reflect all system changes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for validation behavior and reporting
    |
    */
    'validation' => [
        'output_directory' => 'storage/compliance',
        'backup_directory' => 'storage/compliance/backups',
        'report_formats' => ['txt', 'json'],
        'severity_levels' => [
            'critical' => ['pks_violations', 'security_violations'],
            'high' => ['template_violations', 'missing_references'],
            'medium' => ['version_mismatches', 'cross_reference_errors'],
            'low' => ['formatting_issues', 'minor_inconsistencies'],
        ],
        'auto_backup' => true,
        'max_backup_files' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance Thresholds
    |--------------------------------------------------------------------------
    |
    | Thresholds for compliance scoring and pass/fail criteria
    |
    */
    'thresholds' => [
        'pks_compliance_minimum' => 100, // Must be 100% for security compliance
        'template_compliance_minimum' => 95, // Allow minor formatting issues
        'cross_reference_accuracy' => 98, // High accuracy required for traceability
        'version_consistency' => 100, // Must be 100% for document management
    ],

    /*
    |--------------------------------------------------------------------------
    | Agency Information
    |--------------------------------------------------------------------------
    |
    | Standard agency information for KRISA documents
    |
    */
    'agency' => [
        'nama_agensi' => 'BPM (Bahagian Pengurusan Maklumat)',
        'nama_agensi_induk' => 'MOTAC (Kementerian Pelancongan, Seni dan Budaya)',
        'system_name' => 'ICTServe',
        'deployment_context' => 'Intranet-only deployment with mandatory authentication',
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance Change Log Template
    |--------------------------------------------------------------------------
    |
    | Standard change description for compliance updates
    |
    */
    'change_log' => [
        'compliance_update_description' => 'PKS/PSPM compliance update - Eliminated guest access violations per PKS 5.2.1, implemented data sovereignty controls per PKS 9.2.1 & 4.2, added KRISA template compliance, updated HRMIS integration requirements, and enhanced security documentation per PKS 5.4.3',
        'default_author' => 'BPM Compliance Team',
        'change_categories' => [
            'security' => 'Security compliance update',
            'template' => 'KRISA template compliance update',
            'reference' => 'Cross-reference consistency update',
            'version' => 'Version control standardization',
        ],
    ],
];
