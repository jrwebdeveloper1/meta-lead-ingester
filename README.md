# Meta Lead Ingester

A standalone plug-and-play Laravel package for capturing, verifying, and ingesting Meta (Facebook/Instagram) Lead Ads in real-time.

## Installation

You can install the package via composer:

```bash
composer require vendor/meta-lead-ingester
```
*(Note: Change `vendor/meta-lead-ingester` to your actual packagist vendor and package name if published).*

## Setup

1. Publish the configuration and migrations:
```bash
php artisan vendor:publish --tag=meta-lead-ingester-config
php artisan vendor:publish --tag=meta-lead-ingester-migrations
```

2. Run the migrations:
```bash
php artisan migrate
```

3. Update your `.env` with the necessary Meta App variables:
```dotenv
META_APP_SECRET="your-meta-app-secret"
META_GRAPH_API_VERSION="v20.0"
META_LEAD_INGESTER_ROUTE_PREFIX="api/meta-lead-ingester"
META_LEAD_INGESTER_QUEUE="default"
```

## Usage

This package automatically exposes webhook routes (by default `api/meta-lead-ingester/webhook`) that you can plug into your Meta App Dashboard. When Meta sends a webhook event, the package will automatically verify the HMAC signature, dispatch a queue job to fetch the lead details, and store it in your database seamlessly.
