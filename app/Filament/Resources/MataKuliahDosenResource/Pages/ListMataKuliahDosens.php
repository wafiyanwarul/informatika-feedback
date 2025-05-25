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
            Actions\CreateAction::make()
                ->label('Tambah MK Dosen Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'MK Dosen Management';
    }
}
