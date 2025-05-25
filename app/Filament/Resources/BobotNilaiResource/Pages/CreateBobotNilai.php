<?php

namespace App\Filament\Resources\BobotNilaiResource\Pages;

use App\Filament\Resources\BobotNilaiResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBobotNilai extends CreateRecord
{
    protected static string $resource = BobotNilaiResource::class;

    public function getTitle(): string
    {
        return 'Tambah Bobot Nilai';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Bobot nilai berhasil ditambahkan!')
            ->body('Data bobot nilai telah disimpan ke sistem.');
    }
}
