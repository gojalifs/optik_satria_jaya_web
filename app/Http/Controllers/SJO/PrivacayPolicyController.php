<?php

namespace App\Http\Controllers\SJO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrivacayPolicyController extends Controller
{
    public function index()
    {
        return view('privacy.privacy');
    }
}
