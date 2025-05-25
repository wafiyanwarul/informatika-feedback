<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'memory' => memory_get_usage(true),
        'timestamp' => now(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'failed'
    ]);
});
