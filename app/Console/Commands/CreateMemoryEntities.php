<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\MemoryGraphService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CreateMemoryEntities extends Command
{
    protected $signature = 'memory:create-theme-entities';

    protected $description = 'Create theme toggle entities and relations using MemoryGraphService';

    public function handle(MemoryGraphService $memoryGraph): int
    {
        $this->info('Creating theme-related memory entities...');

        $theme = $memoryGraph->createEntity([
            'name' => 'Theme_Toggle_Implementation_2025-12-08',
            'entity_type' => 'technical_implementation',
            'summary' => 'Phase 1: Implemented theme toggle (light default; dark optional).',
            'source' => 'code-change',
            'discovered_at' => Carbon::now(),
        ]);

        $memoryGraph->recordObservation($theme, [
            'content' => 'Removed system theme option; default light; BM labels; persisted to localStorage; inline theme-init for FOUT prevention',
            'content_hash' => sha1('Removed system theme option; default light'),
            'confidence' => 0.9,
        ]);

        $script = $memoryGraph->createEntity([
            'name' => 'Theme_Init_Script_Added_2025-12-08',
            'entity_type' => 'technical_implementation',
            'summary' => 'Inline theme-init added to apply saved theme before render to avoid FOUT',
            'source' => 'code-change',
            'discovered_at' => Carbon::now(),
        ]);

        $memoryGraph->createRelation($theme, $script, ['relation_type' => 'implements']);

        $portalFix = $memoryGraph->createEntity([
            'name' => 'Portal_Dark_Class_Removal_2025-12-08',
            'entity_type' => 'solved_issue',
            'summary' => 'Removed forced dark class from portal layout; now follows saved preference',
            'source' => 'code-change',
            'discovered_at' => Carbon::now(),
        ]);

        $memoryGraph->createRelation($portalFix, $theme, ['relation_type' => 'related_to']);

        $session = $memoryGraph->createEntity([
            'name' => 'Theme_Toggle_Session_2025-12-08',
            'entity_type' => 'work_session',
            'summary' => 'Work session: Theme toggle & theme-init script Phase 1; pending page-level dark mode and WCAG validation',
            'source' => 'memory:create-theme-entities',
            'discovered_at' => Carbon::now(),
        ]);

        $memoryGraph->createRelation($session, $theme, ['relation_type' => 'documents']);

        $this->info('Memory entities and relations created.');

        return 0;
    }
}
