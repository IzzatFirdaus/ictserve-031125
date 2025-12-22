<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MemoryAdapter;
use App\Models\MemoryEntity;
use App\Services\MemoryGraphService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class SyncMemoryMarkdown extends Command
{
    protected $signature = 'memory:sync-markdown {--path=* : Path globs or specific markdown files to scan (doc root by default)} {--delete : Delete files after successful import}';

    protected $description = 'Scan markdown files and import them into the Memory Graph as entities + observations.';

    public function handle(MemoryGraphService $memoryGraph): int
    {
        $this->info('Starting memory sync of markdown files...');

        $pathOption = $this->option('path');
        $paths = is_array($pathOption) && ! empty($pathOption)
            ? $pathOption
            : [base_path('docs'), base_path('.agents'), base_path('.github'), base_path('.')];
        $paths = array_map('strval', $paths);

        $fs = new Filesystem;
        $adapter = MemoryAdapter::firstOrCreate([
            'provider' => 'local',
            'name' => 'local-md-sync',
        ], [
            'config' => ['paths' => $paths],
            'is_active' => true,
        ]);

        $synced = 0;

        foreach ($paths as $path) {
            // accept both file path and directory with glob
            $files = [];
            if ($fs->exists($path) && $fs->isFile($path) && Str::endsWith($path, '.md')) {
                $files[] = $path;
            } else {
                $pattern = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'*.md';
                $files = glob($pattern) ?: [];
            }
            foreach ($files as $md) {
                if (! $fs->exists($md)) {
                    continue;
                }

                $contents = $fs->get($md);
                // Normalize encoding: convert UTF-16/LE/BE to UTF-8 and strip BOMs so DB storage doesn't fail
                $contents = $this->normalizeEncoding($contents);
                $title = $this->extractTitle($contents) ?: pathinfo($md, PATHINFO_FILENAME);
                $summary = $this->extractSummary($contents);

                // entity type heuristics
                $entityType = Str::contains($md, ['docs', 'D00_', 'D04_']) ? 'canonical_document' : 'analysis_work';

                $entity = MemoryEntity::firstOrCreate([
                    'name' => $title,
                ], [
                    'entity_type' => $entityType,
                    'summary' => $summary,
                    'source' => 'file',
                    'source_identifier' => $md,
                    'discovered_at' => Carbon::now(),
                ]);

                // record observation
                $memoryGraph->recordObservation($entity, [
                    'content' => $contents,
                    'content_hash' => sha1($contents),
                    'memory_adapter_id' => $adapter->id,
                    'confidence' => 0.9,
                    'metadata' => ['imported_from' => $md],
                ]);

                $synced++;
                $this->info("Synced: $md -> {$entity->name}");

                if ($this->option('delete')) {
                    try {
                        $fs->delete($md);
                        $this->info("Deleted source file: $md");
                    } catch (\Throwable $e) {
                        $this->error("Failed to delete $md: {$e->getMessage()}");
                    }
                }
            }
        }

        // record adapter sync
        $memoryGraph->recordAdapterSync($adapter, [
            'status' => 'completed',
            'synced_entities' => $synced,
            'finished_at' => Carbon::now(),
        ]);

        // Create a work_session entity to record this import action for traceability
        $workSession = $memoryGraph->createEntity([
            'name' => 'Session_'.now()->format('Y-m-d_His').'_AutoMarkdownImport',
            'entity_type' => 'work_session',
            'summary' => "Auto import of {$synced} markdown files",
            'source' => 'memory:sync-markdown',
            'source_identifier' => 'local-md-sync',
            'discovered_at' => Carbon::now(),
        ]);

        // Link this work session to the MCP export artifact if present
        $mcp = MemoryEntity::where('name', 'MCP_MemoryServer_Complete_Export_2025-11-08')->first();

        if ($mcp) {
            $memoryGraph->createRelation($workSession, $mcp, ['relation_type' => 'documents']);
        }

        // Append a portable backup to memory.jsonl for auditability and portability
        try {
            $backupPath = base_path('memory.jsonl');
            $append = json_encode([
                'name' => $workSession->name,
                'entityType' => $workSession->entity_type,
                'observations' => [
                    "Auto import: {$synced} files from memory:sync-markdown",
                ],
                'relations' => $mcp ? [['relationType' => 'documents', 'to' => $mcp->name]] : [],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            file_put_contents($backupPath, $append.PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            $this->error('Failed to append to memory.jsonl: '.$e->getMessage());
        }

        $this->info("Memory sync completed: {$synced} files indexed.");

        return 0;
    }

    protected function extractTitle(string $contents): ?string
    {
        if (preg_match('/^#\s+(.+)$/m', $contents, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    protected function extractSummary(string $contents): ?string
    {
        // first non-empty paragraph
        $parts = preg_split('/\R\s*\R/', trim($contents));

        if (! is_array($parts) || empty($parts[0])) {
            return null;
        }

        // remove title if included
        $summary = preg_replace('/^# .*$/m', '', $parts[0]) ?? '';

        return trim(Str::limit(strip_tags($summary), 320));
    }

    /**
     * Normalize file content encoding to UTF-8 and remove BOMs.
     */
    protected function normalizeEncoding(string $contents): string
    {
        // strip UTF-8 BOM if present
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents);

        // Detect common encodings (UTF-8, UTF-16LE, UTF-16BE). If not UTF-8, attempt conversion.
        $encoding = mb_detect_encoding($contents ?? '', ['UTF-8', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1'], true);

        if ($encoding !== false && $encoding !== 'UTF-8') {
            $converted = mb_convert_encoding($contents ?? '', 'UTF-8', $encoding);
            if ($converted !== false) {
                $contents = $converted;
            }
        }

        // Ensure valid UTF-8
        $validated = mb_convert_encoding($contents ?? '', 'UTF-8', 'UTF-8');
        if ($validated !== false) {
            $contents = $validated;
        }

        return $contents;
    }
}
