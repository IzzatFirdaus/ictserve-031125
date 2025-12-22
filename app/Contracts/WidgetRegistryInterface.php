<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Widget Registry Interface
 *
 * Provides centralized management and deduplication of dashboard widgets
 * following ICTServe v3.6.1 patterns and Filament v4.3.1 compliance.
 *
 * @see D04 §3.2 Widget Management Architecture
 * @see R1 Widget Deduplication
 * @see R3 Missing Widget Detection
 */
interface WidgetRegistryInterface
{
    /**
     * Register a widget with the registry
     *
     * @param  string  $widgetClass  Fully qualified widget class name
     * @param  array  $config  Widget configuration (category, sort_order, roles, etc.)
     */
    

/**
 * @param array<string, mixed> $config
 */
public function register(string $widgetClass, array $config = []): void;

    /**
     * Remove a widget from the registry
     *
     * @param  string  $widgetClass  Fully qualified widget class name
     */
    public function deregister(string $widgetClass): void;

    /**
     * Get all registered widgets
     *
     * @return array Array of registered widget configurations
     */
    public function getRegisteredWidgets(): array;

    /**
     * Get widgets filtered by category
     *
     * @param  string  $category  Widget category (header, chart, content)
     * @return array Array of widgets in the specified category
     */
    public function getWidgetsByCategory(string $category): array;

    /**
     * Validate widget class and configuration
     *
     * @param  string  $widgetClass  Fully qualified widget class name
     * @return bool True if widget is valid
     */
    public function validateWidget(string $widgetClass): bool;

    /**
     * Detect duplicate widget registrations
     *
     * @return array Array of duplicate widget information
     */
    public function detectDuplicates(): array;

    /**
     * Get widgets accessible to a specific role
     *
     * @param  string  $role  User role (staff, admin, superuser)
     * @return array Array of widgets accessible to the role
     */
    public function getWidgetsByRole(string $role): array;

    /**
     * Check if a widget is registered
     *
     * @param  string  $widgetClass  Fully qualified widget class name
     * @return bool True if widget is registered
     */
    public function isRegistered(string $widgetClass): bool;
}
