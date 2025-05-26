<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MaintenanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.maintenance-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 999;

    // Only show to admin users (customize based on your auth logic)
    public static function canView(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        // return $user && isset($user->is_admin) ? $user->is_admin : false; // Adjust this condition
        return true;
    }

    public function getViewData(): array
    {
        return [
            'isInMaintenance' => app()->isDownForMaintenance(),
            'koyebStatus' => $this->getKoyebServiceStatus(),
        ];
    }

    private function getKoyebServiceStatus(): string
    {
        $token = config('services.koyeb.token');
        $serviceId = config('services.koyeb.service_id');

        if (!$token || !$serviceId) {
            return 'not_configured';
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get("https://app.koyeb.com/v1/services/{$serviceId}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['service']['status'] ?? 'unknown';
            }

            return 'api_error';

        } catch (\Exception $e) {
            Log::error('Koyeb status API error: ' . $e->getMessage());
            return 'connection_error';
        }
    }
}
