<?php

namespace App\Http\Middleware;

use App\Services\LicenseValidator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VerifyLicense
{
    public function handle(Request $request, Closure $next)
    {
        // Skip installer and license routes
        if (
            $request->is('install*') ||
            $request->is('installer*') ||
            $request->is('license/revalidate*')
        ) {
            return $next($request);
        }

        $purchase = config('app.license_purchase');
        $token = config('app.license_token');
        $signature = config('app.license_signature');

        // Use the same domain source that is used when generating the signature
        $domain = $request->getSchemeAndHttpHost();

        if (!$purchase || !$domain || !$token || !$signature) {
            return redirect()->route('license.revalidate', [
                'intended' => $request->fullUrl(),
                'message' => 'Application is not licensed.',
            ]);
        }

        // Verify signature integrity
        $expected = LicenseValidator::signature(
            $purchase,
            $domain,
            $token
        );

        if (!hash_equals($expected, (string) $signature)) {
            return redirect()->route('license.revalidate', [
                'intended' => $request->fullUrl(),
                'message' => 'License signature mismatch.',
            ]);
        }

        // Periodic remote revalidation
        $cacheKey = 'license_recheck_ts';
        $last = Cache::get($cacheKey);
        $interval = (int) config('license.recheck_minutes', 720);
        $now = now()->timestamp;

        if (!$last || ($now - (int) $last) > ($interval * 60)) {
            $client = new LicenseValidator();
            $res = $client->validate($purchase, $domain);

            if (($res['success'] ?? false) === false) {
                return redirect()->route('license.revalidate', [
                    'intended' => $request->fullUrl(),
                    'message' => 'License validation failed: ' . ($res['message'] ?? 'Unknown'),
                ]);
            }

            // Update runtime token if validator returns a new one
            $newToken = $res['data']['token'] ?? null;

            if ($newToken && $newToken !== $token) {
                config(['app.license_token' => $newToken]);
            }

            Cache::put($cacheKey, $now, now()->addMinutes($interval));
        }

        return $next($request);
    }
}