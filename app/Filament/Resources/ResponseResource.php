<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResponseResource\Pages;
use App\Models\Survey;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ResponseResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Manage Responses & Final Scores';
    protected static ?string $navigationLabel = 'Survey Responses';
    protected static ?string $modelLabel = 'Survey Response';
    protected static ?string $pluralModelLabel = 'Survey Responses';
    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Survey')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('kategoriSurvey.nama')
                    ->label('Kategori')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('unique_respondents_count')
                    ->label('Total Responden')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_responses_count')
                    ->label('Total Jawaban')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_responses')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('has_responses')
                    ->label('Status Response')
                    ->options([
                        '1' => 'Sudah ada responden',
                        '0' => 'Belum ada responden',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === '1') {
                            return $query->whereHas('responses');
                        } elseif ($data['value'] === '0') {
                            return $query->whereDoesntHave('responses');
                        }
                        return $query;
                    }),

                SelectFilter::make('kategori_id')
                    ->label('Kategori Survey')
                    ->relationship('kategoriSurvey', 'nama_kategori')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_responses')
                    ->label('View Responses')
                    ->icon('heroicon-s-eye')
                    ->color('primary')
                    ->url(fn($record) => static::getUrl('respondents', ['survey' => $record->id]))
                    ->action(function ($record) {
                        $hasResponses = $record->responses()->exists();

                        if (!$hasResponses) {
                            \Filament\Notifications\Notification::make()
                                ->title('Tidak ada responden')
                                ->body('Belum ada responden yang menjawab survey ini.')
                                ->warning()
                                ->send();
                            return;
                        }

                        return redirect()->to(static::getUrl('respondents', ['survey' => $record->id]));
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResponses::route('/'),
            'respondents' => Pages\ListRespondents::route('/{survey}/respondents'),
            'responses' => Pages\ListUserResponses::route('/{survey}/respondents/{user}/responses'),
            'view' => Pages\ViewResponse::route('/{survey}/respondents/{user}/responses/{response}'),
            'edit' => Pages\EditResponse::route('/{survey}/respondents/{user}/responses/{response}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return Survey::query()
            ->withCount([
                'responses as unique_respondents_count' => function ($query) {
                    $query->distinct('user_id');
                },
                'responses as total_responses_count'
            ])
            ->withExists('responses as has_responses');
    }

    public static function getNavigationBadge(): ?string
    {
        return Survey::whereHas('responses')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}
