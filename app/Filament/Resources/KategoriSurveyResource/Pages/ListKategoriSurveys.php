<?php

namespace App\Filament\Resources\KategoriSurveyResource\Pages;

use App\Filament\Resources\KategoriSurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriSurveys extends ListRecords
{
    protected static string $resource = KategoriSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
