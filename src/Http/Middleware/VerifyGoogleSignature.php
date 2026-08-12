<?php

namespace Vendor\MetaLeadIngester\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Vendor\MetaLeadIngester\Models\GoogleAccount;
use Illuminate\Support\Facades\Log;

class VerifyGoogleSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $payload = $request->all();
        $googleKey = $payload['google_key'] ?? null;

        if (!$googleKey) {
            Log::warning('Google Webhook: Missing google_key in payload.');
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $account = GoogleAccount::where('google_key', $googleKey)->first();

        if (!$account) {
            Log::warning('Google Webhook: Invalid google_key received.', ['key' => $googleKey]);
            return response()->json(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        // Attach the found account to the request for the controller to use
        $request->attributes->set('google_account', $account);

        return $next($request);
    }
}
