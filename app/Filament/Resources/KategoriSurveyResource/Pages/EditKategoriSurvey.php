<?php

namespace App\Filament\Resources\KategoriSurveyResource\Pages;

use App\Filament\Resources\KategoriSurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriSurvey extends EditRecord
{
    protected static string $resource = KategoriSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
