# app Directory - GEMINI Instructions

This file provides instructions for the Gemini AI related to the `app` directory of the ICTServe project.

## Directory Overview

The `app` directory contains the core application logic. This includes:

* **Models:** Eloquent models that interact with the database.
* **Http:** Controllers, middleware, and requests.
* **Console:** Artisan commands.
* **Providers:** Service providers that bootstrap the application.
* **...and more:** Other components like Events, Jobs, Listeners, Mail, etc.

## Instructions

* **Models:** When creating or modifying models, ensure they follow the existing structure. Pay attention to fillable attributes, relationships, and any existing traits.
* **Controllers:** Keep controllers focused on a single responsibility. Use dependency injection where appropriate.
* **Business Logic:** Place business logic in services or other appropriate classes, not directly in controllers or routes.
* **Filament:** This project appears to use Filament for its admin panel. When working with Filament resources, follow the established patterns in the `app/Filament` directory.
