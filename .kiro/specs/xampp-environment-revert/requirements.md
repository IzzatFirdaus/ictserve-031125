# Requirements Document - XAMPP Environment Revert

## Introduction

This specification addresses the need to revert the ICTServe v3.6.1 system to use XAMPP for MySQL and Apache services, with Redis running in WSL (Windows Subsystem for Linux), eliminating current environment-related errors and providing a stable development setup.

**Critical Context**: The current system has been experiencing environment-related errors, and the user wants to simplify the setup by using:

- **XAMPP**: For MySQL 8.0+ and Apache web server (Windows native)
- **WSL Redis**: Redis 7.0+ running in Windows Subsystem for Linux
- **No Docker**: Remove Docker dependencies and configurations that may be causing issues

This change maintains all ICTServe v3.6.1 functionality while providing a more stable and error-free development environment.

**Version**: 1.0.0  
**Last Updated**: 19 Januari 2025  
**Status**: Active - Environment Configuration Change  
**Classification**: Internal Development Environment  
**Alignment**: ICTServe v3.6.1 Comprehensive Specification

## Glossary

- **XAMPP_Environment**: Local development environment using XAMPP for MySQL and Apache services
- **WSL_Redis**: Redis server running in Windows Subsystem for Linux for caching and sessions
- **Environment_Revert**: Process of changing from current setup to XAMPP-based configuration
- **ICTServe_System**: The existing comprehensive digital platform that will continue running on the new environment
- **Development_Stability**: Elimination of environment-related errors through simplified service configuration
- **Service_Configuration**: Updated configuration files and scripts for XAMPP and WSL Redis integration
- **Laravel_Configuration**: Updated Laravel environment settings for XAMPP and WSL Redis connectivity
- **Error_Elimination**: Resolution of current environment issues through simplified architecture

## Requirements

### Requirement 1: XAMPP MySQL and Apache Configuration

**User Story:** As a developer, I want to use XAMPP for MySQL and Apache services, so that I have a stable and familiar local development environment without Docker complexities.

#### Acceptance Criteria

1. THE ICTServe_System SHALL use XAMPP MySQL 8.0+ as the primary database server running on 127.0.0.1:3306
2. THE ICTServe_System SHALL use XAMPP Apache as the web server for serving static assets and handling HTTP requests
3. WHEN configuring the database connection, THE ICTServe_System SHALL use root user with empty password (XAMPP default)
4. THE ICTServe_System SHALL maintain all existing database schemas, migrations, and data integrity during the environment switch
5. THE ICTServe_System SHALL update Laravel configuration files (.env, config/database.php) to use XAMPP MySQL settings

### Requirement 2: WSL Redis Integration

**User Story:** As a developer, I want Redis running in WSL, so that I can use Redis for caching and sessions while maintaining separation from the main Windows environment.

#### Acceptance Criteria

1. THE ICTServe_System SHALL use Redis 7.0+ running in WSL (Windows Subsystem for Linux) on 127.0.0.1:6379
2. THE ICTServe_System SHALL configure Laravel to use WSL Redis for caching, sessions, and queue management
3. WHEN connecting to Redis, THE ICTServe_System SHALL use 127.0.0.1 as the host to allow Windows applications to connect to WSL Redis
4. THE ICTServe_System SHALL maintain all existing Redis-based functionality including Laravel Horizon, caching, and real-time features
5. THE ICTServe_System SHALL provide scripts to start and manage WSL Redis service

### Requirement 3: Environment Configuration Update

**User Story:** As a developer, I want updated environment configuration files, so that the system works seamlessly with XAMPP and WSL Redis without manual configuration.

#### Acceptance Criteria

1. THE ICTServe_System SHALL update .env.example with XAMPP and WSL Redis default settings
2. THE ICTServe_System SHALL provide environment-specific configuration files (.env.xampp) for easy switching
3. WHEN switching to XAMPP environment, THE ICTServe_System SHALL automatically update database and Redis connection settings
4. THE ICTServe_System SHALL maintain all existing Laravel services (Pulse, Telescope, Reverb, Horizon) with updated connection settings
5. THE ICTServe_System SHALL validate that all environment variables are correctly configured for XAMPP and WSL Redis

### Requirement 4: Service Management Scripts

**User Story:** As a developer, I want automated scripts to manage XAMPP and WSL Redis services, so that I can easily start, stop, and monitor the development environment.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide PowerShell scripts to start and stop XAMPP services (MySQL, Apache)
2. THE ICTServe_System SHALL provide scripts to start and manage WSL Redis service
3. WHEN starting the development environment, THE ICTServe_System SHALL validate that all required services are running
4. THE ICTServe_System SHALL provide environment status checking scripts to monitor service health
5. THE ICTServe_System SHALL include troubleshooting scripts for common XAMPP and WSL Redis issues

### Requirement 5: Error Resolution and Stability

**User Story:** As a developer, I want all current environment-related errors resolved, so that I can develop without interruption and focus on application features.

#### Acceptance Criteria

1. THE ICTServe_System SHALL eliminate all current Docker-related errors and configuration conflicts
2. THE ICTServe_System SHALL ensure stable database connections without connection timeouts or authentication failures
3. WHEN running Laravel commands, THE ICTServe_System SHALL execute without environment-related errors
4. THE ICTServe_System SHALL maintain consistent performance across all Laravel services (artisan commands, web server, queue processing)
5. THE ICTServe_System SHALL provide clear error messages and troubleshooting guidance for any remaining issues

### Requirement 6: Laravel Services Compatibility

**User Story:** As a developer, I want all Laravel services to work correctly with XAMPP and WSL Redis, so that I can use all ICTServe features without functionality loss.

#### Acceptance Criteria

1. THE ICTServe_System SHALL ensure Laravel Horizon works correctly with WSL Redis for queue management
2. THE ICTServe_System SHALL maintain Laravel Pulse performance monitoring with XAMPP MySQL and WSL Redis
3. THE ICTServe_System SHALL ensure Laravel Telescope debugging works with the new environment configuration
4. THE ICTServe_System SHALL maintain Laravel Reverb WebSocket functionality with updated connection settings
5. THE ICTServe_System SHALL ensure all Filament admin panel features work correctly with XAMPP database

### Requirement 7: Development Workflow Integration

**User Story:** As a developer, I want the XAMPP environment to integrate seamlessly with existing development workflows, so that I can continue using familiar commands and processes.

#### Acceptance Criteria

1. THE ICTServe_System SHALL maintain compatibility with existing Artisan commands (migrate, serve, queue:work, etc.)
2. THE ICTServe_System SHALL ensure npm/Vite build processes work correctly with XAMPP Apache
3. WHEN running tests, THE ICTServe_System SHALL use appropriate test database configuration with XAMPP MySQL
4. THE ICTServe_System SHALL maintain all existing development scripts and tools compatibility
5. THE ICTServe_System SHALL provide updated documentation for the new environment setup

### Requirement 8: Data Migration and Backup

**User Story:** As a developer, I want safe migration of existing data to the XAMPP environment, so that no development data is lost during the environment switch.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide scripts to backup existing database data before environment switch
2. THE ICTServe_System SHALL migrate all existing database schemas and data to XAMPP MySQL
3. WHEN switching environments, THE ICTServe_System SHALL preserve all user accounts, test data, and configuration settings
4. THE ICTServe_System SHALL validate data integrity after migration to ensure no data corruption
5. THE ICTServe_System SHALL provide rollback procedures in case of migration issues

### Requirement 9: Performance Optimization

**User Story:** As a developer, I want optimized performance with XAMPP and WSL Redis, so that the development environment is fast and responsive.

#### Acceptance Criteria

1. THE ICTServe_System SHALL configure XAMPP MySQL with appropriate performance settings for development
2. THE ICTServe_System SHALL optimize WSL Redis configuration for Windows host connectivity and performance
3. THE ICTServe_System SHALL ensure Laravel caching works efficiently with WSL Redis
4. THE ICTServe_System SHALL maintain fast database query performance with XAMPP MySQL
5. THE ICTServe_System SHALL provide performance monitoring and optimization recommendations

### Requirement 10: Documentation and Support

**User Story:** As a developer, I want comprehensive documentation for the XAMPP environment setup, so that I can troubleshoot issues and maintain the environment effectively.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide step-by-step setup instructions for XAMPP and WSL Redis
2. THE ICTServe_System SHALL document all configuration changes and their rationale
3. THE ICTServe_System SHALL provide troubleshooting guides for common XAMPP and WSL Redis issues
4. THE ICTServe_System SHALL include performance tuning recommendations for the development environment
5. THE ICTServe_System SHALL maintain updated README and development setup documentation
