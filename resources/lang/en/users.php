<?php

declare(strict_types=1);

return [
    'full_name' => 'Full name',
    'email_address' => 'Email address',
    'password' => 'Password', // Translation label only, not a credential
    'role' => 'Role',
    'status' => 'Status',
    'active_status' => 'Active Status',
    'staff_id' => 'Staff ID',
    'division' => 'Division',
    'grade' => 'Grade',
    'position' => 'Position',
    'office_phone' => 'Office Phone',
    'mobile_phone' => 'Mobile Phone',
    'role_staff' => 'Staff',
    'role_approver' => 'Approver (Grade '.config('app.min_approver_grade_level', 41).'+)',
    'role_admin' => 'Admin',
    'role_superuser' => 'Superuser',
    'status_active' => 'Active',
    'status_inactive' => 'Inactive',
];
