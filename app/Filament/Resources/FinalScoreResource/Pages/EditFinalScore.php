<?php

namespace App\Filament\Resources\FinalScoreResource\Pages;

use App\Filament\Resources\FinalScoreResource;
use App\Models\FinalScore;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFinalScore extends EditRecord
{
    protected static string $resource = FinalScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate final score when editing
        $finalScore = FinalScore::calculateFinalScore($data['dosen_id'], $data['mata_kuliah_id']);

        if ($finalScore === null) {
            Notification::make()
                ->title('No Evaluation Data')
                ->body('No student evaluation data found for this combination')
                ->warning()
                ->send();

            $data['final_score'] = 0;
        } else {
            $data['final_score'] = $finalScore;
        }

        return $data;
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Final Score Updated')
            ->body('The final score has been recalculated and updated successfully.');
    }
}
