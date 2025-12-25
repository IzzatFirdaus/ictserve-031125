<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Services\WidgetLayoutManager;
use App\Services\WidgetRegistry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Widget Customization Panel Component
 *
 * Provides drag-and-drop interface for widget customization with accessibility support
 * following ICTServe v3.6.1 patterns and WCAG 2.2 AA compliance.
 *
 * @trace Requirements: R5 (Widget Configuration), R20 (Widget Customization)
 *
 * @see D04 §3.2 Widget Management Architecture
 * @see D12 §3.4 User Experience Design
 * @see D12 WCAG 2.2 AA Accessibility Standards
 *
 * @version 3.6.1
 */
class WidgetCustomizationPanel extends Component
{
    public bool $isOpen = false;

    public array $layout = [];

    public array $availableWidgets = [];

    public array $categories = ['header', 'content', 'charts'];

    public string $activeTab = 'layout';

    public array $widgetSizes = ['small', 'medium', 'large'];

    public string $exportData = '';

    public string $importData = '';

    public bool $showExportModal = false;

    public bool $showImportModal = false;

    public bool $showResetConfirmation = false;

    protected $listeners = [
        'widget-order-updated' => 'handleWidgetOrderUpdate',
        'widget-visibility-toggled' => 'handleVisibilityToggle',
        'widget-size-changed' => 'handleSizeChange',
    ];

    public function mount(): void
    {
        $this->loadUserLayout();
        $this->loadAvailableWidgets();
    }

    /**
     * Toggle customization panel visibility
     */
    public function togglePanel(): void
    {
        $this->isOpen = ! $this->isOpen;

        if ($this->isOpen) {
            $this->loadUserLayout();
            $this->dispatch('panel-opened');
        } else {
            $this->dispatch('panel-closed');
        }
    }

    /**
     * Switch between tabs (layout, visibility, sizes, import/export)
     */
    public function switchTab(string $tab): void
    {
        $validTabs = ['layout', 'visibility', 'sizes', 'import-export'];

        if (\in_array($tab, $validTabs, true)) {
            $this->activeTab = $tab;
            $this->dispatch('tab-changed', ['tab' => $tab]);
        }
    }

    /**
     * Handle widget order update from drag-and-drop
     */
    #[On('widget-order-updated')]
    

/**
 * @param array<string, mixed> $widgetOrder
 */
public function handleWidgetOrderUpdate(string $category, array $widgetOrder): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->addError('general', 'Pengguna tidak dijumpai.');

            return;
        }

        $layoutManager = app(WidgetLayoutManager::class);
        $success = $layoutManager->updateWidgetOrder($user, $category, $widgetOrder);

        if ($success) {
            $this->loadUserLayout();
            $this->dispatch('widget-order-saved', [
                'category' => $category,
                'message' => 'Susunan widget berjaya dikemas kini.',
            ]);
        } else {
            $this->addError('order', 'Gagal mengemas kini susunan widget.');
        }
    }

    /**
     * Toggle widget visibility
     */
    public function toggleWidgetVisibility(string $widgetClass): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->addError('general', 'Pengguna tidak dijumpai.');

            return;
        }

        // Find current visibility state
        $currentlyVisible = true;
        foreach ($this->layout['widgets'] ?? [] as $widgets) {
            foreach ($widgets as $widget) {
                if ($widget['class'] === $widgetClass) {
                    $currentlyVisible = $widget['visible'] ?? true;
                    break 2;
                }
            }
        }

        $newVisibility = ! $currentlyVisible;
        $layoutManager = app(WidgetLayoutManager::class);
        $success = $layoutManager->toggleWidgetVisibility($user, $widgetClass, $newVisibility);

        if ($success) {
            $this->loadUserLayout();
            $message = $newVisibility ? 'Widget dipaparkan.' : 'Widget disembunyikan.';
            $this->dispatch('widget-visibility-changed', [
                'widget' => $widgetClass,
                'visible' => $newVisibility,
                'message' => $message,
            ]);
        } else {
            $this->addError('visibility', 'Gagal mengemas kini keterlihatan widget.');
        }
    }

    /**
     * Update widget size
     */
    public function updateWidgetSize(string $widgetClass, string $size): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->addError('general', 'Pengguna tidak dijumpai.');

            return;
        }

        $layoutManager = app(WidgetLayoutManager::class);
        $success = $layoutManager->updateWidgetSize($user, $widgetClass, $size);

        if ($success) {
            $this->loadUserLayout();
            $this->dispatch('widget-size-changed', [
                'widget' => $widgetClass,
                'size' => $size,
                'message' => 'Saiz widget berjaya dikemas kini.',
            ]);
        } else {
            $this->addError('size', 'Gagal mengemas kini saiz widget.');
        }
    }

    /**
     * Reset layout to default
     */
    public function resetToDefault(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->addError('general', 'Pengguna tidak dijumpai.');

            return;
        }

        $layoutManager = app(WidgetLayoutManager::class);
        $success = $layoutManager->resetToDefault($user);

        if ($success) {
            $this->loadUserLayout();
            $this->showResetConfirmation = false;
            $this->dispatch('layout-reset', [
                'message' => 'Susun atur telah dikembalikan kepada tetapan asal.',
            ]);
        } else {
            $this->addError('reset', 'Gagal mengeset semula susun atur.');
        }
    }

    /**
     * Export current layout
     */
    public function exportLayout(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->addError('general', 'Pengguna tidak dijumpai.');

            return;
        }

        $layoutManager = app(WidgetLayoutManager::class);
        $exportData = $layoutManager->exportLayout($user);
        $this->exportData = json_encode($exportData, JSON_PRETTY_PRINT);
        $this->showExportModal = true;
    }

    /**
     * Import layout from JSON data
     */
    public function importLayout(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->addError('general', 'Pengguna tidak dijumpai.');

            return;
        }

        try {
            $importData = json_decode($this->importData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError('import', 'Format JSON tidak sah.');

                return;
            }

            $layoutManager = app(WidgetLayoutManager::class);
            $success = $layoutManager->importLayout($user, $importData);

            if ($success) {
                $this->loadUserLayout();
                $this->importData = '';
                $this->showImportModal = false;
                $this->dispatch('layout-imported', [
                    'message' => 'Susun atur berjaya diimport.',
                ]);
            } else {
                $this->addError('import', 'Gagal mengimport susun atur.');
            }
        } catch (\Exception $e) {
            Log::error('Layout import failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            $this->addError('import', 'Ralat berlaku semasa mengimport susun atur.');
        }
    }

    /**
     * Copy export data to clipboard (handled by JavaScript)
     */
    public function copyExportData(): void
    {
        $this->dispatch('copy-to-clipboard', ['data' => $this->exportData]);
    }

    /**
     * Close modals
     */
    public function closeModal(string $modal): void
    {
        match ($modal) {
            'export' => $this->showExportModal = false,
            'import' => $this->showImportModal = false,
            'reset' => $this->showResetConfirmation = false,
            default => null,
        };
    }

    /**
     * Get widget display name
     */
    public function getWidgetDisplayName(string $widgetClass): string
    {
        $className = class_basename($widgetClass);

        // Convert CamelCase to readable format
        $readable = preg_replace('/([A-Z])/', ' $1', $className);
        $readable = trim($readable);

        // Remove common suffixes
        $readable = str_replace(['Widget', 'Chart', 'Table'], '', $readable);

        return trim($readable) ?: $className;
    }

    /**
     * Get widget category display name
     */
    public function getCategoryDisplayName(string $category): string
    {
        return match ($category) {
            'header' => 'Statistik Utama',
            'content' => 'Kandungan',
            'charts' => 'Carta dan Graf',
            default => ucfirst($category),
        };
    }

    /**
     * Get size display name
     */
    public function getSizeDisplayName(string $size): string
    {
        return match ($size) {
            'small' => 'Kecil',
            'medium' => 'Sederhana',
            'large' => 'Besar',
            default => ucfirst($size),
        };
    }

    /**
     * Check if widget is visible
     */
    public function isWidgetVisible(string $widgetClass): bool
    {
        foreach ($this->layout['widgets'] ?? [] as $widgets) {
            foreach ($widgets as $widget) {
                if ($widget['class'] === $widgetClass) {
                    return $widget['visible'] ?? true;
                }
            }
        }

        return true;
    }

    /**
     * Get widget size
     */
    public function getWidgetSize(string $widgetClass): string
    {
        return $this->layout['widget_sizes'][$widgetClass] ?? 'medium';
    }

    /**
     * Load user layout from service
     */
    private function loadUserLayout(): void
    {
        $user = Auth::user();

        if ($user) {
            $layoutManager = app(WidgetLayoutManager::class);
            $this->layout = $layoutManager->getUserLayout($user);
        }
    }

    /**
     * Load available widgets for user
     */
    private function loadAvailableWidgets(): void
    {
        $user = Auth::user();

        if ($user) {
            $userRole = $user->role ?? 'staff';
            $widgetRegistry = app(WidgetRegistry::class);
            $this->availableWidgets = $widgetRegistry->getWidgetsByRole($userRole);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.widget-customization-panel');
    }
}
