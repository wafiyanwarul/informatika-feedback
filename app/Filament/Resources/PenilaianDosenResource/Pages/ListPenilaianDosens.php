<?php

namespace App\Filament\Resources\PenilaianDosenResource\Pages;

use App\Filament\Resources\PenilaianDosenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenilaianDosens extends ListRecords
{
    protected static string $resource = PenilaianDosenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
