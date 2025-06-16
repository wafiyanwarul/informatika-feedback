<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use App\Models\Survey;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ListRespondents extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ResponseResource::class;

    protected static string $view = 'filament.resources.response-resource.pages.list-respondents';

    public Survey $survey;

    public function mount(Survey $survey): void
    {
        $this->survey = $survey;
    }

    public function getTitle(): string
    {
        return "Respondents for: {$this->survey->judul}";
    }

    public function getHeading(): string
    {
        return "Respondents for: {$this->survey->judul}";
    }

    public function getBreadcrumbs(): array
    {
        return [
            ResponseResource::getUrl() => 'Survey Responses',
            '' => $this->survey->judul,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereHas('responses', function (Builder $query) {
                        $query->where('survey_id', $this->survey->id);
                    })
                    ->withCount([
                        'responses as responses_count' => function (Builder $query) {
                            $query->where('survey_id', $this->survey->id);
                        }
                    ])
            )
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role.nama_role')
                    ->label('Role')
                    ->badge(),

                Tables\Columns\TextColumn::make('responses_count')
                    ->label('Total Responses')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('latest_response')
                    ->label('Latest Response')
                    ->getStateUsing(function ($record) {
                        $latestResponse = $record->responses()
                            ->where('survey_id', $this->survey->id)
                            ->latest()
                            ->first();
                        return $latestResponse ? $latestResponse->created_at->format('d M Y H:i') : '-';
                    })
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_responses')
                    ->label('View Responses')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => ResponseResource::getUrl('responses', [
                        'survey' => $this->survey->id,
                        'user' => $record->id
                    ])),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_id')
                    ->label('Role')
                    ->relationship('role', 'nama_role'),
            ])
            ->defaultSort('name', 'asc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_surveys')
                ->label('Back to Surveys')
                ->icon('heroicon-o-arrow-left')
                ->url(ResponseResource::getUrl()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add survey statistics widget here if needed
        ];
    }
}
