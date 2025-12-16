# Requirements Document

## Introduction

This specification defines the requirements for implementing real-time notifications and broadcasting capabilities in ICTServe v3.6.0 using Laravel Reverb. The system enables instant status updates, notification delivery, and AI streaming responses without page refreshes, supporting both authenticated users and guests through a dual-channel strategy.

The implementation aligns with D16 Broadcasting Setup documentation and integrates with the existing True Hybrid Architecture (guest + authenticated + admin layers).

## Glossary

- **Laravel Reverb**: Official Laravel WebSocket server for real-time broadcasting
- **Laravel Echo**: JavaScript library for subscribing to channels and listening for events
- **Private Channel**: Authenticated channel requiring authorization callback
- **Dual Channel Strategy**: Broadcasting to `private-user.{id}` for authenticated users and `private-{type}.{uuid}` for guests
- **SSE (Server-Sent Events)**: HTTP-based streaming for AI response chunks
- **Broadcasting Event**: PHP class implementing `ShouldBroadcast` interface
- **Queue Worker**: Background process handling asynchronous broadcast jobs
- **Status Token**: UUID-based token for guest channel authorization

## Requirements

### Requirement 1

**User Story:** As an authenticated staff member, I want to receive real-time notifications when my helpdesk ticket status changes, so that I can stay informed without refreshing the page.

#### Acceptance Criteria

1. WHEN a helpdesk ticket status changes THEN the system SHALL broadcast a `status.updated` event to the `private-user.{userId}` channel within 2 seconds
2. WHEN a notification is created for an authenticated user THEN the system SHALL broadcast a `notification.created` event to the user's private channel
3. WHEN the frontend receives a status update event THEN the system SHALL update the UI status badge without page reload
4. WHEN the frontend receives a notification event THEN the system SHALL increment the notification bell counter and display a toast message
5. IF the WebSocket connection is lost THEN the system SHALL attempt automatic reconnection with exponential backoff

### Requirement 2

**User Story:** As a guest user tracking my ticket, I want to receive real-time status updates on the status check page, so that I can see changes immediately without refreshing.

#### Acceptance Criteria

1. WHEN a guest accesses the status check page with a valid status token THEN the system SHALL subscribe to the `private-ticket.{uuid}` channel
2. WHEN a ticket status changes for a guest submission THEN the system SHALL broadcast to the UUID-based channel
3. WHEN the guest's channel receives a status update THEN the system SHALL update the status display in real-time
4. WHEN authorizing a guest channel THEN the system SHALL validate the status token against the ticket UUID
5. IF the status token is invalid or expired THEN the system SHALL deny channel subscription and display an error message

### Requirement 3

**User Story:** As a system administrator, I want the broadcasting infrastructure to be properly configured, so that real-time features work reliably in all environments.

#### Acceptance Criteria

1. WHEN the application starts THEN the system SHALL load Reverb configuration from environment variables
2. WHEN configuring broadcasting THEN the system SHALL support both Reverb (primary) and Pusher (fallback) drivers
3. WHEN the Reverb server starts THEN the system SHALL listen on the configured host and port
4. WHEN a broadcast event is dispatched THEN the system SHALL queue the job for asynchronous processing via Redis
5. WHEN the queue worker processes a broadcast job THEN the system SHALL deliver the message to the WebSocket server within 1 second

### Requirement 4

**User Story:** As a developer, I want broadcasting events to follow consistent patterns, so that the codebase remains maintainable and testable.

#### Acceptance Criteria

1. WHEN creating a broadcast event THEN the event class SHALL implement `ShouldBroadcast` interface
2. WHEN defining broadcast channels THEN the event SHALL use the `broadcastOn()` method returning appropriate channel types
3. WHEN serializing event data THEN the event SHALL use `broadcastWith()` to define the payload structure
4. WHEN naming broadcast events THEN the system SHALL use dot-notation format (e.g., `status.updated`, `notification.created`)
5. WHEN testing broadcast events THEN the system SHALL support `Event::fake()` and `Broadcast::assertDispatched()` assertions

### Requirement 5

**User Story:** As a frontend developer, I want Laravel Echo properly initialized, so that I can subscribe to channels and listen for events.

#### Acceptance Criteria

1. WHEN the application loads THEN the system SHALL initialize Laravel Echo with Reverb configuration
2. WHEN Echo initializes THEN the system SHALL configure WebSocket transport with TLS support based on environment
3. WHEN subscribing to private channels THEN Echo SHALL send authorization requests to `/broadcasting/auth`
4. WHEN the authorization endpoint receives a request THEN the system SHALL validate the user session or status token
5. IF Reverb is unavailable THEN the system SHALL fallback to Pusher configuration if available

### Requirement 6

**User Story:** As an authenticated user, I want to receive AI streaming responses in real-time, so that I can see the AI assistant's response as it generates.

#### Acceptance Criteria

1. WHEN an AI response starts generating THEN the system SHALL broadcast `ai.streaming.started` event
2. WHEN AI generates response chunks THEN the system SHALL broadcast `ai.streaming.chunk` events with content fragments
3. WHEN AI response completes THEN the system SHALL broadcast `ai.streaming.completed` event
4. WHEN displaying streaming content THEN the frontend SHALL append chunks progressively with typing indicator
5. IF an AI error occurs THEN the system SHALL broadcast `ai.error.occurred` event with error details and retry option

### Requirement 7

**User Story:** As a system operator, I want broadcasting to be secure and performant, so that the system handles load efficiently without security vulnerabilities.

#### Acceptance Criteria

1. WHEN authorizing private channels THEN the system SHALL verify user ownership or valid status token
2. WHEN broadcasting sensitive data THEN the system SHALL exclude PII and credentials from event payloads
3. WHEN handling high broadcast volume THEN the system SHALL use Redis queue with configurable concurrency
4. WHEN the WebSocket server receives connections THEN the system SHALL enforce rate limiting per client
5. WHEN logging broadcast events THEN the system SHALL record event type, channel, and timestamp for audit purposes

### Requirement 8

**User Story:** As a developer, I want comprehensive tests for broadcasting functionality, so that I can ensure reliability and catch regressions.

#### Acceptance Criteria

1. WHEN testing broadcast events THEN the test suite SHALL verify event dispatching with correct channels
2. WHEN testing channel authorization THEN the test suite SHALL verify authenticated and guest access patterns
3. WHEN testing frontend integration THEN the test suite SHALL verify Echo subscription and event handling
4. WHEN testing queue processing THEN the test suite SHALL verify broadcast job execution
5. WHEN running the test suite THEN all broadcasting tests SHALL pass with minimum 80% code coverage
