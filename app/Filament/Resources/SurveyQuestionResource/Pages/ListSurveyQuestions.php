<?php

namespace App\Filament\Resources\SurveyQuestionResource\Pages;

use App\Models\Survey;
use App\Filament\Resources\SurveyQuestionResource;
use App\Models\SurveyQuestion;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
                ->icon('heroicon-o-eye'),
            Action::make('add_question')
                ->label('Tambah Pertanyaan')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->form([
                    TextInput::make('pertanyaan')
                        ->label('Pertanyaan')
                        ->required()
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Select::make('tipe')
                        ->label('Tipe Pertanyaan')
                        ->required()
                        ->options([
                            'rating' => 'Rating',
                            'kritik_saran' => 'Kritik & Saran',
                        ])
                        ->default('rating'),
                ])
                ->action(function (array $data, Survey $record): void {
                    SurveyQuestion::create([
                        'survey_id' => $record->id,
                        'pertanyaan' => $data['pertanyaan'],
                        'tipe' => $data['tipe'],
                    ]);

                    Notification::make()
                        ->title('Pertanyaan berhasil ditambahkan!')
                        ->success()
                        ->send();
                })
                ->modalHeading(fn(Survey $record) => 'Tambah Pertanyaan untuk: ' . $record->judul)
                ->modalSubmitActionLabel('Tambah Pertanyaan')
                ->modalWidth('lg'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulk_add_questions')
                ->label('Tambah Pertanyaan Massal')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->form([
                    Select::make('survey_id')
                        ->label('Pilih Survey')
                        ->required()
                        ->options(Survey::pluck('judul', 'id'))
                        ->searchable(),

                    Textarea::make('pertanyaan_list')
                        ->label('Daftar Pertanyaan')
                        ->required()
                        ->rows(6)
                        ->placeholder('Masukkan pertanyaan, satu per baris:' . "\n" . 'Bagaimana penilaian Anda terhadap...?' . "\n" . 'Apa saran Anda untuk...?')
                        ->helperText('Masukkan satu pertanyaan per baris. Semua pertanyaan akan ditambahkan dengan tipe yang sama.'),

                    Select::make('tipe')
                        ->label('Tipe Pertanyaan')
                        ->required()
                        ->options([
                            'rating' => 'Rating',
                            'kritik_saran' => 'Kritik & Saran',
                        ])
                        ->default('rating'),
                ])
                ->action(function (array $data): void {
                    $questions = array_filter(explode("\n", $data['pertanyaan_list']));
                    $added = 0;

                    foreach ($questions as $question) {
                        $question = trim($question);
                        if (!empty($question)) {
                            SurveyQuestion::create([
                                'survey_id' => $data['survey_id'],
                                'pertanyaan' => $question,
                                'tipe' => $data['tipe'],
                            ]);
                            $added++;
                        }
                    }

                    Notification::make()
                        ->title("Berhasil menambahkan {$added} pertanyaan!")
                        ->success()
                        ->send();
                })
                ->modalHeading('Tambah Pertanyaan Massal')
                ->modalSubmitActionLabel('Tambah Semua Pertanyaan')
                ->modalWidth('xl'),
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
