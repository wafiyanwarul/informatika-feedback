<?php

namespace App\Filament\Resources\PenilaianDosenResource\Pages;

use App\Filament\Resources\PenilaianDosenResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPenilaianDosen extends EditRecord
{
    protected static string $resource = PenilaianDosenResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Nilai Dosen per mahasiswa berhasil diperbarui!')
            ->body('Perubahan data telah disimpan.');
    }

}
