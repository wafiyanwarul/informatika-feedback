<?php

namespace App\Filament\Resources\BobotNilaiResource\Pages;

use App\Filament\Resources\BobotNilaiResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBobotNilai extends EditRecord
{
    protected static string $resource = BobotNilaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Bobot Nilai';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Bobot nilai berhasil diperbarui!')
            ->body('Perubahan data telah disimpan.');
    }
}
