<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use App\Models\Survey;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class ListRespondents extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ResponseResource::class;
    protected static string $view = 'filament.resources.response-resource.pages.list-respondents';

    public Survey $survey;

    public function mount(int|string $survey): void
    {
        $this->survey = Survey::findOrFail($survey);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\User::query()
                    ->whereHas('responses', function (Builder $query) {
                        $query->where('survey_id', $this->survey->id);
                    })
                    ->withCount(['responses' => function (Builder $query) {
                        $query->where('survey_id', $this->survey->id);
                    }])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('responses_count')
                    ->label('Total Jawaban')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('latest_response_date')
                    ->label('Terakhir Menjawab')
                    ->getStateUsing(function ($record) {
                        return $record->responses()
                            ->where('survey_id', $this->survey->id)
                            ->latest()
                            ->first()
                            ?->created_at
                            ?->format('d M Y H:i');
                    })
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_responses')
                    ->label('Lihat Jawaban')
                    ->icon('heroicon-s-eye')
                    ->color('primary')
                    ->url(fn($record) => ResponseResource::getUrl('responses', [
                        'survey' => $this->survey->id,
                        'user' => $record->id
                    ])),
            ])
            ->defaultSort('name');
    }

    public function getTitle(): string
    {
        return 'Responden - ' . $this->survey->judul;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getBreadcrumbs(): array
    {
        return [
            ResponseResource::getUrl('index') => 'Survey Responses',
            '' => $this->survey->judul,
        ];
    }
}
