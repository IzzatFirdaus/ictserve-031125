<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add foreign key constraints for cross-module integration.
     *
     * @see D03-FR-003.1 Cross-module integration requirements
     * @see D04 §6.1 Referential integrity design
     */
    public function up(): void
    {
        // Helpdesk Tickets - Asset relationship
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            if (! $this->foreignKeyExists('helpdesk_tickets', 'helpdesk_tickets_asset_id_foreign')) {
                $table->foreign('asset_id')
                    ->references('id')
                    ->on('assets')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }
        });

        // Loan Items - Asset relationship
        Schema::table('loan_items', function (Blueprint $table) {
            if (! $this->foreignKeyExists('loan_items', 'loan_items_asset_id_foreign')) {
                $table->foreign('asset_id')
                    ->references('id')
                    ->on('assets')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
            }
        });

        // Cross Module Integrations - Source relationships
        Schema::table('cross_module_integrations', function (Blueprint $table) {
            // Note: We use polymorphic relationships, so we don't add foreign keys for source_id and target_id
            // as they can reference different tables based on source_module and target_module
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys in reverse order
        Schema::table('loan_items', function (Blueprint $table) {
            if ($this->foreignKeyExists('loan_items', 'loan_items_asset_id_foreign')) {
                $table->dropForeign(['asset_id']);
            }
        });

        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            if ($this->foreignKeyExists('helpdesk_tickets', 'helpdesk_tickets_asset_id_foreign')) {
                $table->dropForeign(['asset_id']);
            }
        });
    }

    /**
     * Check if a foreign key exists
     */
    private function foreignKeyExists(string $table, string $foreignKey): bool
    {
        $connection = Schema::getConnection();
        $schemaManager = $connection->getDoctrineSchemaManager();
        $foreignKeys = $schemaManager->listTableForeignKeys($table);

        foreach ($foreignKeys as $key) {
            if ($key->getName() === $foreignKey) {
                return true;
            }
        }

        return false;
    }
};
