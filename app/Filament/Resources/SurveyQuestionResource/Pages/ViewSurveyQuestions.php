<?php

namespace App\Filament\Resources\SurveyQuestionResource\Pages;

use App\Filament\Resources\SurveyQuestionResource;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;

class ViewSurveyQuestions extends Page implements Tables\Contracts\HasTable
{
    use InteractsWithTable;

    public Survey $record;

    protected static string $resource = SurveyQuestionResource::class;

    protected static string $view = 'filament.resources.survey-question-resource.pages.view-survey-questions';

    protected function getTableQuery(): Builder
    {
        return SurveyQuestion::query()->where('survey_id', $this->record->id);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('no')
                ->label('No.')
                ->rowIndex(),
            TextColumn::make('pertanyaan')->label('Pertanyaan')->limit(100),
            TextColumn::make('tipe')->label('Tipe')->badge()
                ->colors([
                    'success' => 'rating',
                    'info' => 'kritik_saran',
                ]),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('tipe')
                ->label('Tipe')
                ->options([
                    'rating' => 'Rating',
                    'kritik_saran' => 'Kritik & Saran',
                ])
                ->attribute('tipe')
        ];
    }

    public function getTitle(): string
    {
        return $this->record->judul;
    }

    public function getBreadcrumbs(): array
    {
        return [
            SurveyQuestionResource::getUrl('index') => 'Survey Questions',
            url()->current() => $this->record->judul . ' > List',
        ];
    }

    protected function getDefaultTableRecordsPerPage(): int
    {
        return 25;
    }
}
