<?php

namespace App\Filament\Resources\MataKuliahDosenResource\Pages;

use App\Filament\Resources\MataKuliahDosenResource;
use Filament\Actions;
use Filament\Notifications\Notification;
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('MK Dosen berhasil diperbarui!')
            ->body('Perubahan data telah disimpan.');
    }
}
