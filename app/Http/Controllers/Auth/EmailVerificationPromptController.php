<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Show the email verification prompt page.
     */
    public function __invoke(Request $request): Response|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            Log::info('EmailVerificationPromptController: email already verified, redirecting', [
                'user_id' => $request->user()->id,
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('dashboard', absolute: false));
        }

        Log::info('EmailVerificationPromptController: email verification prompt shown', [
            'user_id' => $request->user()->id,
            'ip' => $request->ip(),
        ]);

        return Inertia::render('auth/verify-email', ['status' => $request->session()->get('status')]);
    }
}
