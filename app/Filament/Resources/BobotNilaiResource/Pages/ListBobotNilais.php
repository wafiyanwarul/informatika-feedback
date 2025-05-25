<?php

namespace App\Filament\Resources\BobotNilaiResource\Pages;

use App\Filament\Resources\BobotNilaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBobotNilais extends ListRecords
{
    protected static string $resource = BobotNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Bobot Nilai')
                ->icon('heroicon-o-plus'),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Rating Weights Management';
    }
}
