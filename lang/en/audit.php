<?php

return [
    'retention' => [
        'title' => 'Data Retention Policy',
        'description' => 'This system retains audit trail logs to meet legal and compliance requirements. Audit logs are preserved for a period of 7 years by default. After the retention period, logs are archived or anonymized in accordance with organizational policies and PDPA/privacy requirements.',
        'docs' => 'For more details, see the :requirements and :design documentation.',
        'docs_requirements' => 'Software Requirements',
        'docs_design' => 'Technical Design',
        'note' => 'Only users with the appropriate admin or compliance roles may access or export audit logs. Review your organization\'s retention schedule for additional constraints.',
        'actions' => [
            'export_all' => 'Export All',
            'retention_policy' => 'Retention Policy',
            'security_summary' => 'Security Summary',
        ],
        'modals' => [
            'export' => [
                'heading' => 'Export Audit Logs',
                'description' => 'Export audit logs with the selected criteria. Large exports may take several minutes.',
            ],
        ],
    ],
];
