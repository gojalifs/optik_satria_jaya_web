<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthMiddleware
{
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

        return $next($request);
    }
}
