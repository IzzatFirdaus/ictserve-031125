<?php

declare(strict_types=1);

namespace App\Mcp;

use App\Services\BrowserStackMcpService;
use Illuminate\Support\Facades\Log;
use Laravel\Mcp\Server;
use Laravel\Mcp\Tool;
use Laravel\Mcp\Resource;

/**
 * BrowserStack MCP Server for ICTServe v3.6.1
 * 
 * Provides MCP tools for BrowserStack integration with Percy visual testing.
 * Enables natural language commands for cross-platform testing, accessibility
 * validation, and visual debugging through BrowserStack's comprehensive platform.
 */
class BrowserStackMcpServer extends Server
{
    private BrowserStackMcpService $browserStackService;

    public function __construct()
    {
        $this->browserStackService = new BrowserStackMcpService();
    }

    /**
     * Register MCP tools for BrowserStack integration
     */
    public function tools(): array
    {
        return [
            Tool::make('browserstack_validate_config')
                ->description('Validate BrowserStack configuration and credentials')
                ->handler(function () {
                    return $this->validateConfiguration();
                }),

            Tool::make('browserstack_get_browsers')
                ->description('Get available browsers and devices for testing')
                ->handler(function () {
                    return $this->getAvailableBrowsers();
                }),

            Tool::make('browserstack_create_percy_session')
                ->description('Create BrowserStack session for Percy visual testing')
                ->parameters([
                    'capabilities' => [
                        'type' => 'object',
                        'description' => 'Browser/device capabilities for the session',
                        'properties' => [
                            'browser' => ['type' => 'string', 'description' => 'Browser name (Chrome, Firefox, Safari, Edge)'],
                            'browser_version' => ['type' => 'string', 'description' => 'Browser version (latest, specific version)'],
                            'os' => ['type' => 'string', 'description' => 'Operating system (Windows, OS X, Android, iOS)'],
                            'os_version' => ['type' => 'string', 'description' => 'OS version'],
                            'device' => ['type' => 'string', 'description' => 'Mobile device name (optional)'],
                            'resolution' => ['type' => 'string', 'description' => 'Screen resolution (desktop only)'],
                        ],
                    ],
                ])
                ->handler(function (array $capabilities = []) {
                    return $this->createPercySession($capabilities);
                }),

            Tool::make('browserstack_run_percy_visual_test')
                ->description('Execute Percy visual test across multiple browsers/devices')
                ->parameters([
                    'test_config' => [
                        'type' => 'object',
                        'description' => 'Configuration for Percy visual test execution',
                        'properties' => [
                            'browsers' => [
                                'type' => 'array',
                                'description' => 'List of browser/device configurations to test',
                                'items' => ['type' => 'object'],
                            ],
                            'snapshots' => [
                                'type' => 'array',
                                'description' => 'List of snapshots to capture',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string', 'description' => 'Snapshot name'],
                                        'url' => ['type' => 'string', 'description' => 'URL to capture'],
                                        'widths' => ['type' => 'array', 'description' => 'Viewport widths'],
                                        'percy_css' => ['type' => 'string', 'description' => 'CSS overrides'],
                                    ],
                                ],
                            ],
                            'percy_options' => [
                                'type' => 'object',
                                'description' => 'Percy-specific options',
                                'properties' => [
                                    'widths' => ['type' => 'array', 'description' => 'Default viewport widths'],
                                    'min_height' => ['type' => 'integer', 'description' => 'Minimum snapshot height'],
                                    'percy_css' => ['type' => 'string', 'description' => 'Global CSS overrides'],
                                ],
                            ],
                        ],
                        'required' => ['browsers'],
                    ],
                ])
                ->handler(function (array $testConfig) {
                    return $this->executePercyVisualTest($testConfig);
                }),

            Tool::make('browserstack_accessibility_test')
                ->description('Run accessibility testing with Percy visual validation')
                ->parameters([
                    'test_config' => [
                        'type' => 'object',
                        'description' => 'Configuration for accessibility testing',
                        'properties' => [
                            'url' => ['type' => 'string', 'description' => 'URL to test for accessibility'],
                            'wcag_level' => ['type' => 'string', 'description' => 'WCAG compliance level (AA, AAA)', 'default' => 'AA'],
                            'capabilities' => ['type' => 'object', 'description' => 'Browser/device capabilities'],
                            'snapshot_name' => ['type' => 'string', 'description' => 'Percy snapshot name for compliance validation'],
                            'percy_options' => ['type' => 'object', 'description' => 'Percy snapshot options'],
                        ],
                        'required' => ['url'],
                    ],
                ])
                ->handler(function (array $testConfig) {
                    return $this->runAccessibilityTest($testConfig);
                }),

            Tool::make('browserstack_wcag_compliance_scan')
                ->description('Run comprehensive WCAG 2.2 AA compliance scan with Percy visual validation')
                ->parameters([
                    'test_config' => [
                        'type' => 'object',
                        'description' => 'Configuration for WCAG compliance scan',
                        'properties' => [
                            'url' => ['type' => 'string', 'description' => 'URL to scan for WCAG compliance'],
                            'wcag_level' => ['type' => 'string', 'description' => 'WCAG compliance level (A, AA, AAA)', 'default' => 'AA'],
                            'capabilities' => ['type' => 'object', 'description' => 'Browser/device capabilities'],
                        ],
                        'required' => ['url'],
                    ],
                ])
                ->handler(function (array $testConfig) {
                    return $this->runWCAGComplianceScan($testConfig);
                }),

            Tool::make('browserstack_focus_state_validation')
                ->description('Validate focus state accessibility for interactive elements')
                ->parameters([
                    'test_config' => [
                        'type' => 'object',
                        'description' => 'Configuration for focus state validation',
                        'properties' => [
                            'url' => ['type' => 'string', 'description' => 'URL to validate focus states'],
                            'selectors' => [
                                'type' => 'array',
                                'description' => 'CSS selectors for focusable elements',
                                'items' => ['type' => 'string'],
                            ],
                            'capabilities' => ['type' => 'object', 'description' => 'Browser/device capabilities'],
                        ],
                        'required' => ['url'],
                    ],
                ])
                ->handler(function (array $testConfig) {
                    return $this->runFocusStateValidation($testConfig);
                }),

            Tool::make('browserstack_color_contrast_validation')
                ->description('Validate color contrast ratios for WCAG compliance')
                ->parameters([
                    'test_config' => [
                        'type' => 'object',
                        'description' => 'Configuration for color contrast validation',
                        'properties' => [
                            'url' => ['type' => 'string', 'description' => 'URL to validate color contrast'],
                            'wcag_level' => ['type' => 'string', 'description' => 'WCAG compliance level (AA, AAA)', 'default' => 'AA'],
                            'capabilities' => ['type' => 'object', 'description' => 'Browser/device capabilities'],
                        ],
                        'required' => ['url'],
                    ],
                ])
                ->handler(function (array $testConfig) {
                    return $this->runColorContrastValidation($testConfig);
                }),

            Tool::make('browserstack_keyboard_navigation_test')
                ->description('Test keyboard navigation accessibility')
                ->parameters([
                    'test_config' => [
                        'type' => 'object',
                        'description' => 'Configuration for keyboard navigation testing',
                        'properties' => [
                            'url' => ['type' => 'string', 'description' => 'URL to test keyboard navigation'],
                            'tab_count' => ['type' => 'integer', 'description' => 'Number of tab presses to test', 'default' => 10],
                            'capabilities' => ['type' => 'object', 'description' => 'Browser/device capabilities'],
                        ],
                        'required' => ['url'],
                    ],
                ])
                ->handler(function (array $testConfig) {
                    return $this->runKeyboardNavigationTest($testConfig);
                }),

            Tool::make('browserstack_create_live_session')
                ->description('Create BrowserStack Live session for visual debugging')
                ->parameters([
                    'capabilities' => [
                        'type' => 'object',
                        'description' => 'Browser/device capabilities for live session',
                        'properties' => [
                            'browser' => ['type' => 'string', 'description' => 'Browser name'],
                            'browser_version' => ['type' => 'string', 'description' => 'Browser version'],
                            'os' => ['type' => 'string', 'description' => 'Operating system'],
                            'os_version' => ['type' => 'string', 'description' => 'OS version'],
                            'device' => ['type' => 'string', 'description' => 'Mobile device name (optional)'],
                        ],
                    ],
                ])
                ->handler(function (array $capabilities = []) {
                    return $this->createLiveSession($capabilities);
                }),

            Tool::make('browserstack_capture_live_screenshot')
                ->description('Capture screenshot during BrowserStack Live session')
                ->parameters([
                    'session_id' => [
                        'type' => 'string',
                        'description' => 'Live session ID',
                    ],
                    'name' => [
                        'type' => 'string',
                        'description' => 'Screenshot name',
                    ],
                    'options' => [
                        'type' => 'object',
                        'description' => 'Screenshot options',
                        'properties' => [
                            'description' => ['type' => 'string', 'description' => 'Screenshot description'],
                            'compare_with_percy' => ['type' => 'boolean', 'description' => 'Also capture Percy snapshot'],
                        ],
                    ],
                ])
                ->handler(function (string $sessionId, string $name, array $options = []) {
                    return $this->captureLiveSessionScreenshot($sessionId, $name, $options);
                }),

            Tool::make('browserstack_capture_live_percy_snapshot')
                ->description('Capture Percy snapshot during Live session for visual comparison')
                ->parameters([
                    'session_id' => [
                        'type' => 'string',
                        'description' => 'Live session ID',
                    ],
                    'snapshot_name' => [
                        'type' => 'string',
                        'description' => 'Percy snapshot name',
                    ],
                    'options' => [
                        'type' => 'object',
                        'description' => 'Percy snapshot options',
                        'properties' => [
                            'widths' => ['type' => 'array', 'description' => 'Viewport widths', 'items' => ['type' => 'integer']],
                            'min_height' => ['type' => 'integer', 'description' => 'Minimum height'],
                            'percy_css' => ['type' => 'string', 'description' => 'Custom CSS for Percy'],
                        ],
                    ],
                ])
                ->handler(function (string $sessionId, string $snapshotName, array $options = []) {
                    return $this->captureLiveSessionPercySnapshot($sessionId, $snapshotName, $options);
                }),

            Tool::make('browserstack_create_visual_issue')
                ->description('Create visual issue report from Live session findings')
                ->parameters([
                    'session_id' => [
                        'type' => 'string',
                        'description' => 'Live session ID',
                    ],
                    'issue_data' => [
                        'type' => 'object',
                        'description' => 'Visual issue details',
                        'properties' => [
                            'title' => ['type' => 'string', 'description' => 'Issue title'],
                            'description' => ['type' => 'string', 'description' => 'Issue description'],
                            'severity' => ['type' => 'string', 'description' => 'Severity: critical, major, minor, cosmetic'],
                            'affected_pages' => ['type' => 'array', 'description' => 'Affected page URLs', 'items' => ['type' => 'string']],
                            'screenshots' => ['type' => 'array', 'description' => 'Screenshot IDs', 'items' => ['type' => 'string']],
                            'percy_snapshots' => ['type' => 'array', 'description' => 'Percy snapshot IDs', 'items' => ['type' => 'string']],
                        ],
                        'required' => ['title', 'severity'],
                    ],
                ])
                ->handler(function (string $sessionId, array $issueData) {
                    return $this->createVisualIssueReport($sessionId, $issueData);
                }),

            Tool::make('browserstack_start_collaborative_session')
                ->description('Start collaborative debugging session for visual issues')
                ->parameters([
                    'session_id' => [
                        'type' => 'string',
                        'description' => 'Live session ID',
                    ],
                    'owner_name' => [
                        'type' => 'string',
                        'description' => 'Name of the session owner',
                    ],
                ])
                ->handler(function (string $sessionId, string $ownerName) {
                    return $this->startCollaborativeSession($sessionId, $ownerName);
                }),

            Tool::make('browserstack_get_debugging_workflow')
                ->description('Get predefined debugging workflow for visual issues')
                ->parameters([
                    'workflow_type' => [
                        'type' => 'string',
                        'description' => 'Workflow type: percy_visual_regression, accessibility_compliance, cross_browser_consistency',
                    ],
                ])
                ->handler(function (string $workflowType) {
                    return $this->getDebuggingWorkflow($workflowType);
                }),

            Tool::make('browserstack_get_test_report')
                ->description('Get comprehensive test execution report combining BrowserStack and Percy results')
                ->parameters([
                    'build_id' => [
                        'type' => 'string',
                        'description' => 'BrowserStack build ID to generate report for',
                    ],
                ])
                ->handler(function (string $buildId) {
                    return $this->getTestExecutionReport($buildId);
                }),

            Tool::make('browserstack_run_cross_browser_test')
                ->description('Run cross-browser visual consistency test with Percy')
                ->parameters([
                    'test_config' => [
                        'type' => 'object',
                        'description' => 'Cross-browser test configuration',
                        'properties' => [
                            'url' => ['type' => 'string', 'description' => 'URL to test across browsers'],
                            'snapshot_name' => ['type' => 'string', 'description' => 'Base name for snapshots'],
                            'browsers' => [
                                'type' => 'array',
                                'description' => 'Browsers to test (default: Chrome, Firefox, Safari, Edge)',
                                'items' => ['type' => 'string'],
                            ],
                            'percy_options' => ['type' => 'object', 'description' => 'Percy snapshot options'],
                        ],
                        'required' => ['url', 'snapshot_name'],
                    ],
                ])
                ->handler(function (array $testConfig) {
                    return $this->runCrossBrowserTest($testConfig);
                }),

            Tool::make('browserstack_mobile_visual_test')
                ->description('Run mobile visual regression testing on real devices')
                ->parameters([
                    'test_config' => [
                        'type' => 'object',
                        'description' => 'Mobile visual test configuration',
                        'properties' => [
                            'url' => ['type' => 'string', 'description' => 'URL to test on mobile devices'],
                            'snapshot_name' => ['type' => 'string', 'description' => 'Base name for mobile snapshots'],
                            'devices' => [
                                'type' => 'array',
                                'description' => 'Mobile devices to test (default: iPhone 14, Samsung Galaxy S23, iPad Pro)',
                                'items' => ['type' => 'string'],
                            ],
                            'orientations' => [
                                'type' => 'array',
                                'description' => 'Device orientations to test (portrait, landscape)',
                                'items' => ['type' => 'string'],
                                'default' => ['portrait'],
                            ],
                            'percy_options' => ['type' => 'object', 'description' => 'Percy snapshot options'],
                        ],
                        'required' => ['url', 'snapshot_name'],
                    ],
                ])
                ->handler(function (array $testConfig) {
                    return $this->runMobileVisualTest($testConfig);
                }),
        ];
    }

    /**
     * Register MCP resources for BrowserStack integration
     */
    public function resources(): array
    {
        return [
            Resource::make('browserstack://config')
                ->description('BrowserStack configuration and status')
                ->handler(function () {
                    return $this->getConfigurationResource();
                }),

            Resource::make('browserstack://browsers')
                ->description('Available browsers and devices')
                ->handler(function () {
                    return $this->getBrowsersResource();
                }),

            Resource::make('browserstack://sessions')
                ->description('Active BrowserStack sessions')
                ->handler(function () {
                    return $this->getSessionsResource();
                }),
        ];
    }

    /**
     * Validate BrowserStack configuration
     */
    private function validateConfiguration(): array
    {
        try {
            $result = $this->browserStackService->validateConfiguration();

            Log::info('BrowserStack configuration validation', [
                'valid' => $result['valid'],
                'errors' => $result['errors'],
            ]);

            return [
                'success' => true,
                'validation_result' => $result,
                'message' => $result['valid']
                    ? 'Konfigurasi BrowserStack adalah sah'
                    : 'Konfigurasi BrowserStack mempunyai ralat',
            ];
        } catch (\Exception $e) {
            Log::error('BrowserStack configuration validation failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal mengesahkan konfigurasi: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get available browsers and devices
     */
    private function getAvailableBrowsers(): array
    {
        try {
            $result = $this->browserStackService->getAvailableBrowsers();

            return [
                'success' => true,
                'browsers' => $result,
                'message' => $result['success']
                    ? "Berjaya mendapatkan {$result['desktop_count']} pelayar desktop dan {$result['mobile_count']} peranti mudah alih"
                    : 'Gagal mendapatkan senarai pelayar',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get available browsers', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal mendapatkan senarai pelayar: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create Percy session on BrowserStack
     */
    private function createPercySession(array $capabilities): array
    {
        try {
            $result = $this->browserStackService->createPercySession($capabilities);

            if ($result['success']) {
                Log::info('BrowserStack Percy session created', [
                    'session_id' => $result['session_id'],
                    'capabilities' => $capabilities,
                ]);
            }

            return [
                'success' => $result['success'],
                'session' => $result,
                'message' => $result['success']
                    ? 'Sesi Percy BrowserStack berjaya dicipta'
                    : 'Gagal mencipta sesi Percy: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Percy session creation failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal mencipta sesi Percy: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Execute Percy visual test across browsers
     */
    private function executePercyVisualTest(array $testConfig): array
    {
        try {
            $result = $this->browserStackService->executePercyVisualTest($testConfig);

            Log::info('Percy visual test executed', [
                'total_tests' => $result['total_tests'] ?? 0,
                'successful_tests' => $result['successful_tests'] ?? 0,
                'failed_tests' => $result['failed_tests'] ?? 0,
            ]);

            return [
                'success' => $result['success'],
                'test_results' => $result,
                'message' => $result['success']
                    ? "Ujian visual Percy selesai: {$result['successful_tests']}/{$result['total_tests']} berjaya"
                    : 'Ujian visual Percy gagal',
            ];
        } catch (\Exception $e) {
            Log::error('Percy visual test execution failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal menjalankan ujian visual Percy: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Run accessibility test with Percy
     */
    private function runAccessibilityTest(array $testConfig): array
    {
        try {
            $result = $this->browserStackService->runAccessibilityTestWithPercy($testConfig);

            if ($result['success']) {
                Log::info('Accessibility test with Percy completed', [
                    'wcag_level' => $result['wcag_level'] ?? 'AA',
                    'compliance_status' => $result['compliance_status'] ?? 'unknown',
                ]);
            }

            return [
                'success' => $result['success'],
                'accessibility_result' => $result,
                'message' => $result['success']
                    ? "Ujian kebolehcapaian selesai dengan status: {$result['compliance_status']}"
                    : 'Ujian kebolehcapaian gagal: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Accessibility test failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal menjalankan ujian kebolehcapaian: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Run WCAG compliance scan
     */
    private function runWCAGComplianceScan(array $testConfig): array
    {
        try {
            $result = $this->browserStackService->runWCAGComplianceScan($testConfig);

            if ($result['success']) {
                Log::info('WCAG compliance scan completed', [
                    'wcag_level' => $result['wcag_level'] ?? 'AA',
                    'compliance_status' => $result['compliance_status'] ?? 'unknown',
                ]);
            }

            return [
                'success' => $result['success'],
                'wcag_result' => $result,
                'message' => $result['success']
                    ? "Imbasan WCAG selesai dengan status: {$result['compliance_status']}"
                    : 'Imbasan WCAG gagal: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('WCAG compliance scan failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal menjalankan imbasan WCAG: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Run focus state validation
     */
    private function runFocusStateValidation(array $testConfig): array
    {
        try {
            $result = $this->browserStackService->runFocusStateValidation($testConfig);

            if ($result['success']) {
                Log::info('Focus state validation completed', [
                    'compliance_status' => $result['compliance_status'] ?? 'unknown',
                ]);
            }

            return [
                'success' => $result['success'],
                'focus_result' => $result,
                'message' => $result['success']
                    ? "Pengesahan keadaan fokus selesai dengan status: {$result['compliance_status']}"
                    : 'Pengesahan keadaan fokus gagal: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Focus state validation failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal menjalankan pengesahan keadaan fokus: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Run color contrast validation
     */
    private function runColorContrastValidation(array $testConfig): array
    {
        try {
            $result = $this->browserStackService->runColorContrastValidation($testConfig);

            if ($result['success']) {
                Log::info('Color contrast validation completed', [
                    'wcag_level' => $result['wcag_level'] ?? 'AA',
                    'compliance_status' => $result['compliance_status'] ?? 'unknown',
                ]);
            }

            return [
                'success' => $result['success'],
                'contrast_result' => $result,
                'message' => $result['success']
                    ? "Pengesahan kontras warna selesai dengan status: {$result['compliance_status']}"
                    : 'Pengesahan kontras warna gagal: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Color contrast validation failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal menjalankan pengesahan kontras warna: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Run keyboard navigation test
     */
    private function runKeyboardNavigationTest(array $testConfig): array
    {
        try {
            $result = $this->browserStackService->runKeyboardNavigationTest($testConfig);

            if ($result['success']) {
                Log::info('Keyboard navigation test completed', [
                    'compliance_status' => $result['compliance_status'] ?? 'unknown',
                ]);
            }

            return [
                'success' => $result['success'],
                'navigation_result' => $result,
                'message' => $result['success']
                    ? "Ujian navigasi papan kekunci selesai dengan status: {$result['compliance_status']}"
                    : 'Ujian navigasi papan kekunci gagal: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Keyboard navigation test failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal menjalankan ujian navigasi papan kekunci: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create Live session for debugging
     */
    private function createLiveSession(array $capabilities): array
    {
        try {
            $result = $this->browserStackService->createLiveSession($capabilities);

            if ($result['success']) {
                Log::info('BrowserStack Live session created', [
                    'session_id' => $result['session_id'],
                    'live_url' => $result['live_url'],
                ]);
            }

            return [
                'success' => $result['success'],
                'live_session' => $result,
                'message' => $result['success']
                    ? 'Sesi langsung BrowserStack berjaya dicipta'
                    : 'Gagal mencipta sesi langsung: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Live session creation failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal mencipta sesi langsung: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Capture screenshot during Live session
     */
    private function captureLiveSessionScreenshot(string $sessionId, string $name, array $options = []): array
    {
        try {
            $result = $this->browserStackService->captureLiveSessionScreenshot($sessionId, $name, $options);

            if ($result['success']) {
                Log::info('Live session screenshot captured', [
                    'session_id' => $sessionId,
                    'screenshot_id' => $result['screenshot_id'],
                ]);
            }

            return [
                'success' => $result['success'],
                'screenshot' => $result,
                'message' => $result['success']
                    ? 'Tangkapan skrin sesi langsung berjaya ditangkap'
                    : 'Gagal menangkap tangkapan skrin: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Live session screenshot capture failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal menangkap tangkapan skrin sesi langsung: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Capture Percy snapshot during Live session
     */
    private function captureLiveSessionPercySnapshot(string $sessionId, string $snapshotName, array $options = []): array
    {
        try {
            $result = $this->browserStackService->captureLiveSessionPercySnapshot($sessionId, $snapshotName, $options);

            if ($result['success']) {
                Log::info('Live session Percy snapshot captured', [
                    'session_id' => $sessionId,
                    'snapshot_id' => $result['snapshot_id'],
                ]);
            }

            return [
                'success' => $result['success'],
                'percy_snapshot' => $result,
                'message' => $result['success']
                    ? 'Percy snapshot sesi langsung berjaya ditangkap'
                    : 'Gagal menangkap Percy snapshot: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Live session Percy snapshot capture failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal menangkap Percy snapshot sesi langsung: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create visual issue report from Live session
     */
    private function createVisualIssueReport(string $sessionId, array $issueData): array
    {
        try {
            $result = $this->browserStackService->createVisualIssueReport($sessionId, $issueData);

            if ($result['success']) {
                Log::info('Visual issue report created', [
                    'session_id' => $sessionId,
                    'issue_id' => $result['issue_id'],
                    'severity' => $result['severity'],
                ]);
            }

            return [
                'success' => $result['success'],
                'issue' => $result,
                'message' => $result['success']
                    ? 'Laporan isu visual berjaya dicipta'
                    : 'Gagal mencipta laporan isu: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Visual issue report creation failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal mencipta laporan isu visual: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Start collaborative debugging session
     */
    private function startCollaborativeSession(string $sessionId, string $ownerName): array
    {
        try {
            $result = $this->browserStackService->startCollaborativeSession($sessionId, $ownerName);

            if ($result['success']) {
                Log::info('Collaborative debugging session started', [
                    'live_session_id' => $sessionId,
                    'collaborative_session_id' => $result['collaborative_session_id'],
                    'owner' => $ownerName,
                ]);
            }

            return [
                'success' => $result['success'],
                'collaborative_session' => $result,
                'message' => $result['success']
                    ? 'Sesi penyahpepijatan kolaboratif berjaya dimulakan'
                    : 'Gagal memulakan sesi kolaboratif: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Collaborative session start failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal memulakan sesi penyahpepijatan kolaboratif: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get predefined debugging workflow
     */
    private function getDebuggingWorkflow(string $workflowType): array
    {
        try {
            $result = $this->browserStackService->getDebuggingWorkflow($workflowType);

            if ($result['success']) {
                Log::info('Debugging workflow retrieved', [
                    'workflow_type' => $workflowType,
                    'workflow_name' => $result['workflow']['name'] ?? 'Unknown',
                ]);
            }

            return [
                'success' => $result['success'],
                'workflow' => $result['workflow'] ?? null,
                'message' => $result['success']
                    ? 'Aliran kerja penyahpepijatan berjaya diambil'
                    : 'Gagal mengambil aliran kerja: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Debugging workflow retrieval failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal mengambil aliran kerja penyahpepijatan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get test execution report
     */
    private function getTestExecutionReport(string $buildId): array
    {
        try {
            $result = $this->browserStackService->getTestExecutionReport($buildId);

            return [
                'success' => $result['success'],
                'report' => $result,
                'message' => $result['success']
                    ? 'Laporan pelaksanaan ujian berjaya dijana'
                    : 'Gagal menjana laporan: ' . ($result['error'] ?? 'Ralat tidak diketahui'),
            ];
        } catch (\Exception $e) {
            Log::error('Test execution report generation failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Gagal menjana laporan pelaksanaan ujian: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Run cross-browser visual consistency test
     */
    private function runCrossBrowserTest(array $testConfig): array
    {
        // Default browsers for cross-browser testing
        $defaultBrowsers = [
            ['browser' => 'Chrome', 'browser_version' => 'latest', 'os' => 'Windows', 'os_version' => '11'],
            ['browser' => 'Firefox', 'browser_version' => 'latest', 'os' => 'Windows', 'os_version' => '11'],
            ['browser' => 'Safari', 'browser_version' => 'latest', 'os' => 'OS X', 'os_version' => 'Monterey'],
            ['browser' => 'Edge', 'browser_version' => 'latest', 'os' => 'Windows', 'os_version' => '11'],
        ];

        $testConfig['browsers'] = $testConfig['browsers'] ?? $defaultBrowsers;

        return $this->executePercyVisualTest($testConfig);
    }

    /**
     * Run mobile visual regression testing
     */
    private function runMobileVisualTest(array $testConfig): array
    {
        // Default mobile devices for testing
        $defaultDevices = [
            ['device' => 'iPhone 14', 'os_version' => '16', 'real_mobile' => true],
            ['device' => 'Samsung Galaxy S23', 'os_version' => '13.0', 'real_mobile' => true],
            ['device' => 'iPad Pro 12.9 2022', 'os_version' => '16', 'real_mobile' => true],
        ];

        $testConfig['browsers'] = $testConfig['devices'] ?? $defaultDevices;

        return $this->executePercyVisualTest($testConfig);
    }

    /**
     * Get configuration resource
     */
    private function getConfigurationResource(): array
    {
        $validation = $this->browserStackService->validateConfiguration();

        return [
            'configuration' => $validation['config'],
            'validation_status' => $validation['valid'],
            'errors' => $validation['errors'],
            'last_checked' => now()->toISOString(),
        ];
    }

    /**
     * Get browsers resource
     */
    private function getBrowsersResource(): array
    {
        $browsers = $this->browserStackService->getAvailableBrowsers();

        return [
            'available_browsers' => $browsers['browsers'] ?? [],
            'desktop_count' => $browsers['desktop_count'] ?? 0,
            'mobile_count' => $browsers['mobile_count'] ?? 0,
            'last_updated' => now()->toISOString(),
        ];
    }

    /**
     * Get sessions resource
     */
    private function getSessionsResource(): array
    {
        // Mock active sessions - in real implementation, this would query BrowserStack API
        return [
            'active_sessions' => [],
            'total_sessions' => 0,
            'last_updated' => now()->toISOString(),
        ];
    }
}
