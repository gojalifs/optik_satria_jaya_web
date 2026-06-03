<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthMiddleware
{
    private const COOKIE_NAME = 'invoice_auth';
    private const COOKIE_LIFETIME = 60 * 24 * 365; // 1 year in minutes

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validUsername = env('USERNAME');
        $validPassword = env('PASSWORD');

        if (!$validUsername || !$validPassword) {
            abort(500, 'Invoice auth credentials are not configured.');
        }

        $expectedToken = $this->generateToken($validUsername, $validPassword);

        // Allow through if persistent auth cookie is present and valid
        if ($request->cookie(self::COOKIE_NAME) === $expectedToken) {
            return $next($request);
        }

        $username = $request->getUser();
        $password = $request->getPassword();

        if (
            !$username ||
            !$password ||
            !hash_equals($validUsername, $username) ||
            !hash_equals($validPassword, $password)
        ) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="Invoice"',
            ]);
        }

        // Credentials valid — attach a persistent auth cookie to the response
        $response = $next($request);

        return $response->withCookie(
            cookie(self::COOKIE_NAME, $expectedToken, self::COOKIE_LIFETIME, '/', null, $request->secure(), true)
        );
    }

    /**
     * Generate a stable HMAC token derived from the credentials and APP_KEY.
     * The token is invalidated automatically when credentials or APP_KEY change.
     * The password is pre-hashed so it never appears in plain text inside the HMAC input.
     */
    private function generateToken(string $username, string $password): string
    {
        $passwordHash = hash('sha256', $password);

        return hash_hmac('sha256', $username . ':' . $passwordHash, config('app.key'));
    }
}
