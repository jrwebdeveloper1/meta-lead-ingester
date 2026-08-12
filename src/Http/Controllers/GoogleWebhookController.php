<?php

namespace Vendor\MetaLeadIngester\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Vendor\MetaLeadIngester\Jobs\ProcessGoogleLeadJob;
use Illuminate\Support\Facades\Log;

class GoogleWebhookController extends Controller
{
    /**
     * Handle the POST webhook request for receiving Google Ads Leads.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function receive(Request $request): JsonResponse
    {
        $payload = $request->all();
        $account = $request->attributes->get('google_account');

        if (!$account) {
            // Should be caught by middleware, but fallback here
            return response()->json(['error' => 'Account not found contextually'], 403);
        }

        // Google Ads lead webhooks are straightforward JSON payloads
        if (isset($payload['lead_id'])) {
            // Dispatch job to process the lead data
            ProcessGoogleLeadJob::dispatch($account->id, $payload)
                ->onQueue(config('meta-lead-ingester.queue', 'default'));
        } else {
            Log::info('Google Webhook received without lead_id (could be a test/ping)', ['payload' => $payload]);
        }

        // Return a 200 OK response to acknowledge receipt of the event
        return response()->json(['status' => 'Event received'], 200);
    }
}
