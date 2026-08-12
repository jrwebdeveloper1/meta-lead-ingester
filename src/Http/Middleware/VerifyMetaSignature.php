<?php

namespace Vendor\MetaLeadIngester\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMetaSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $appSecret = config('meta-lead-ingester.app_secret');

        if (empty($appSecret)) {
            abort(500, 'Meta app secret is not configured.');
        }

        $signatureHeader = $request->header('X-Hub-Signature-256');
        
        if (!$signatureHeader) {
            abort(403, 'Missing signature header.');
        }

        $signatureParts = explode('=', $signatureHeader);

        if (count($signatureParts) !== 2 || $signatureParts[0] !== 'sha256') {
            abort(403, 'Invalid signature format.');
        }

        $expectedSignature = hash_hmac('sha256', $request->getContent(), $appSecret);

        if (!hash_equals($expectedSignature, $signatureParts[1])) {
            abort(403, 'Invalid signature.');
        }

        return $next($request);
    }
}
