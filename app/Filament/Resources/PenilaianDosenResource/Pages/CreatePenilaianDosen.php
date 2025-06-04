<?php

namespace App\Filament\Resources\PenilaianDosenResource\Pages;

use App\Filament\Resources\PenilaianDosenResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePenilaianDosen extends CreateRecord
{
    protected static string $resource = PenilaianDosenResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
