<?php

namespace App\Filament\Resources\SurveyQuestionResource\Pages;

use App\Models\Survey;
use App\Filament\Resources\SurveyQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\ImageColumn;

class ListSurveyQuestions extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = SurveyQuestionResource::class;

    protected static string $view = 'filament.resources.survey-question-resource.pages.list-survey-questions';

    protected function getTableQuery()
    {
        return Survey::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('no')
                ->label('No.')
                ->rowIndex(),
            ImageColumn::make('avatar')
                ->label('Avatar')
                ->circular()
                ->getStateUsing(fn($record) => $record->avatar ?: 'https://api.dicebear.com/7.x/shapes/svg?seed=' . urlencode($record->judul)),
            TextColumn::make('judul')->label('Survey Name'),
            TextColumn::make('questions_count')
                ->label('Jumlah Pertanyaan')
                ->counts('questions'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Lihat Pertanyaan')
                ->url(fn(Survey $record): string => SurveyQuestionResource::getUrl('view-survey', ['record' => $record->id]))
                ->icon('heroicon-o-eye')
        ];
    }

    public function getTitle(): string
    {
        return 'Survey Questions Management';
    }

    public function getBreadcrumbs(): array
    {
        return [
            url()->current() => 'Survey Questions',
        ];
    }
}
