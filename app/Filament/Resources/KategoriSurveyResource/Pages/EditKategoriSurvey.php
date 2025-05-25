<?php

namespace App\Filament\Resources\KategoriSurveyResource\Pages;

use App\Filament\Resources\KategoriSurveyResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditKategoriSurvey extends EditRecord
{
    protected static string $resource = KategoriSurveyResource::class;

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
            ->title('Kategori Survey berhasil diperbarui!')
            ->body('Perubahan data telah disimpan.');
    }
}
