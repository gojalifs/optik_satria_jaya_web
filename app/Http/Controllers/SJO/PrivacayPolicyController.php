<?php

namespace App\Http\Controllers\SJO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrivacayPolicyController extends Controller
{
    public function index(Request $request)
    {
        Log::info('PrivacayPolicyController@index: privacy policy page accessed', [
            'ip' => $request->ip(),
            'user_id' => $request->user()?->id,
        ]);

        return view('privacy.privacy');
    }
}
