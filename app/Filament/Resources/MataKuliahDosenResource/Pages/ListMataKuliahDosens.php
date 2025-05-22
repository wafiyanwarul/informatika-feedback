<?php

namespace App\Filament\Resources\MataKuliahDosenResource\Pages;

use App\Filament\Resources\MataKuliahDosenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMataKuliahDosens extends ListRecords
{
    protected static string $resource = MataKuliahDosenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
