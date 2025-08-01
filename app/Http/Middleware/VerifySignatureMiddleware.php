<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifySignatureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedSignature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');
        $secret = env('API_SECRET_KEY');

        if (!$providedSignature || !$timestamp) {
            throw new HttpException(400, 'Missing signature or timestamp');
        }

        // 1. Cek apakah timestamp valid (misal hanya 5 menit toleransi)
        $now = now()->timestamp;
        if (abs($now - (int)$timestamp) > 300) {
            throw new HttpException(400, 'Timestamp expired or invalid');
        }

        // 2. Ambil body dan hitung ulang signature
        $body = $request->getContent();
        $payload = $timestamp . '.' . $body;
        $computedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        // 3. Bandingkan signature aman
        if (!hash_equals($providedSignature, $computedSignature)) {
            throw new HttpException(401, 'Invalid Signature');
        }

        return $next($request);
    }
}
