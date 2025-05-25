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
            Actions\CreateAction::make()
                ->label('Tambah Kategori Survey')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'Survey Categories Management';
    }
}
