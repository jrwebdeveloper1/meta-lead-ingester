# Meta & Google Lead Ingester

A standalone plug-and-play Laravel package for capturing, verifying, and ingesting Meta (Facebook/Instagram) and Google Ads Lead Forms in real-time.

## Installation

You can install the package via composer:

```bash
composer require jrwebdeveloper1/meta-lead-ingester
```

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

3. Update your `.env` with the necessary App variables:
```dotenv
META_APP_SECRET="your-meta-app-secret"
META_GRAPH_API_VERSION="v20.0"
META_LEAD_INGESTER_ROUTE_PREFIX="api/meta-lead-ingester"
META_LEAD_INGESTER_QUEUE="default"
```

## Usage

### Dashboard Configuration
The package comes with a built-in dashboard where you can add and manage your Meta and Google account credentials.
Once installed, you can access the dashboard in your browser by visiting:
```text
/meta-lead-ingester/dashboard
```
*(Note: If you customize the dashboard route prefix in your `config/meta-lead-ingester.php`, use that URL instead.)*

### Meta Lead Ads
This package exposes a webhook route (by default `api/meta-lead-ingester/webhook`) that you can plug into your Meta App Dashboard. When Meta sends a webhook event, the package automatically verifies the HMAC signature, dispatches a queue job to fetch lead details, and stores it in your database seamlessly.

### Google Ads Leads
The package also supports Google Lead Forms out of the box via the `api/meta-lead-ingester/google/webhook` endpoint.
1. Insert a record into the `google_accounts` table with a secure `google_key`.
2. Configure your Google Ads Lead Form webhook URL to point to this endpoint and provide your Google Key.
3. The package will automatically verify incoming payloads and securely save the leads to the `google_leads` table via a background job.
