# Laravel Product Sync Task

This repository contains a Laravel backend task for **synchronizing products** from a public API to a local database using queued jobs, batch processing, notifications, and a clean architecture with repository and service layers.  

---

## Table of Contents

- [Requirements](#requirements)  
- [Installation And Initialization](#installation-and-initialization)   
- [Database Setup](#database-setup)
- [Mail Setup](#mail-setup)  
- [Queue Setup](#queue-setup)
- [Migrate And Seeding The Database ](#migrate-and-seeding-the-database)
- [Running the Product Sync](#running-the-product-sync)  
- [Testing](#testing)  
- [Notes](#notes)  

---

## Requirements

- PHP >= 8.1
- Composer  
- MySQL
- Laravel 12
- Redis (for Horizon & queue processing)
- Laravel Horizon
- Mailtrap or any SMTP server (for notifications)
- Postman
- VSCode Or PHPStorm

---

## Installation and Initialization

- Clone the repository and install dependencies:
   - Run the following commands in your terminal.
     - `git clone https://github.com/devahmedsaber/sync-products-task.git`
     - `cd sync-products-task`
     - `composer install`

- Environment:
   - Run the following commands in your terminal.
     - cp .env.example .env
     - php artisan key:generate
     
---

## Database Setup

- Database (MYSQL):
   - DB_CONNECTION=mysql
   - DB_HOST=127.0.0.1
   - DB_PORT=3306
   - DB_DATABASE=sync_products
   - DB_USERNAME=root
   - DB_PASSWORD=your_db_password
  
---

## Mail Setup

- Mail (MAILTRAP):
   - MAIL_MAILER=smtp
   - MAIL_HOST=sandbox.smtp.mailtrap.io
   - MAIL_PORT=587
   - MAIL_USERNAME=your_mailtrap_username
   - MAIL_PASSWORD=your_mailtrap_password
   - MAIL_ENCRYPTION=null
   - MAIL_FROM_ADDRESS=no-reply@example.com
   - MAIL_FROM_NAME="Sync Product"

---

## Queue Setup

- Use the database queue for simple local testing:
     - QUEUE_CONNECTION=database
- Or, for Horizon/Redis, run these commands in the terminal:
     - brew install redis
     - brew services start redis
- For Horizon/Redis (update .env file):
     - QUEUE_CONNECTION=redis
     - REDIS_HOST=127.0.0.1
     - REDIS_PASSWORD=null
     - REDIS_PORT=6379
  
---

## Migrate and Seeding The Database
   - php artisan migrate --seed
     - Tables created:
       - users (admin user created)
       - products
       - categories
       - sync_logs
       - jobs (for queued jobs)
       - job_batches (for batch processing)

---

## Running the Product Sync
   - There are multiple ways to run the product sync:
     - Trigger via API Request:
        - Send a POST request to: `http://127.0.0.1:8000/api/sync-products`
            - Response:
              {
                  "message": "Product sync has been triggered successfully"
              }
            - Jobs will be dispatched to the queue.
            - Use queue worker or Horizon to process them.
   - Run Directly via Artisan Command (with Progress Bar):
        - php artisan sync:products
          - Runs the sync immediately.
          - Displays a progress bar in the console.
          - Processes all products in batches and downloads images.
   - Using Queue Worker (Database Queue):
       - php artisan queue:work --queue=products
           - Picks up jobs dispatched by API or command.
           - Processes products asynchronously.
           - Works well for local development without Redis/Horizon.
   - Using Laravel Horizon (Redis Queue):
     1. Start Redis locally:
        - brew services start redis
     2. Run Horizon:
        - php artisan horizon
            - Open `http://127.0.0.1:8000/horizon` to monitor queued jobs.
            - Horizon is suitable for production-level queues.
   - Scheduled Sync (via Cron/Scheduler):
        - Add this code
          `$schedule->command('sync:products')->everyMinute();` to app/Console/Kernel.php
        - If you are using Laravel 12 add this code to `app/routes/console.php` instead of `app/Console/Kernel.php`.
        - Run the scheduler locally: php artisan schedule:work
            - Automatically triggers the sync periodically.
            - Combines with queue workers or Horizon for processing.
    
---

## Testing
   - Unit tests:
     - php artisan test --testsuite=Unit
       - Tests ProductService logic: create, update, skip products.
       - Uses mocked repositories.
   - Feature tests:
     - php artisan test --testsuite=Feature
       - Tests the API endpoint /api/sync-products.
       - Verifies job dispatching and response.
      
---

## Notes
   - API URL is configurable via .env:
     - FAKE_API_URL=https://fakestoreapi.com/products
   - Duplicate external IDs are handled automatically.
   - Products missing required fields are skipped.
   - Images are downloaded and stored locally in storage/app/public/products.
   - Repository and Service layers used for a clean architecture.
   - Sync logs saved in sync_logs table for summary.
   - Notifications sent via email after each sync batch. 
