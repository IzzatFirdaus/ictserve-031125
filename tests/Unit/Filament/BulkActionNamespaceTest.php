<?php

declare(strict_types=1);

namespace Tests\Unit\Filament;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BulkActionNamespaceTest extends TestCase
{
    #[Test]
    public function filament_v4_bulk_action_classes_exist(): void
    {
        $this->assertTrue(class_exists(\Filament\Actions\BulkActionGroup::class));
        $this->assertTrue(class_exists(\Filament\Actions\BulkAction::class));
    }

    #[Test]
    public function filament_tables_bulk_action_group_does_not_exist_in_v4(): void
    {
        $this->assertFalse(class_exists('Filament\\Tables\\Actions\\BulkActionGroup'));
    }
}
