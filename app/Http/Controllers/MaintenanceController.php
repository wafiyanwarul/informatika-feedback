<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    private $koyebApiUrl = 'https://app.koyeb.com/v1';
    private $koyebToken;
    private $serviceId;

    public function __construct()
    {
        $this->koyebToken = config('services.koyeb.token');
        $this->serviceId = config('services.koyeb.service_id');
    }

    public function toggle(Request $request)
    {
        try {
            // Add admin check
            if (!$this->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $isCurrentlyInMaintenance = app()->isDownForMaintenance();

            if ($isCurrentlyInMaintenance) {
                // Complete maintenance - Resume service
                return $this->completeMaintenance();
            } else {
                // Enter maintenance - Pause service
                return $this->enterMaintenance();
            }

        } catch (\Exception $e) {
            Log::error('Maintenance toggle error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user' => \Illuminate\Support\Facades\Auth::user()?->email
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle maintenance mode: ' . $e->getMessage()
            ], 500);
        }
    }

    private function enterMaintenance()
    {
        try {
            // 1. Put Laravel in maintenance mode first
            $exitCode = Artisan::call('down', [
                '--render' => 'errors::maintenance',
                '--secret' => 'maintenance-secret-key-' . time(),
                '--with-secret' => true
            ]);

            if ($exitCode !== 0) {
                throw new \Exception('Failed to put Laravel in maintenance mode');
            }

            // 2. Pause Koyeb service
            $koyebResponse = $this->pauseKoyebService();

            Log::info('Maintenance mode activated', [
                'laravel_maintenance' => true,
                'koyeb_paused' => $koyebResponse,
                'user' => \Illuminate\Support\Facades\Auth::user()?->email,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'maintenance_mode' => true,
                'message' => 'Maintenance mode activated successfully',
                'details' => [
                    'laravel_status' => 'down',
                    'koyeb_status' => $koyebResponse ? 'paused' : 'pause_failed'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Enter maintenance error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function completeMaintenance()
    {
        try {
            // 1. Resume Koyeb service first
            $koyebResponse = $this->resumeKoyebService();

            // 2. Bring Laravel back online
            $exitCode = Artisan::call('up');

            if ($exitCode !== 0) {
                throw new \Exception('Failed to bring Laravel back online');
            }

            Log::info('Maintenance mode completed', [
                'laravel_maintenance' => false,
                'koyeb_resumed' => $koyebResponse,
                'user' => \Illuminate\Support\Facades\Auth::user()?->email,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'maintenance_mode' => false,
                'message' => 'Maintenance completed successfully',
                'details' => [
                    'laravel_status' => 'online',
                    'koyeb_status' => $koyebResponse ? 'resumed' : 'resume_failed'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Complete maintenance error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function pauseKoyebService(): bool
    {
        if (!$this->koyebToken || !$this->serviceId) {
            Log::warning('Koyeb credentials not configured', [
                'has_token' => !empty($this->koyebToken),
                'has_service_id' => !empty($this->serviceId)
            ]);
            return false;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->koyebToken,
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->koyebApiUrl}/services/{$this->serviceId}/pause");

            if ($response->successful()) {
                Log::info('Koyeb service paused successfully', [
                    'service_id' => $this->serviceId
                ]);
                return true;
            } else {
                Log::error('Koyeb pause failed', [
                    'service_id' => $this->serviceId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Koyeb pause API error: ' . $e->getMessage(), [
                'service_id' => $this->serviceId,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function resumeKoyebService(): bool
    {
        if (!$this->koyebToken || !$this->serviceId) {
            Log::warning('Koyeb credentials not configured');
            return false;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->koyebToken,
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->koyebApiUrl}/services/{$this->serviceId}/resume");

            if ($response->successful()) {
                Log::info('Koyeb service resumed successfully', [
                    'service_id' => $this->serviceId
                ]);
                return true;
            } else {
                Log::error('Koyeb resume failed', [
                    'service_id' => $this->serviceId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Koyeb resume API error: ' . $e->getMessage(), [
                'service_id' => $this->serviceId,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function status()
    {
        if (!$this->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'laravel_maintenance' => app()->isDownForMaintenance(),
                'koyeb_status' => $this->getKoyebServiceStatus(),
                'timestamp' => now()
            ]
        ]);
    }

    private function getKoyebServiceStatus(): string
    {
        if (!$this->koyebToken || !$this->serviceId) {
            return 'not_configured';
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->koyebToken
                ])
                ->get("{$this->koyebApiUrl}/services/{$this->serviceId}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['service']['status'] ?? 'unknown';
            }

            Log::error('Koyeb status API failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return 'api_error';

        } catch (\Exception $e) {
            Log::error('Koyeb status API error: ' . $e->getMessage());
            return 'connection_error';
        }
    }

    private function isAdmin(): bool
    {
        // Customize this based on your admin logic
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return false;
        }

        // Example checks - adjust based on your user model:
        return $user->is_admin ?? false; // or $user->role === 'admin' or $user->hasRole('admin')
    }
}
