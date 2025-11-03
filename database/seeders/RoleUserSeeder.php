<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Role User Seeder
 *
 * Seeds users with four roles (Staff, Approver, Admin, Superuser)
 * for testing the updated helpdesk module's hybrid architecture.
 *
 * Requirements: Requirement 3.1
 * Traceability: D03-FR-003.1, D04 §3.1
 */
class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have divisions and grades
        $division = Division::firstOrCreate(
            ['code' => 'ICT'],
            [
                'name_ms' => 'Bahagian Teknologi Maklumat',
                'name_en' => 'Information Technology Division',
                'description_ms' => 'Bahagian pengurusan teknologi maklumat',
                'description_en' => 'Information technology management division',
                'is_active' => true,
            ]
        );

        $gradeStaff = Grade::firstOrCreate(
            ['code' => 'N32'],
            [
                'name_ms' => 'Gred N32',
                'name_en' => 'Grade N32',
                'level' => 32,
                'can_approve_loans' => false,
            ]
        );

        $gradeApprover = Grade::firstOrCreate(
            ['code' => 'N44'],
            [
                'name_ms' => 'Gred N44',
                'name_en' => 'Grade N44',
                'level' => 44,
                'can_approve_loans' => true,
            ]
        );

        $gradeAdmin = Grade::firstOrCreate(
            ['code' => 'N48'],
            [
                'name_ms' => 'Gred N48',
                'name_en' => 'Grade N48',
                'level' => 48,
                'can_approve_loans' => true,
            ]
        );

        // Create Staff user
        User::firstOrCreate(
            ['email' => 'staff@motac.gov.my'],
            [
                'name' => 'Ahmad Staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'staff_id' => 'MOTAC001',
                'division_id' => $division->id,
                'grade_id' => $gradeStaff->id,
                'phone' => '03-12345678',
                'mobile' => '012-3456789',
                'is_active' => true,
            ]
        );

        // Create Approver user (Grade 41+)
        User::firstOrCreate(
            ['email' => 'approver@motac.gov.my'],
            [
                'name' => 'Siti Approver',
                'password' => Hash::make('password'),
                'role' => 'approver',
                'staff_id' => 'MOTAC002',
                'division_id' => $division->id,
                'grade_id' => $gradeApprover->id,
                'phone' => '03-12345679',
                'mobile' => '012-3456790',
                'is_active' => true,
            ]
        );

        // Create Admin user
        User::firstOrCreate(
            ['email' => 'admin@motac.gov.my'],
            [
                'name' => 'Kumar Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'staff_id' => 'MOTAC003',
                'division_id' => $division->id,
                'grade_id' => $gradeAdmin->id,
                'phone' => '03-12345680',
                'mobile' => '012-3456791',
                'is_active' => true,
            ]
        );

        // Create Superuser
        User::firstOrCreate(
            ['email' => 'superuser@motac.gov.my'],
            [
                'name' => 'Lee Superuser',
                'password' => Hash::make('password'),
                'role' => 'superuser',
                'staff_id' => 'MOTAC004',
                'division_id' => $division->id,
                'grade_id' => $gradeAdmin->id,
                'phone' => '03-12345681',
                'mobile' => '012-3456792',
                'is_active' => true,
            ]
        );

        // Create additional staff users for testing
        User::firstOrCreate(
            ['email' => 'staff2@motac.gov.my'],
            [
                'name' => 'Fatimah Staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'staff_id' => 'MOTAC005',
                'division_id' => $division->id,
                'grade_id' => $gradeStaff->id,
                'phone' => '03-12345682',
                'mobile' => '012-3456793',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff3@motac.gov.my'],
            [
                'name' => 'Raj Staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'staff_id' => 'MOTAC006',
                'division_id' => $division->id,
                'grade_id' => $gradeStaff->id,
                'phone' => '03-12345683',
                'mobile' => '012-3456794',
                'is_active' => true,
            ]
        );

        $this->command->info('✓ Created users with four roles (Staff, Approver, Admin, Superuser)');
    }
}
