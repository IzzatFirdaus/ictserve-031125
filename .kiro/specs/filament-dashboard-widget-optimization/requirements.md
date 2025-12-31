# Requirements Document

## Introduction

The ICTServe Filament admin dashboard currently displays duplicated widgets, creating a poor user experience and inefficient resource usage. This feature addresses the deduplication of widgets and ensures proper organization of dashboard components according to the MyDS design system and WCAG 2.2 AA accessibility standards.

**System Context:** ICTServe v3.6.1 - Laravel 12.43.1 Enterprise Application with True Hybrid Architecture (guest + authenticated + admin access), Filament 4.3.1 admin panel, and Cloud Hybrid AI integration (D18).

**Technology Stack Alignment:**

- **Laravel Framework:** 12.43.1 with PHP 8.4.11 strict typing
- **Filament Admin Panel:** 4.3.1 with Server-Driven UI (SDUI) patterns
- **Laravel Pulse:** 1.4.7 for performance monitoring integration
- **Laravel Reverb:** 1.6.3 for real-time WebSocket communication
- **Laravel Horizon:** 5.41.0 for queue management and monitoring
- **Livewire:** 3.7.3 for reactive components
- **Tailwind CSS:** 4.1.18 with @theme configuration

## Glossary

- **Dashboard_Widget**: A Filament widget component that displays data or functionality on the admin dashboard
- **Widget_Deduplication**: The process of removing duplicate widget instances from the dashboard
- **Header_Widget**: Widgets displayed in the header section of the dashboard (overview stats, critical alerts)
- **Chart_Widget**: Widgets that display data visualizations (charts, graphs, analytics)
- **Content_Widget**: Widgets that display content or interactive elements (activity feeds, quick actions)
- **Widget_Registry**: A system to track and manage which widgets are active and displayed
- **Laravel_Horizon_Integration**: Laravel Horizon 5.41.0 dashboard integration for queue monitoring widgets accessible to admin and superuser roles
- **AI_Dashboard_Widgets**: Cloud Hybrid AI monitoring widgets for Ollama and AWS Bedrock performance metrics (D18 integration)
- **Dual_Audit_Widgets**: Widgets displaying both compliance audit (owen-it/laravel-auditing) and operational audit (spatie/laravel-activitylog) data

## Requirements

### Requirement 1: Widget Deduplication (D04 §3.2, SRS-ADM-003)

**User Story:** As an administrator, I want to see each dashboard widget only once, so that I can have a clean and efficient dashboard interface.

#### Acceptance Criteria

1. WHEN the admin dashboard loads, THE System SHALL display each widget type only once
2. WHEN duplicate widgets are detected, THE System SHALL remove the duplicates and keep only the intended instance
3. WHEN widgets are rendered, THE System SHALL validate that no widget class appears multiple times in the same section
4. THE System SHALL log any duplicate widget detection for debugging purposes
5. WHEN the dashboard is refreshed, THE System SHALL maintain the deduplicated state

### Requirement 2: Widget Organization and Categorization (D04 §3.2, SRS-UX-005)

**User Story:** As an administrator, I want widgets to be properly organized by type and importance, so that I can quickly access the most relevant information.

#### Acceptance Criteria

1. WHEN the dashboard loads, THE System SHALL display header widgets in the designated header section only
2. WHEN chart widgets are rendered, THE System SHALL display them in the charts section with proper grid layout
3. WHEN content widgets are displayed, THE System SHALL place them in the main content area
4. THE System SHALL respect the widget sort order defined in each widget class
5. WHEN widgets are categorized, THE System SHALL follow the MyDS 12-8-4 grid system for responsive layout

### Requirement 3: Missing Widget Detection and Integration (SRS-RPT-004, D10 Filament/Widgets)

**User Story:** As an administrator, I want all available widgets to be properly integrated into the dashboard, so that I don't miss important system information.

#### Acceptance Criteria

1. WHEN the system starts, THE System SHALL scan for all available widget classes in the widgets directory
2. WHEN a widget is not registered in the dashboard, THE System SHALL log it as a missing widget
3. WHEN missing widgets are detected, THE System SHALL provide a mechanism to integrate them appropriately
4. THE System SHALL categorize widgets based on their class type (StatsOverviewWidget, ChartWidget, BaseWidget, etc.)
5. WHEN new widgets are added to the codebase, THE System SHALL automatically detect and suggest their integration

### Requirement 4: Widget Performance and Caching (D04 §3.2, SRS-RPT-006)

**User Story:** As an administrator, I want the dashboard to load quickly and efficiently, so that I can access information without delays.

#### Acceptance Criteria

1. WHEN widgets are loaded, THE System SHALL implement proper caching mechanisms for widget data
2. WHEN multiple widgets request similar data, THE System SHALL use shared caching to reduce database queries
3. WHEN widgets are rendered, THE System SHALL use lazy loading for non-critical widgets
4. THE System SHALL implement a maximum cache TTL of 300 seconds for real-time widgets
5. WHEN the dashboard is accessed, THE System SHALL load critical widgets first and defer others

### Requirement 5: Widget Configuration Management (SRS-UX-005, D12 §3.4)

**User Story:** As an administrator, I want to be able to configure which widgets are displayed, so that I can customize my dashboard experience.

#### Acceptance Criteria

1. WHEN an administrator accesses widget settings, THE System SHALL provide a configuration interface
2. WHEN widgets are enabled or disabled, THE System SHALL persist the configuration per user or globally
3. WHEN widget configuration changes, THE System SHALL apply changes without requiring a full page reload
4. THE System SHALL provide default widget configurations for different user roles
5. WHEN widgets are configured, THE System SHALL validate that essential widgets cannot be disabled

### Requirement 6: Accessibility and Responsive Design (D12 WCAG 2.2 AA, MyDS Grid)

**User Story:** As an administrator using assistive technology, I want the dashboard widgets to be accessible and responsive, so that I can use the system effectively on any device.

#### Acceptance Criteria

1. WHEN widgets are rendered, THE System SHALL include proper ARIA labels and semantic HTML
2. WHEN the dashboard is viewed on different screen sizes, THE System SHALL adapt widget layout using MyDS responsive breakpoints
3. WHEN widgets contain interactive elements, THE System SHALL ensure keyboard navigation support
4. THE System SHALL maintain WCAG 2.2 AA compliance for all widget content
5. WHEN widgets display data, THE System SHALL provide alternative text descriptions for screen readers

### Requirement 7: Widget Error Handling and Fallbacks (D04 §3.1 Service Layer)

**User Story:** As an administrator, I want the dashboard to remain functional even when individual widgets fail, so that I can continue working with the available information.

#### Acceptance Criteria

1. WHEN a widget fails to load, THE System SHALL display an error state without breaking the entire dashboard
2. WHEN widget data is unavailable, THE System SHALL show appropriate fallback content
3. WHEN widgets encounter errors, THE System SHALL log the errors for debugging
4. THE System SHALL provide retry mechanisms for failed widgets
5. WHEN critical widgets fail, THE System SHALL notify administrators of the issue

### Requirement 8: Real-time Updates and Broadcasting (D16 Laravel Reverb, SRS-ADM-003)

**User Story:** As an administrator, I want widgets to update in real-time, so that I always see the most current system information.

#### Acceptance Criteria

1. WHEN system data changes, THE System SHALL broadcast updates to relevant widgets via Laravel Reverb
2. WHEN widgets receive real-time updates, THE System SHALL update the display without full page refresh
3. WHEN multiple users are viewing the dashboard, THE System SHALL synchronize widget updates across sessions
4. THE System SHALL implement proper rate limiting for real-time updates to prevent performance issues
5. WHEN real-time connections are lost, THE System SHALL gracefully fall back to periodic polling

### Requirement 9: Laravel Pulse Integration (D13 §5.6, SRS-RPT-006)

**User Story:** As an administrator, I want to monitor system performance through integrated Laravel Pulse widgets, so that I can proactively identify and resolve performance issues.

#### Acceptance Criteria

1. WHEN the dashboard loads, THE System SHALL display Laravel Pulse performance metrics in dedicated widgets
2. WHEN performance thresholds are exceeded, THE System SHALL highlight critical metrics with appropriate visual indicators
3. WHEN Pulse data is displayed, THE System SHALL show real-time performance data including response times, database queries, and queue status
4. THE System SHALL restrict Laravel Pulse dashboard access to admin and superuser roles only
5. WHEN performance alerts are triggered, THE System SHALL integrate with the notification system for proactive monitoring

### Requirement 10: Role-Based Widget Access (SRS-ADM-002, D04 §3.1)

**User Story:** As a system administrator, I want widgets to display appropriate content based on user roles, so that users only see relevant information for their responsibilities.

#### Acceptance Criteria

1. WHEN an admin user accesses the dashboard, THE System SHALL display operational widgets (ticket stats, loan stats, SLA compliance)
2. WHEN a superuser accesses the dashboard, THE System SHALL display all widgets including system configuration and audit widgets
3. WHEN role permissions change, THE System SHALL update widget visibility without requiring logout
4. THE System SHALL hide sensitive widgets from users without appropriate permissions
5. WHEN widgets are role-restricted, THE System SHALL provide clear messaging about access requirements

### Requirement 11: MyDS Design System Compliance (D12 §4, D14 MyDS v2025.2)

**User Story:** As a government system user, I want the dashboard to follow Malaysian Design System standards, so that I have a consistent experience across government digital services.

#### Acceptance Criteria

1. WHEN widgets are rendered, THE System SHALL use MyDS 12-8-4 responsive grid system for layout
2. WHEN colors are applied, THE System SHALL use MyDS semantic color tokens and maintain proper contrast ratios
3. WHEN typography is displayed, THE System SHALL use MyDS approved fonts (Poppins for headings, Inter for body text)
4. THE System SHALL implement MyDS spacing system and component patterns for consistent visual hierarchy
5. WHEN interactive elements are displayed, THE System SHALL follow MyDS interaction patterns and motion tokens

### Requirement 12: Filament v4.3.1 Integration (D04 Technology Stack)

**User Story:** As a developer, I want widgets to properly integrate with Filament v4.3.1 architecture, so that they follow framework best practices and maintain compatibility.

#### Acceptance Criteria

1. WHEN widgets are created, THE System SHALL extend appropriate Filament v4 widget base classes
2. WHEN widget data is loaded, THE System SHALL use Filament's caching mechanisms and lazy loading features
3. WHEN widgets are displayed, THE System SHALL respect Filament's column spanning and responsive behavior
4. THE System SHALL implement proper widget sorting and organization using Filament's widget management system

### Requirement 13: Missing Widget Integration from Screenshots Analysis

**User Story:** As an administrator, I want all the widgets shown in the system screenshots to be properly integrated into the dashboard, so that I have access to all available system information and analytics.

#### Acceptance Criteria

**Missing Widgets Identified from Screenshots:**

1. WHEN the dashboard loads, THE System SHALL include the **Asset Status Distribution** pie chart widget (visible in screenshots but missing from current dashboard)
2. WHEN performance metrics are displayed, THE System SHALL include the **System Health Widget** showing server status and performance indicators
3. WHEN asset management is accessed, THE System SHALL include the **Asset Availability Calendar Widget** for visual asset scheduling
4. WHEN email operations are monitored, THE System SHALL include **Email Queue Stats Widget** and **Email Queue Trends Widget**
5. WHEN system monitoring is required, THE System SHALL include **Performance Metrics Widget** showing response times and system load
6. WHEN user activity tracking is needed, THE System SHALL include **User Activity Stats Widget** and **User Activity Widget**
7. WHEN loan analytics are displayed, THE System SHALL include **Loan Approval Queue Widget** for pending approvals
8. WHEN system diagnostics are required, THE System SHALL include **Health Check Table Widget** and **Horizon Health Widget**
9. WHEN data retention compliance is monitored, THE System SHALL include **Data Retention Alert Widget**
10. WHEN sensitive access is audited, THE System SHALL include **Sensitive Access Log Widget**

**Widget Integration Requirements:**

1. THE System SHALL categorize missing widgets into appropriate sections (header, content, or chart widgets)
2. THE System SHALL ensure missing widgets follow the same caching and performance patterns as existing widgets
3. THE System SHALL implement proper authorization for sensitive widgets (e.g., Sensitive Access Log Widget for superuser only)
4. THE System SHALL provide configuration options to enable/disable optional widgets
5. THE System SHALL maintain proper sort order and visual hierarchy when adding missing widgets

### Requirement 14: Graph Widget UI/UX Improvements

**User Story:** As an administrator, I want graph widgets to have improved visual design and interactivity, so that I can better understand and analyze system data.

#### Acceptance Criteria

**Visual Design Improvements:**

1. WHEN chart widgets are displayed, THE System SHALL implement consistent color schemes using MyDS semantic colors
2. WHEN pie charts are rendered, THE System SHALL use distinct, accessible colors with proper contrast ratios (minimum 3:1)
3. WHEN bar charts are displayed, THE System SHALL implement gradient fills and subtle shadows for visual depth
4. THE System SHALL use consistent typography (Inter font) for chart labels and legends
5. WHEN charts contain data labels, THE System SHALL ensure text is readable with appropriate font sizes (minimum 12px)

**Interactive Features:**

1. WHEN users hover over chart elements, THE System SHALL display detailed tooltips with contextual information
2. WHEN charts support drilling down, THE System SHALL provide click interactions to view detailed data
3. WHEN chart data is extensive, THE System SHALL implement zoom and pan functionality for better navigation
4. THE System SHALL provide export functionality (PNG, SVG, PDF) for chart widgets
5. WHEN real-time data updates occur, THE System SHALL animate chart transitions smoothly

**Responsive Design:**

1. WHEN charts are viewed on mobile devices, THE System SHALL adapt chart layouts for smaller screens
2. WHEN screen orientation changes, THE System SHALL re-render charts with appropriate dimensions
3. WHEN charts are resized using the toggle functionality, THE System SHALL maintain aspect ratios and readability
4. THE System SHALL implement progressive disclosure for complex charts on smaller screens
5. WHEN multiple charts are displayed, THE System SHALL use consistent spacing and alignment

**Accessibility Enhancements:**

1. WHEN charts are rendered, THE System SHALL provide alternative text descriptions for screen readers
2. WHEN chart data changes, THE System SHALL announce updates to assistive technologies
3. WHEN charts use color to convey information, THE System SHALL provide additional visual indicators (patterns, shapes)
4. THE System SHALL support keyboard navigation for interactive chart elements
5. WHEN charts contain animations, THE System SHALL respect user preferences for reduced motion

**Performance Optimizations:**

1. WHEN large datasets are displayed, THE System SHALL implement data virtualization and lazy loading
2. WHEN multiple charts are rendered, THE System SHALL use efficient rendering techniques to prevent performance degradation
3. WHEN chart data updates frequently, THE System SHALL implement debouncing to prevent excessive re-renders
4. THE System SHALL cache chart configurations and reuse them for similar data sets
5. WHEN charts are not visible, THE System SHALL defer rendering until they come into viewport

**Data Presentation Improvements:**

1. WHEN displaying trends, THE System SHALL include trend indicators (arrows, percentage changes) in chart titles
2. WHEN showing comparative data, THE System SHALL implement side-by-side chart layouts with synchronized interactions
3. WHEN data has time components, THE System SHALL provide time range selectors (last 7 days, 30 days, etc.)
4. THE System SHALL implement smart data aggregation for charts with large datasets
5. WHEN displaying empty or insufficient data, THE System SHALL show meaningful empty states with guidance

### Requirement 15: Filament Dashboard Color System (D12 §4, MyDS v2025.2)

**User Story:** As an administrator, I want the Filament dashboard to use consistent, accessible colors in both light and dark modes, so that I have an optimal viewing experience that complies with government design standards.

#### Acceptance Criteria

**Light Mode Color Palette:**

**Primary Colors (Government Blue - MOTAC Branding):**

1. WHEN primary actions are displayed, THE System SHALL use Primary 500 (#0056B3) for main buttons, links, and active states
2. WHEN hover states are triggered, THE System SHALL use Primary 600 (#004494) for interactive feedback
3. WHEN focus states are applied, THE System SHALL use Primary 700 (#003875) with proper focus rings
4. WHEN background highlights are needed, THE System SHALL use Primary 50 (#eff6ff) for subtle emphasis

**Secondary Colors (Supporting Blue):**

1. WHEN secondary actions are displayed, THE System SHALL use Secondary 500 (#0B4D8F) for less prominent buttons
2. WHEN secondary hover states occur, THE System SHALL use Secondary 600 (#094070) for feedback

**Semantic Colors (Status Indicators):**

1. WHEN success states are shown, THE System SHALL use Success 500 (#1B7C54) with 4.6:1 contrast ratio
2. WHEN warning states are displayed, THE System SHALL use Warning 500 (#CC7700) with 4.5:1 contrast ratio  
3. WHEN error states are shown, THE System SHALL use Danger 500 (#B3002D) with 7.8:1 contrast ratio
4. WHEN informational states are displayed, THE System SHALL use Info 500 (#0369A1) with 6.2:1 contrast ratio

**Neutral Colors (Text and Backgrounds):**

1. WHEN main content backgrounds are rendered, THE System SHALL use Gray 50 (#f9fafb) for page backgrounds
2. WHEN card backgrounds are displayed, THE System SHALL use White (#ffffff) for content containers
3. WHEN borders are needed, THE System SHALL use Gray 200 (#e5e7eb) with 3:1 contrast
4. WHEN body text is displayed, THE System SHALL use Gray 700 (#374151) with 8.9:1 contrast ratio
5. WHEN headings are rendered, THE System SHALL use Gray 900 (#111827) with 12.6:1 contrast ratio
6. WHEN secondary text is shown, THE System SHALL use Gray 500 (#6b7280) with 4.6:1 contrast ratio

**Dark Mode Color Palette:**

**Primary Colors (Adapted for Dark Mode):**

1. WHEN primary actions are displayed in dark mode, THE System SHALL use Primary 400 (#3B82F6) for better visibility
2. WHEN hover states are triggered in dark mode, THE System SHALL use Primary 300 (#93C5FD) for feedback
3. WHEN focus states are applied in dark mode, THE System SHALL use Primary 200 (#DBEAFE) with dark focus rings
4. WHEN background highlights are needed in dark mode, THE System SHALL use Primary 900 (#1E3A8A) for subtle emphasis

**Semantic Colors (Dark Mode Adapted):**

1. WHEN success states are shown in dark mode, THE System SHALL use Success 400 (#34D399) with maintained contrast
2. WHEN warning states are displayed in dark mode, THE System SHALL use Warning 400 (#FBBF24) with sufficient contrast
3. WHEN error states are shown in dark mode, THE System SHALL use Danger 400 (#F87171) with proper visibility
4. WHEN informational states are displayed in dark mode, THE System SHALL use Info 400 (#60A5FA) with adequate contrast

**Neutral Colors (Dark Mode):**

1. WHEN main content backgrounds are rendered in dark mode, THE System SHALL use Gray 900 (#111827) for page backgrounds
2. WHEN card backgrounds are displayed in dark mode, THE System SHALL use Gray 800 (#1F2937) for content containers
3. WHEN borders are needed in dark mode, THE System SHALL use Gray 700 (#374151) with maintained contrast
4. WHEN body text is displayed in dark mode, THE System SHALL use Gray 100 (#f3f4f6) with 8.9:1 contrast ratio
5. WHEN headings are rendered in dark mode, THE System SHALL use White (#ffffff) with maximum contrast
6. WHEN secondary text is shown in dark mode, THE System SHALL use Gray 400 (#9CA3AF) with 4.6:1 contrast ratio

**Widget-Specific Colors:**

**Chart and Graph Colors:**

1. WHEN pie charts are rendered, THE System SHALL use a palette of: Primary 500, Success 500, Warning 500, Info 500, Secondary 500, with additional tints as needed
2. WHEN bar charts are displayed, THE System SHALL use gradient fills from Primary 500 to Primary 300 for visual depth
3. WHEN line charts are shown, THE System SHALL use distinct colors: Primary 500 (main trend), Success 500 (positive metrics), Danger 500 (alerts)
4. WHEN chart backgrounds are rendered, THE System SHALL use Gray 50 (light mode) or Gray 800 (dark mode)

**Status Badge Colors:**

1. WHEN ticket status badges are displayed, THE System SHALL use: Open (Danger 500), In Progress (Warning 500), Resolved (Success 500), Closed (Gray 500)
2. WHEN loan status badges are shown, THE System SHALL use: Pending (Warning 500), Approved (Success 500), Rejected (Danger 500), Returned (Info 500)
3. WHEN priority indicators are displayed, THE System SHALL use: High (Danger 500), Medium (Warning 500), Low (Success 500)

**Interactive Element Colors:**

1. WHEN buttons are rendered, THE System SHALL use Primary 500 background with White text for primary actions
2. WHEN secondary buttons are displayed, THE System SHALL use Gray 200 background with Gray 700 text (light mode)
3. WHEN danger buttons are shown, THE System SHALL use Danger 500 background with White text
4. WHEN disabled states are applied, THE System SHALL use Gray 300 background with Gray 500 text

**Accessibility Requirements:**

1. THE System SHALL maintain minimum 4.5:1 contrast ratio for all text elements
2. THE System SHALL maintain minimum 3:1 contrast ratio for UI components and graphical elements
3. WHEN color is used to convey information, THE System SHALL provide additional visual indicators (icons, patterns)
4. THE System SHALL support Windows High Contrast mode and other accessibility color schemes
5. WHEN users have color vision deficiencies, THE System SHALL ensure information remains accessible through alternative visual cues

**Theme Switching:**

1. WHEN users toggle between light and dark modes, THE System SHALL smoothly transition colors with CSS animations
2. WHEN theme preferences are saved, THE System SHALL persist the choice across sessions
3. WHEN system theme changes are detected, THE System SHALL optionally follow system preferences
4. THE System SHALL provide a theme toggle widget in the dashboard header for easy access

### Requirement 16: WCAG 2.2 AA Dark Mode Best Practices (WCAG 2.2 AA, D12 §3.1)

**User Story:** As a user with visual impairments or light sensitivity, I want the dark mode to follow WCAG 2.2 AA accessibility guidelines, so that I can use the dashboard comfortably and effectively with assistive technologies.

#### Acceptance Criteria

**Contrast Requirements (WCAG 2.2 Success Criterion 1.4.3, 1.4.6):**

1. WHEN text is displayed in dark mode, THE System SHALL maintain minimum 4.5:1 contrast ratio for normal text (AA level)
2. WHEN large text (18pt+ or 14pt+ bold) is displayed in dark mode, THE System SHALL maintain minimum 3:1 contrast ratio
3. WHEN UI components are rendered in dark mode, THE System SHALL maintain minimum 3:1 contrast ratio for borders, icons, and interactive elements
4. WHEN focus indicators are shown in dark mode, THE System SHALL maintain minimum 3:1 contrast ratio against adjacent colors
5. THE System SHALL provide enhanced contrast option achieving 7:1 ratio for normal text (AAA level) as user preference

**Color and Visual Design (WCAG 2.2 Success Criterion 1.4.1, 1.4.8):**

1. WHEN information is conveyed through color in dark mode, THE System SHALL provide additional visual indicators (icons, patterns, text labels)
2. WHEN status is indicated by color in dark mode, THE System SHALL include text labels or symbols alongside color coding
3. WHEN interactive elements change state in dark mode, THE System SHALL provide multiple visual cues (color, shape, text, icons)
4. THE System SHALL avoid using color alone to distinguish between different data series in charts and graphs
5. WHEN error states are displayed in dark mode, THE System SHALL combine color with clear text descriptions and icons

**Text and Typography (WCAG 2.2 Success Criterion 1.4.4, 1.4.10, 1.4.12):**

1. WHEN text is resized up to 200% in dark mode, THE System SHALL maintain readability without horizontal scrolling
2. WHEN text spacing is increased in dark mode, THE System SHALL accommodate line height up to 1.5x font size
3. WHEN paragraph spacing is modified in dark mode, THE System SHALL support spacing up to 2x font size
4. THE System SHALL use fonts that remain legible in dark mode (Inter for body text, Poppins for headings)
5. WHEN text is displayed over background images in dark mode, THE System SHALL ensure sufficient contrast through overlays or borders

**Focus and Navigation (WCAG 2.2 Success Criterion 2.4.7, 2.4.11, 2.4.13):**

1. WHEN keyboard focus moves between elements in dark mode, THE System SHALL provide clearly visible focus indicators
2. WHEN focus indicators are displayed in dark mode, THE System SHALL use high contrast colors (minimum 3:1 against background)
3. WHEN interactive elements receive focus in dark mode, THE System SHALL ensure focus indicators are not obscured by other content
4. THE System SHALL maintain consistent focus indicator styles across all interactive elements in dark mode
5. WHEN focus moves to off-screen elements in dark mode, THE System SHALL scroll them into view appropriately

**Motion and Animation (WCAG 2.2 Success Criterion 2.3.3, 2.2.2):**

1. WHEN animations are displayed in dark mode, THE System SHALL respect user's reduced motion preferences
2. WHEN theme transitions occur, THE System SHALL provide smooth animations that don't trigger vestibular disorders
3. WHEN auto-updating content is shown in dark mode, THE System SHALL provide pause, stop, or hide controls
4. THE System SHALL avoid flashing content that could trigger seizures (no more than 3 flashes per second)
5. WHEN loading animations are displayed in dark mode, THE System SHALL use subtle, non-distracting motion

**Screen Reader and Assistive Technology Support (WCAG 2.2 Success Criterion 4.1.2, 4.1.3):**

1. WHEN dark mode is activated, THE System SHALL announce the theme change to screen readers
2. WHEN color schemes change in dark mode, THE System SHALL update ARIA labels to reflect current visual state
3. WHEN charts and graphs are displayed in dark mode, THE System SHALL provide comprehensive alternative text descriptions
4. THE System SHALL ensure all interactive elements have appropriate ARIA roles and properties in dark mode
5. WHEN dynamic content updates in dark mode, THE System SHALL announce changes to assistive technologies

**Cognitive Accessibility (WCAG 2.2 Success Criterion 3.2.1, 3.2.2, 3.3.2):**

1. WHEN users switch to dark mode, THE System SHALL maintain consistent navigation and layout patterns
2. WHEN forms are displayed in dark mode, THE System SHALL provide clear labels and instructions
3. WHEN errors occur in dark mode, THE System SHALL provide specific, actionable error messages
4. THE System SHALL maintain consistent interaction patterns between light and dark modes
5. WHEN complex interfaces are shown in dark mode, THE System SHALL provide contextual help and guidance

**Performance and Technical Considerations:**

1. WHEN dark mode is enabled, THE System SHALL not significantly impact page load times or rendering performance
2. WHEN theme switching occurs, THE System SHALL use efficient CSS custom properties for color changes
3. WHEN images are displayed in dark mode, THE System SHALL provide appropriate alternatives or filters for better visibility
4. THE System SHALL cache dark mode preferences to avoid repeated theme calculations
5. WHEN printing from dark mode, THE System SHALL automatically switch to light mode or provide print-optimized styles

**Testing and Validation Requirements:**

1. THE System SHALL be tested with multiple screen readers (NVDA, JAWS, VoiceOver) in dark mode
2. THE System SHALL be validated using automated accessibility testing tools for dark mode compliance
3. THE System SHALL undergo manual testing with users who have visual impairments
4. THE System SHALL be tested across different devices and operating systems for dark mode consistency
5. THE System SHALL maintain accessibility compliance when users have custom system color schemes

**Dark Mode Specific Best Practices:**

1. WHEN pure black backgrounds are used, THE System SHALL avoid them in favor of dark gray (#111827) to reduce eye strain
2. WHEN white text is displayed on dark backgrounds, THE System SHALL use slightly off-white (#f3f4f6) to prevent harsh contrast
3. WHEN shadows are applied in dark mode, THE System SHALL use lighter shadows or borders instead of traditional drop shadows
4. THE System SHALL ensure sufficient color differentiation between adjacent elements in dark mode
5. WHEN gradients are used in dark mode, THE System SHALL maintain accessibility while providing visual appeal

### Requirement 17: Widget Performance Standards (D13 §9.2, D11 §12.1, SRS-RPT-006)

**User Story:** As an administrator, I want dashboard widgets to load quickly and efficiently, so that I can access real-time information without performance delays.

#### Acceptance Criteria

**Widget Refresh Rate Standards (D13 §9.2):**

1. WHEN real-time widgets are displayed, THE System SHALL refresh HelpdeskStatsWidget and SLAComplianceWidget in real-time using Laravel Reverb WebSocket
2. WHEN performance metrics are shown, THE System SHALL refresh PulseOverviewWidget every 30 seconds for optimal balance of accuracy and performance
3. WHEN asset data is displayed, THE System SHALL refresh AssetUtilizationWidget every 5 minutes to reduce database load
4. WHEN loan metrics are shown, THE System SHALL refresh LoanStatsWidget in real-time for critical approval workflows
5. THE System SHALL allow administrators to configure refresh rates within defined ranges (10 seconds to 30 minutes)

**Caching Performance Requirements (D04 §7.3, D11 §12.1):**

1. WHEN widget data is requested, THE System SHALL implement Redis caching with 5-minute TTL for dashboard statistics
2. WHEN expensive queries are executed, THE System SHALL cache results with 15-minute TTL for complex analytics
3. WHEN asset availability is calculated, THE System SHALL cache calendar data with 5-minute TTL to improve response times
4. THE System SHALL implement cache tags for efficient invalidation of related widget data
5. WHEN cache hit rate is measured, THE System SHALL maintain >90% cache hit rate as monitored by Laravel Pulse

**Response Time Standards (D11 §12.1):**

1. WHEN widgets are loaded, THE System SHALL achieve <2 seconds response time (p95) for all dashboard widgets
2. WHEN database queries are executed, THE System SHALL maintain <500ms average query time for widget data
3. WHEN real-time updates occur, THE System SHALL deliver WebSocket notifications within 100ms
4. THE System SHALL implement lazy loading for non-critical widgets to improve initial page load
5. WHEN multiple widgets are rendered, THE System SHALL use efficient batching to minimize database connections

**Laravel Pulse Integration (D13 §9.2, D11 §12.1):**

1. WHEN performance monitoring is active, THE System SHALL integrate Laravel Pulse 1.4.7 metrics into PulseOverviewWidget
2. WHEN system health is displayed, THE System SHALL show CPU usage, memory consumption, and queue job status
3. WHEN slow queries are detected, THE System SHALL highlight performance issues in the dashboard
4. THE System SHALL restrict Laravel Pulse dashboard access to admin and superuser roles only
5. WHEN performance thresholds are exceeded, THE System SHALL trigger alerts through the notification system

**Laravel Horizon Integration (D11 §9, SRS-RPT-006):**

1. WHEN queue monitoring is required, THE System SHALL integrate Laravel Horizon 5.41.0 metrics into HorizonStatsWidget
2. WHEN job processing is displayed, THE System SHALL show queue throughput, failed jobs, and worker status
3. WHEN queue issues are detected, THE System SHALL provide drill-down capabilities to detailed Horizon dashboard
4. THE System SHALL restrict Horizon dashboard access to admin and superuser roles only
5. WHEN queue performance degrades, THE System SHALL trigger automated alerts for proactive monitoring

**Memory and Resource Management:**

1. WHEN widgets are rendered, THE System SHALL limit memory usage to <50MB per widget instance
2. WHEN background processing occurs, THE System SHALL use Laravel Queue for heavy computations
3. WHEN concurrent users access widgets, THE System SHALL implement connection pooling for database efficiency
4. THE System SHALL implement garbage collection for widget instances to prevent memory leaks
5. WHEN server resources are constrained, THE System SHALL gracefully degrade widget functionality

### Requirement 18: Laravel Pulse Dashboard Integration (D11 §9, ICTServe_System_Documentation)

**User Story:** As a system administrator, I want comprehensive performance monitoring through Laravel Pulse integration, so that I can proactively identify and resolve system issues.

#### Acceptance Criteria

**Pulse Dashboard Access (D11 §9, ICTServe_System_Documentation):**

1. WHEN administrators access performance monitoring, THE System SHALL provide Laravel Pulse dashboard at `/admin/pulse` path
2. WHEN user roles are verified, THE System SHALL restrict Pulse access to admin and superuser roles only
3. WHEN Pulse dashboard loads, THE System SHALL display real-time performance metrics with 30-second refresh rate
4. THE System SHALL integrate Pulse metrics into the main admin dashboard through PulseOverviewWidget
5. WHEN performance issues are detected, THE System SHALL provide drill-down capabilities to detailed Pulse views

**Performance Metrics Display (D11 §12.1):**

1. WHEN system performance is monitored, THE System SHALL display response times, database query performance, and queue job status
2. WHEN slow queries are detected, THE System SHALL show query details, execution time, and optimization suggestions
3. WHEN queue metrics are displayed, THE System SHALL show job processing rates, failed jobs, and queue depth
4. THE System SHALL display server health metrics including CPU usage, memory consumption, and disk space
5. WHEN error rates are tracked, THE System SHALL maintain <0.5% error rate (5xx responses) and alert when exceeded

**Alert Thresholds (SRS-RPT-006):**

1. WHEN response times exceed 2 seconds (p95), THE System SHALL trigger performance alerts
2. WHEN database query times exceed 500ms average, THE System SHALL notify administrators
3. WHEN queue job failures exceed 2%, THE System SHALL send immediate notifications
4. THE System SHALL implement configurable alert thresholds for different performance metrics
5. WHEN critical thresholds are breached, THE System SHALL integrate alerts with the notification center

### Requirement 19: Real-Time Widget Updates (D16 Laravel Reverb, SRS-ADM-003)

**User Story:** As an administrator, I want widgets to update in real-time without page refresh, so that I always see current system status and can respond quickly to issues.

#### Acceptance Criteria

**WebSocket Integration (D16 Laravel Reverb):**

1. WHEN system data changes, THE System SHALL broadcast updates via Laravel Reverb WebSocket server
2. WHEN widgets subscribe to updates, THE System SHALL use Laravel Echo client for real-time communication
3. WHEN multiple administrators are online, THE System SHALL synchronize widget updates across all sessions
4. THE System SHALL implement efficient event broadcasting to minimize server load
5. WHEN WebSocket connections are lost, THE System SHALL automatically reconnect and resume real-time updates

**Event Broadcasting Patterns (D16 §10.2):**

1. WHEN tickets are created or updated, THE System SHALL broadcast events to HelpdeskStatsWidget subscribers
2. WHEN loan applications change status, THE System SHALL update LoanStatsWidget in real-time
3. WHEN SLA breaches occur, THE System SHALL immediately notify SLAComplianceWidget
4. THE System SHALL implement role-based event filtering to ensure users only receive relevant updates
5. WHEN system metrics change, THE System SHALL broadcast performance updates to admin users only

**Fallback Mechanisms:**

1. WHEN WebSocket connections fail, THE System SHALL gracefully fall back to periodic polling (every 30 seconds)
2. WHEN network connectivity is poor, THE System SHALL adjust update frequency to maintain performance
3. WHEN server resources are limited, THE System SHALL prioritize critical widget updates
4. THE System SHALL provide manual refresh buttons for all widgets as backup
5. WHEN real-time features are disabled, THE System SHALL clearly indicate refresh status to users

### Requirement 20: Widget Customization and User Preferences (SRS-UX-005, D12 §3.4)

**User Story:** As an administrator, I want to customize which widgets are displayed and their arrangement, so that I can optimize my dashboard for my specific workflow and responsibilities.

#### Acceptance Criteria

**Widget Configuration Interface:**

1. WHEN administrators access widget settings, THE System SHALL provide a drag-and-drop interface for widget reordering
2. WHEN widgets are rearranged, THE System SHALL save layout preferences per user in the database
3. WHEN widget visibility is toggled, THE System SHALL persist show/hide preferences across sessions
4. THE System SHALL provide widget size options (small, medium, large) where applicable
5. WHEN default layouts are needed, THE System SHALL provide role-based default configurations

**User Preference Persistence:**

1. WHEN widget preferences are saved, THE System SHALL store them in the `users.dashboard_layout` JSON column
2. WHEN users log in, THE System SHALL restore their personalized widget arrangement
3. WHEN preferences are updated, THE System SHALL validate configuration to prevent invalid layouts
4. THE System SHALL provide a "Reset to Default" option to restore original widget configuration
5. WHEN sharing configurations, THE System SHALL allow administrators to export/import widget layouts

**Role-Based Widget Access:**

1. WHEN admin users access the dashboard, THE System SHALL display operational widgets (tickets, loans, SLA)
2. WHEN superuser accesses the dashboard, THE System SHALL include additional system configuration and audit widgets
3. WHEN staff users access their dashboard, THE System SHALL show personal widgets (submission history, notifications)
4. THE System SHALL hide sensitive widgets (audit logs, system metrics) from users without appropriate permissions
5. WHEN new widgets are added, THE System SHALL automatically assign them to appropriate user roles

### Requirement 21: Cloud Hybrid AI Dashboard Integration (D18 v1.0.1, SRS-AI-001)

**User Story:** As an admin or superuser, I want comprehensive AI system monitoring through dashboard widgets, so that I can monitor Ollama and AWS Bedrock performance, manage AI costs, and ensure optimal AI service delivery.

#### Acceptance Criteria

**AI Performance Monitoring Widgets:**

1. WHEN AI monitoring is accessed, THE System SHALL display AIPerformanceWidget showing Ollama response times, Bedrock API latency, and model routing efficiency
2. WHEN AI costs are tracked, THE System SHALL provide AICostWidget displaying Bedrock usage costs, token consumption, and cost optimization recommendations
3. WHEN AI health is monitored, THE System SHALL show AIHealthWidget with Ollama server status, AWS Bedrock connectivity, and DuckDuckGo integration status
4. THE System SHALL restrict AI monitoring widgets to admin and superuser roles only per Four_Role_RBAC
5. WHEN AI performance thresholds are exceeded, THE System SHALL integrate alerts with the notification system

**AI Usage Analytics Widgets:**

1. WHEN AI usage is analyzed, THE System SHALL display AIUsageStatsWidget showing query volume, model routing decisions, and user interaction patterns
2. WHEN conversation analytics are needed, THE System SHALL provide ConversationAnalyticsWidget with conversation length, completion rates, and user satisfaction metrics
3. WHEN FAQ Bot performance is monitored, THE System SHALL show FAQBotStatsWidget with response accuracy, query resolution rates, and knowledge base effectiveness
4. THE System SHALL implement real-time updates for AI widgets using Laravel Reverb WebSocket integration
5. WHEN AI data is cached, THE System SHALL use 2-minute TTL for real-time AI metrics and 10-minute TTL for historical analytics

**AI Configuration Management Widgets:**

1. WHEN AI model configuration is managed, THE System SHALL provide ModelConfigWidget for adjusting temperature, max_tokens, and routing thresholds
2. WHEN knowledge base management is required, THE System SHALL display KnowledgeBaseWidget showing document ingestion status, PII detection results, and semantic search performance
3. WHEN auto-reply management is needed, THE System SHALL provide AutoReplyQueueWidget displaying pending AI-generated responses awaiting admin approval
4. THE System SHALL ensure all AI configuration widgets maintain WCAG 2.2 AA compliance with proper contrast ratios and keyboard navigation
5. WHEN AI widgets display sensitive information, THE System SHALL implement additional authorization checks beyond role-based access

**Integration with Existing Dashboard Architecture:**

1. WHEN AI widgets are displayed, THE System SHALL follow the same caching, performance, and real-time update patterns as existing widgets
2. WHEN AI widgets are categorized, THE System SHALL place performance widgets in the header section and analytics widgets in the content section
3. WHEN AI widget data is exported, THE System SHALL include AI metrics in unified dashboard reports and analytics exports
4. THE System SHALL implement consistent color schemes for AI widgets using MyDS semantic colors and maintaining accessibility standards
5. WHEN AI widgets are customized, THE System SHALL support the same user preference and layout customization features as other dashboard widgets

### Requirement 22: Filament Dashboard Visual Consistency Fixes (D12 §4, D14 MyDS v2025.2, Screenshot Audit)

**User Story:** As an administrator, I want the Filament dashboard to display consistent, properly styled widgets with correct spacing, shadows, and typography, so that I have a professional and visually coherent admin experience.

**Context:** Visual audit of dashboard screenshots against v3.6.1 documentation (D12, D14) revealed several styling inconsistencies that need to be addressed to achieve full MyDS v2025.2 compliance.

#### Acceptance Criteria

**Widget Layout and Grid System (MyDS 12-8-4):**

1. WHEN widgets are rendered on desktop (≥1024px), THE System SHALL use 12-column grid with 24px gap spacing
2. WHEN widgets are rendered on tablet (768px-1023px), THE System SHALL use 8-column grid with 24px gap spacing
3. WHEN widgets are rendered on mobile (<768px), THE System SHALL use 4-column grid with 18px gap spacing
4. WHEN multiple widgets are displayed in a row, THE System SHALL ensure consistent vertical alignment
5. WHEN widget sections are rendered, THE System SHALL maintain 24px (--space-6) spacing between sections

**Widget Card Styling (D14 §7.5 Shadow System):**

1. WHEN widget cards are rendered, THE System SHALL apply `shadow-card` elevation (0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05))
2. WHEN widget cards are displayed, THE System SHALL use `rounded-lg` (12px) border-radius
3. WHEN widget backgrounds are rendered, THE System SHALL use `bg-white` (light mode) or `bg-gray-800` (dark mode)
4. WHEN widget borders are needed, THE System SHALL use `border-gray-200` (light mode) or `border-gray-700` (dark mode)
5. WHEN widget padding is applied, THE System SHALL use consistent `p-6` (24px) internal padding

**Typography Consistency (D14 §5):**

1. WHEN widget headers are displayed, THE System SHALL use Poppins font at `text-xl font-semibold` (20px, 600 weight)
2. WHEN widget metric numbers are displayed, THE System SHALL use `text-3xl font-bold` (30px, 700 weight) with appropriate color coding
3. WHEN widget labels are displayed, THE System SHALL use Inter font at `text-sm text-gray-600` (14px)
4. WHEN widget body text is displayed, THE System SHALL use Inter font at `text-base` (16px) with `line-height: 1.5`
5. WHEN widget secondary text is shown, THE System SHALL use `text-gray-500` with 4.6:1 contrast ratio

**Status Badge Styling (D14 §6.7):**

1. WHEN "Tidak Aktif" (Inactive) status is displayed, THE System SHALL use `bg-gray-100 text-gray-700` with `heroicon-o-x-circle` icon
2. WHEN "Aktif" (Active) status is displayed, THE System SHALL use `bg-success-50 text-success-700` with `heroicon-o-check-circle` icon
3. WHEN "Dalam Proses" (In Progress) status is displayed, THE System SHALL use `bg-warning-50 text-warning-700` with `heroicon-o-clock` icon
4. WHEN status badges are rendered, THE System SHALL include both icon and text (not color alone per WCAG 1.4.1)
5. WHEN status badges are displayed, THE System SHALL use `rounded-full px-3 py-1 text-sm font-medium` styling

**Empty State Handling:**

1. WHEN widgets have no data, THE System SHALL display meaningful empty state with icon, title, and description
2. WHEN chart widgets have no data, THE System SHALL display "Tiada data tersedia" message with appropriate styling
3. WHEN metric widgets show zero values, THE System SHALL display "0" with `text-gray-400` color and explanatory text
4. WHEN empty states are displayed, THE System SHALL use `heroicon-o-chart-bar` or relevant icon at `w-12 h-12 text-gray-300`
5. WHEN empty states include actions, THE System SHALL provide a call-to-action button with proper styling

**Chart Widget Visual Fixes (D14 §8, D12 §7):**

1. WHEN pie charts are rendered, THE System SHALL use MyDS color palette: Primary 500, Success 500, Warning 500, Info 500, Secondary 500
2. WHEN chart legends are displayed, THE System SHALL use `text-sm` (14px) with proper spacing and color indicators
3. WHEN chart containers are rendered, THE System SHALL use `min-h-[300px]` for consistent sizing
4. WHEN chart backgrounds are displayed, THE System SHALL use `bg-gray-50` (light mode) or `bg-gray-800` (dark mode)
5. WHEN charts have no data, THE System SHALL display centered empty state instead of broken/empty chart

**Stats Overview Widget Fixes:**

1. WHEN stats overview widgets are rendered, THE System SHALL display metrics in consistent card format with icon, value, and label
2. WHEN stats values are displayed, THE System SHALL use color coding: success (green), warning (orange), danger (red), info (blue)
3. WHEN stats icons are displayed, THE System SHALL use `w-5 h-5` (20px) Heroicons with matching color
4. WHEN stats trends are shown, THE System SHALL display trend arrows (↑/↓) with percentage change
5. WHEN stats cards are arranged, THE System SHALL use `grid grid-cols-2 md:grid-cols-4 gap-4` layout

**Interactive Element Styling:**

1. WHEN hover states are triggered on widgets, THE System SHALL apply `hover:shadow-lg` transition with 200ms duration
2. WHEN focus states are applied, THE System SHALL display 3px outline with 2px offset using `--fr-primary` token
3. WHEN buttons within widgets are rendered, THE System SHALL use `shadow-button` elevation
4. WHEN interactive elements are clicked, THE System SHALL provide visual feedback with `active:scale-98` transform
5. WHEN tooltips are displayed, THE System SHALL use dark background with white text and 200ms delay

**Responsive Behavior Fixes:**

1. WHEN viewport is below 768px, THE System SHALL stack all widgets vertically with full width
2. WHEN viewport is between 768px-1023px, THE System SHALL display widgets in 2-column layout
3. WHEN viewport is 1024px or above, THE System SHALL display widgets in 3-4 column layout based on widget type
4. WHEN widgets are resized, THE System SHALL maintain aspect ratios for chart widgets
5. WHEN mobile navigation is triggered, THE System SHALL collapse sidebar and show hamburger menu

### Requirement 23: Screenshot-Identified Visual Defects Resolution

**User Story:** As an administrator, I want all visual defects identified in the dashboard screenshots to be resolved, so that the dashboard matches the v3.6.1 design specifications.

**Context:** Specific visual defects identified from screenshot analysis that require immediate attention.

#### Acceptance Criteria

**Identified Defects from Screenshots:**

1. WHEN the "Carta & Analitik" section is rendered, THE System SHALL display properly styled chart widgets instead of empty gray boxes
2. WHEN the "Pinjaman Peralatan Perkakasan ICTServe" section is displayed, THE System SHALL show consistent card styling with proper shadows
3. WHEN stats cards show "Tidak Aktif" status, THE System SHALL use proper badge styling with icon and contrasting colors
4. WHEN the sidebar navigation is displayed, THE System SHALL use consistent icon sizing (w-5 h-5) and proper active state highlighting
5. WHEN the dashboard header is rendered, THE System SHALL display user menu, notifications, and theme toggle with proper spacing

**Widget-Specific Fixes:**

1. WHEN `HelpdeskStatsOverview` widget is rendered, THE System SHALL display all metrics with consistent card styling and proper color coding
2. WHEN `AssetLoanStatsOverview` widget is rendered, THE System SHALL display loan statistics with proper status badges and trend indicators
3. WHEN `TicketVolumeChart` widget is rendered, THE System SHALL display chart with proper MyDS colors and legend styling
4. WHEN `AssetUtilizationWidget` widget is rendered, THE System SHALL display utilization data with proper chart styling
5. WHEN `QuickActionsWidget` widget is rendered, THE System SHALL display action buttons in consistent grid layout with proper hover states

**Color Token Application:**

1. WHEN primary actions are displayed, THE System SHALL use `#0056B3` (Primary 500) consistently
2. WHEN success states are shown, THE System SHALL use `#1B7C54` (Success 500) with proper contrast
3. WHEN warning states are displayed, THE System SHALL use `#CC7700` (Warning 500) with proper contrast
4. WHEN danger states are shown, THE System SHALL use `#B3002D` (Danger 500) with proper contrast
5. WHEN neutral backgrounds are rendered, THE System SHALL use `#f9fafb` (Gray 50) for page background

**Spacing and Alignment Fixes:**

1. WHEN widget sections are rendered, THE System SHALL maintain consistent 24px vertical spacing between sections
2. WHEN widget cards are displayed in a row, THE System SHALL use consistent 24px horizontal gap
3. WHEN widget content is rendered, THE System SHALL use consistent 24px internal padding
4. WHEN widget headers and content are displayed, THE System SHALL maintain 16px spacing between header and content
5. WHEN multiple stat cards are displayed, THE System SHALL ensure equal height across all cards in a row
