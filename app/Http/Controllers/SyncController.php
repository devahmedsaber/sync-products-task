<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\JsonResponse;

class SyncController extends Controller
{
    public function syncProducts(): JsonResponse
    {
        Artisan::call('sync:products');

        return response()->json([
            'message' => 'Product sync has been triggered successfully',
        ]);
    }
}
