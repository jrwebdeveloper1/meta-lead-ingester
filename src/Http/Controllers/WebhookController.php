<?php

namespace Vendor\MetaLeadIngester\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Vendor\MetaLeadIngester\Models\MetaAccount;
use Vendor\MetaLeadIngester\Jobs\ProcessMetaLeadJob;

class WebhookController extends Controller
{
    /**
     * Handle the GET webhook request for Meta verification handshake.
     *
     * @param Request $request
     * @return Response
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token) {
            // Find a MetaAccount that expects this verify_token
            $account = MetaAccount::where('verify_token', $token)->first();

            if ($account) {
                return response($challenge, 200);
            }
            
            // If verify_token doesn't match any account
            return response('Forbidden', 403);
        }

        return response('Bad Request', 400);
    }

    /**
     * Handle the POST webhook request for receiving Meta Lead Ads.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function receive(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (!isset($payload['object']) || $payload['object'] !== 'page') {
            return response()->json(['status' => 'Event discarded'], 200);
        }

        if (isset($payload['entry']) && is_array($payload['entry'])) {
            foreach ($payload['entry'] as $entry) {
                $pageId = $entry['id'] ?? null;
                
                if (isset($entry['changes']) && is_array($entry['changes'])) {
                    foreach ($entry['changes'] as $change) {
                        if (
                            isset($change['field']) && 
                            $change['field'] === 'leadgen' && 
                            isset($change['value']['leadgen_id'])
                        ) {
                            $leadgenId = $change['value']['leadgen_id'];
                            $formId = $change['value']['form_id'] ?? null;
                            $createdAt = $change['value']['created_time'] ?? null;
                            
                            ProcessMetaLeadJob::dispatch($pageId, $leadgenId, $formId, $createdAt)
                                ->onQueue(config('meta-lead-ingester.queue'));
                        }
                    }
                }
            }
        }

        // Return a 200 OK response to acknowledge receipt of the event
        return response()->json(['status' => 'Event received'], 200);
    }
}
