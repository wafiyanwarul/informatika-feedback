<?php

namespace App\Filament\Resources\FinalScoreResource\Pages;

use App\Filament\Resources\FinalScoreResource;
use App\Models\FinalScore;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateFinalScore extends CreateRecord
{
    protected static string $resource = FinalScoreResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate final score before creating
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

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Final Score Created')
            ->body('The final score has been calculated and saved successfully.');
    }
}
