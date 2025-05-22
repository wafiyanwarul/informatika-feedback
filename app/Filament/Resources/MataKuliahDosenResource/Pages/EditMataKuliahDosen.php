<?php

namespace App\Filament\Resources\MataKuliahDosenResource\Pages;

use App\Filament\Resources\MataKuliahDosenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMataKuliahDosen extends EditRecord
{
    protected static string $resource = MataKuliahDosenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
