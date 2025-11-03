<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Database Seeder
 *
 * Main seeder that orchestrates all database seeding operations
 * for the ICTServe application.
 *
 * Seeding Order:
 * 1. RoleUserSeeder - Creates users with four roles
 * 2. HelpdeskTicketSeeder - Creates sample tickets (guest + authenticated)
 * 3. CrossModuleIntegrationSeeder - Creates integration records
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding ICTServe database...');
        $this->command->newLine();

        // Seed users with four roles (Staff, Approver, Admin, Superuser)
        $this->command->info('👥 Seeding users with roles...');
        $this->call(RoleUserSeeder::class);
        $this->command->newLine();

        // Seed organizational divisions
        $this->command->info('🏢 Seeding divisions...');
        $this->call(DivisionSeeder::class);
        $this->command->newLine();

        // Seed asset categories
        $this->command->info('📁 Seeding asset categories...');
        $this->call(AssetCategorySeeder::class);
        $this->command->newLine();

        // Seed assets for testing
        $this->command->info('💻 Seeding assets...');
        $this->call(AssetSeeder::class);
        $this->command->newLine();

        // Seed loan module data (applications, items, transactions)
        $this->command->info('📋 Seeding loan module data...');
        $this->call(LoanModuleSeeder::class);
        $this->command->newLine();

        // Seed helpdesk tickets (guest and authenticated)
        $this->command->info('🎫 Seeding helpdesk tickets...');
        $this->call(HelpdeskTicketSeeder::class);
        $this->command->newLine();

        // Seed cross-module integration records
        $this->command->info('🔗 Seeding cross-module integrations...');
        $this->call(CrossModuleIntegrationSeeder::class);
        $this->command->newLine();

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('Test Credentials:');
        $this->command->info('  Staff:      staff@motac.gov.my / password');
        $this->command->info('  Approver:   approver@motac.gov.my / password');
        $this->command->info('  Admin:      admin@motac.gov.my / password');
        $this->command->info('  Superuser:  superuser@motac.gov.my / password');
    }
}
