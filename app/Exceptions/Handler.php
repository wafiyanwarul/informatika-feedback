<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Handle authentication errors gracefully
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                Log::warning('Authentication failed', [
                    'message' => $e->getMessage(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
                return false; // Don't report to crash services
            }

            // Handle database connection errors
            if ($e instanceof \PDOException || $e instanceof \Illuminate\Database\QueryException) {
                Log::critical('Database error', [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'ip' => request()->ip()
                ]);
                return false; // Don't crash the app
            }

            // Handle HTTP client errors (like Turnstile API)
            if ($e instanceof \Illuminate\Http\Client\RequestException) {
                Log::error('HTTP Client error', [
                    'message' => $e->getMessage(),
                    'url' => request()->url()
                ]);
                return false;
            }

            // Handle memory exhaustion
            if ($e instanceof \ErrorException && str_contains($e->getMessage(), 'memory')) {
                Log::critical('Memory exhaustion detected', [
                    'memory_usage' => memory_get_usage(true),
                    'memory_limit' => ini_get('memory_limit'),
                    'ip' => request()->ip()
                ]);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle database connection errors gracefully
        if ($e instanceof \PDOException || $e instanceof \Illuminate\Database\QueryException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Service temporarily unavailable. Please try again later.',
                    'status' => 503
                ], 503);
            }

            return response()->view('errors.503', [], 503);
        }

        // Handle rate limiting errors for better UX
        if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Too many requests. Please slow down.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60
                ], 429);
            }
        }

        return parent::render($request, $e);
    }
}
