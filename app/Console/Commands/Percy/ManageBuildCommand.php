<?php

declare(strict_types=1);

namespace App\Console\Commands\Percy;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;

/**
 * Percy Build Management Command
 *
 * Manages Percy builds for ICTServe v3.6.1 visual testing integration.
 * Provides build creation, status checking, and finalization capabilities.
 *
 * @version 3.6.1
 */
class ManageBuildCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'percy:manage-build 
                            {action : Action to perform: create, status, finalize, list, delete}
                            {--build-id= : Specific build ID for status/finalize/delete actions}
                            {--name= : Build name for create action}
                            {--branch= : Branch name for the build}
                            {--commit= : Commit SHA for the build}
                            {--limit=10 : Number of builds to list}
                            {--force : Force action without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage Percy builds for ICTServe v3.6.1 visual testing';

    /**
     * Percy API base URL
     */
    private const PERCY_API_BASE = 'https://percy.io/api/v1';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        // Validate Percy configuration
        if (! $this->validatePercyConfiguration()) {
            return 1;
        }

        return match ($action) {
            'create' => $this->createBuild(),
            'status' => $this->getBuildStatus(),
            'finalize' => $this->finalizeBuild(),
            'list' => $this->listBuilds(),
            'delete' => $this->deleteBuild(),
            default => $this->handleInvalidAction($action),
        };
    }

    /**
     * Validate Percy configuration
     */
    private function validatePercyConfiguration(): bool
    {
        $token = Config::get('percy.token');
        if (! $token) {
            $this->error('Token Percy tidak ditemui. Sila tetapkan PERCY_TOKEN dalam pembolehubah persekitaran.');

            return false;
        }

        $project = Config::get('percy.project');
        if (! $project) {
            $this->error('Nama projek Percy tidak ditemui. Sila tetapkan PERCY_PROJECT.');

            return false;
        }

        return true;
    }

    /**
     * Create a new Percy build
     */
    private function createBuild(): int
    {
        $this->info('🏗️ Mencipta build Percy baharu...');

        $buildName = $this->option('name') ?? $this->generateBuildName();
        $branch = $this->option('branch') ?? Config::get('percy.branch', 'develop');
        $commit = $this->option('commit') ?? $this->getCurrentCommitSha();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token '.Config::get('percy.token'),
                'Content-Type' => 'application/json',
            ])->post(self::PERCY_API_BASE.'/builds', [
                'data' => [
                    'type' => 'builds',
                    'attributes' => [
                        'branch' => $branch,
                        'target-branch' => Config::get('percy.target_branch', 'develop'),
                        'target-commit-sha' => $commit,
                        'commit-sha' => $commit,
                        'pull-request-number' => null,
                        'parallel-nonce' => Config::get('percy.parallel_nonce'),
                        'parallel-total-shards' => Config::get('percy.parallel_total', 1),
                    ],
                    'relationships' => [
                        'project' => [
                            'data' => [
                                'type' => 'projects',
                                'id' => Config::get('percy.project'),
                            ],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $build = $response->json()['data'];
                $buildId = $build['id'];
                $buildNumber = $build['attributes']['build-number'];
                $webUrl = $build['attributes']['web-url'];

                $this->info('✅ Build Percy berjaya dicipta:');
                $this->info("   ID Build: {$buildId}");
                $this->info("   Nombor Build: {$buildNumber}");
                $this->info("   Cawangan: {$branch}");
                $this->info("   Commit: {$commit}");
                $this->info("   URL: {$webUrl}");

                // Store build info for later use
                $this->storeBuildInfo($buildId, $buildNumber, $webUrl);

                return 0;
            } else {
                $this->error('Gagal mencipta build Percy:');
                $this->error($response->body());

                return 1;
            }
        } catch (Exception $e) {
            $this->error('Ralat mencipta build Percy: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Get build status
     */
    private function getBuildStatus(): int
    {
        $buildId = $this->option('build-id');

        if (! $buildId) {
            $this->error('ID build diperlukan untuk memeriksa status. Gunakan --build-id=<id>');

            return 1;
        }

        $this->info("🔍 Memeriksa status build {$buildId}...");

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token '.Config::get('percy.token'),
            ])->get(self::PERCY_API_BASE."/builds/{$buildId}");

            if ($response->successful()) {
                $build = $response->json()['data'];
                $this->displayBuildInfo($build);

                return 0;
            } else {
                $this->error("Gagal mendapatkan status build {$buildId}:");
                $this->error($response->body());

                return 1;
            }
        } catch (Exception $e) {
            $this->error('Ralat mendapatkan status build: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Finalize a build
     */
    private function finalizeBuild(): int
    {
        $buildId = $this->option('build-id');

        if (! $buildId) {
            $this->error('ID build diperlukan untuk finalisasi. Gunakan --build-id=<id>');

            return 1;
        }

        if (! $this->option('force')) {
            if (! $this->confirm("Adakah anda pasti mahu menyelesaikan build {$buildId}?")) {
                $this->info('Finalisasi dibatalkan.');

                return 0;
            }
        }

        $this->info("🏁 Menyelesaikan build {$buildId}...");

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token '.Config::get('percy.token'),
                'Content-Type' => 'application/json',
            ])->post(self::PERCY_API_BASE."/builds/{$buildId}/finalize");

            if ($response->successful()) {
                $this->info("✅ Build {$buildId} berjaya diselesaikan");

                // Get final build status
                $this->getBuildStatus();

                return 0;
            } else {
                $this->error("Gagal menyelesaikan build {$buildId}:");
                $this->error($response->body());

                return 1;
            }
        } catch (Exception $e) {
            $this->error('Ralat menyelesaikan build: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * List recent builds
     */
    private function listBuilds(): int
    {
        $this->info('📋 Mendapatkan senarai build Percy...');

        $limit = $this->option('limit', 10);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token '.Config::get('percy.token'),
            ])->get(self::PERCY_API_BASE.'/builds', [
                'project' => Config::get('percy.project'),
                'limit' => $limit,
            ]);

            if ($response->successful()) {
                $builds = $response->json()['data'];

                if (empty($builds)) {
                    $this->info('Tiada build ditemui.');

                    return 0;
                }

                $this->displayBuildsTable($builds);

                return 0;
            } else {
                $this->error('Gagal mendapatkan senarai build:');
                $this->error($response->body());

                return 1;
            }
        } catch (Exception $e) {
            $this->error('Ralat mendapatkan senarai build: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Delete a build
     */
    private function deleteBuild(): int
    {
        $buildId = $this->option('build-id');

        if (! $buildId) {
            $this->error('ID build diperlukan untuk pemadaman. Gunakan --build-id=<id>');

            return 1;
        }

        if (! $this->option('force')) {
            if (! $this->confirm("Adakah anda pasti mahu memadam build {$buildId}? Tindakan ini tidak boleh dibatalkan.")) {
                $this->info('Pemadaman dibatalkan.');

                return 0;
            }
        }

        $this->warn("🗑️ Memadam build {$buildId}...");

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token '.Config::get('percy.token'),
            ])->delete(self::PERCY_API_BASE."/builds/{$buildId}");

            if ($response->successful()) {
                $this->info("✅ Build {$buildId} berjaya dipadam");

                return 0;
            } else {
                $this->error("Gagal memadam build {$buildId}:");
                $this->error($response->body());

                return 1;
            }
        } catch (Exception $e) {
            $this->error('Ralat memadam build: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Handle invalid action
     */
    private function handleInvalidAction(string $action): int
    {
        $this->error("Tindakan tidak sah: {$action}");
        $this->info('Tindakan yang tersedia: create, status, finalize, list, delete');

        return 1;
    }

    /**
     * Display build information
     */
    private function displayBuildInfo(array $build): void
    {
        $attributes = $build['attributes'];

        $this->info('📊 Maklumat Build:');
        $this->newLine();

        $this->table(
            ['Atribut', 'Nilai'],
            [
                ['ID Build', $build['id']],
                ['Nombor Build', $attributes['build-number']],
                ['Status', $this->formatBuildState($attributes['state'])],
                ['Cawangan', $attributes['branch']],
                ['Commit SHA', $attributes['commit-sha'] ?? 'N/A'],
                ['Jumlah Tangkapan', $attributes['total-snapshots'] ?? 0],
                ['Jumlah Perbandingan', $attributes['total-comparisons'] ?? 0],
                ['Perubahan Visual', $attributes['total-comparisons-diff'] ?? 0],
                ['Dicipta', $this->formatDate($attributes['created-at'])],
                ['Selesai', $attributes['finished-at'] ? $this->formatDate($attributes['finished-at']) : 'Belum selesai'],
                ['URL Web', $attributes['web-url']],
            ]
        );
    }

    /**
     * Display builds table
     */
    private function displayBuildsTable(array $builds): void
    {
        $this->info("📋 {count($builds)} Build Terkini:");
        $this->newLine();

        $rows = [];
        foreach ($builds as $build) {
            $attributes = $build['attributes'];
            $rows[] = [
                $build['id'],
                $attributes['build-number'],
                $this->formatBuildState($attributes['state']),
                $attributes['branch'],
                $attributes['total-snapshots'] ?? 0,
                $attributes['total-comparisons-diff'] ?? 0,
                $this->formatDate($attributes['created-at']),
            ];
        }

        $this->table(
            ['ID', 'Nombor', 'Status', 'Cawangan', 'Tangkapan', 'Perubahan', 'Dicipta'],
            $rows
        );
    }

    /**
     * Format build state for display
     */
    private function formatBuildState(string $state): string
    {
        return match ($state) {
            'pending' => '⏳ Menunggu',
            'processing' => '🔄 Memproses',
            'finished' => '✅ Selesai',
            'failed' => '❌ Gagal',
            'expired' => '⏰ Tamat Tempoh',
            default => $state,
        };
    }

    /**
     * Format date for display
     */
    private function formatDate(string $date): string
    {
        return date('Y-m-d H:i:s', strtotime($date));
    }

    /**
     * Generate build name
     */
    private function generateBuildName(): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $branch = $this->option('branch') ?? Config::get('percy.branch', 'develop');
        $project = Config::get('percy.project', 'ictserve');

        return "{$project}-{$branch}-{$timestamp}";
    }

    /**
     * Get current commit SHA
     */
    private function getCurrentCommitSha(): string
    {
        try {
            $result = Process::run('git rev-parse HEAD');

            return $result->successful() ? trim($result->output()) : 'unknown';
        } catch (Exception $e) {
            return 'unknown';
        }
    }

    /**
     * Store build info for later use
     */
    private function storeBuildInfo(string $buildId, int $buildNumber, string $webUrl): void
    {
        $buildInfo = [
            'id' => $buildId,
            'number' => $buildNumber,
            'url' => $webUrl,
            'created_at' => now()->toISOString(),
        ];

        file_put_contents(
            storage_path('percy_build_info.json'),
            json_encode($buildInfo, JSON_PRETTY_PRINT)
        );
    }
}
