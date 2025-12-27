<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mcp\BrowserStackMcpServer;
use Illuminate\Console\Command;
use Laravel\Mcp\Facades\Mcp;

/**
 * BrowserStack MCP Command for ICTServe v3.6.1
 * 
 * Starts the BrowserStack MCP server for enhanced cross-platform testing
 * with Percy visual testing integration.
 */
class BrowserStackMcpCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mcp:browserstack';

    /**
     * The console command description.
     */
    protected $description = 'Start BrowserStack MCP server for cross-platform testing with Percy integration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting BrowserStack MCP Server for ICTServe v3.6.1...');

        try {
            // Register the BrowserStack MCP server
            Mcp::web(new BrowserStackMcpServer());

            $this->info('BrowserStack MCP Server started successfully!');
            $this->line('');
            $this->line('Available tools:');
            $this->line('  • browserstack_validate_config - Validate BrowserStack configuration');
            $this->line('  • browserstack_get_browsers - Get available browsers and devices');
            $this->line('  • browserstack_create_percy_session - Create session for Percy visual testing');
            $this->line('  • browserstack_run_percy_visual_test - Execute Percy visual tests');
            $this->line('  • browserstack_accessibility_test - Run accessibility tests with Percy');
            $this->line('  • browserstack_wcag_compliance_scan - Run WCAG 2.2 AA compliance scan');
            $this->line('  • browserstack_focus_state_validation - Validate focus state accessibility');
            $this->line('  • browserstack_color_contrast_validation - Validate color contrast ratios');
            $this->line('  • browserstack_keyboard_navigation_test - Test keyboard navigation');
            $this->line('  • browserstack_create_live_session - Create Live session for debugging');
            $this->line('  • browserstack_capture_live_screenshot - Capture screenshot during Live session');
            $this->line('  • browserstack_capture_live_percy_snapshot - Capture Percy snapshot during Live session');
            $this->line('  • browserstack_create_visual_issue - Create visual issue report from Live session');
            $this->line('  • browserstack_start_collaborative_session - Start collaborative debugging session');
            $this->line('  • browserstack_get_debugging_workflow - Get predefined debugging workflow');
            $this->line('  • browserstack_get_test_report - Get comprehensive test reports');
            $this->line('  • browserstack_run_cross_browser_test - Run cross-browser visual tests');
            $this->line('  • browserstack_mobile_visual_test - Run mobile visual regression tests');
            $this->line('');
            $this->line('Available resources:');
            $this->line('  • browserstack://config - Configuration and status');
            $this->line('  • browserstack://browsers - Available browsers and devices');
            $this->line('  • browserstack://sessions - Active BrowserStack sessions');
            $this->line('');
            $this->info('Server is ready to accept MCP requests...');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to start BrowserStack MCP Server: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
