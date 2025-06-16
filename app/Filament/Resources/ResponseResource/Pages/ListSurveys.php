<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use Filament\Resources\Pages\ListRecords;

class ListSurveys extends ListRecords
{
    protected static string $resource = ResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions needed for this page
        ];
    }

    public function getTitle(): string
    {
        return 'Survey Responses Management';
    }

    public function getHeading(): string
    {
        return 'Survey Responses Management';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add widgets here if needed for statistics
        ];
    }
}
