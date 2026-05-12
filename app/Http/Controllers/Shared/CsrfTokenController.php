<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CsrfTokenController extends Controller
{
    /**
     * Return latest csrf token for the current session
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return response()->json([
            'token' => csrf_token(),
        ]);
    }
}
