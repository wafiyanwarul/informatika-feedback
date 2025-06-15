<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinalScoreResource\Pages;
use App\Filament\Resources\FinalScoreResource\RelationManagers;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\FinalScore;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinalScoreResource extends Resource
{
    protected static ?string $model = FinalScore::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Manage Responses & Final Scores';
    protected static ?string $navigationLabel = 'Final Scores';
    protected static ?string $modelLabel = 'Final Score';
    protected static ?string $pluralModelLabel = 'Final Scores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('dosen_id')
                            ->label('Dosen')
                            ->options(Dosen::all()->pluck('nama_dosen', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $mataKuliahId = $get('mata_kuliah_id');
                                if ($state && $mataKuliahId) {
                                    $finalScore = FinalScore::calculateFinalScore($state, $mataKuliahId);
                                    $set('final_score', $finalScore);
                                }
                            }),

                        Forms\Components\Select::make('mata_kuliah_id')
                            ->label('Mata Kuliah')
                            ->options(MataKuliah::all()->pluck('nama_mk', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $dosenId = $get('dosen_id');
                                if ($state && $dosenId) {
                                    $finalScore = FinalScore::calculateFinalScore($dosenId, $state);
                                    $set('final_score', $finalScore);
                                }
                            }),

                        Forms\Components\TextInput::make('final_score')
                            ->label('Final Score')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(5)
                            ->disabled()
                            ->dehydrated(true)
                            ->helperText('Score will be calculated automatically based on student evaluations'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                ->label('No.')
                ->rowIndex(),
                Tables\Columns\TextColumn::make('dosen.nama_dosen')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mataKuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mataKuliah.sks')
                    ->label('SKS')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_score')
                    ->label('Final Score')
                    ->numeric(2)
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 3.5 => 'info',
                        $state >= 2.5 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn($state) => number_format($state, 2)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('dosen_id')
                    ->label('Dosen')
                    ->options(Dosen::all()->pluck('nama_dosen', 'id')),
                SelectFilter::make('mata_kuliah_id')
                    ->label('Mata Kuliah')
                    ->options(MataKuliah::all()->pluck('nama_mk', 'id')),
                Tables\Filters\Filter::make('score_range')
                    ->form([
                        Forms\Components\Select::make('range')
                            ->options([
                                'excellent' => 'Excellent (3.5 - 4.0)',
                                'good' => 'Good (3.0 - 3.49)',
                                'satisfactory' => 'Satisfactory (2.5 - 3.0)',
                                'needs_improvement' => 'Needs Improvement (< 2.5)',
                            ])
                            ->placeholder('Select score range'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['range'],
                            fn (Builder $query, $range): Builder => match ($range) {
                                'excellent' => $query->where('final_score',  [3.5, 4.0]),
                                'good' => $query->whereBetween('final_score', [3.0, 3.49]),
                                'satisfactory' => $query->whereBetween('final_score', [2.5, 3.0]),
                                'needs_improvement' => $query->where('final_score', '<', 2.5),
                                default => $query,
                            }
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Action::make('recalculate')
                    ->label('Recalculate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (FinalScore $record) {
                        $newScore = FinalScore::calculateFinalScore(
                            $record->dosen_id,
                            $record->mata_kuliah_id
                        );

                        if ($newScore !== null) {
                            $record->update(['final_score' => $newScore]);

                            Notification::make()
                                ->title('Score Recalculated')
                                ->body("Final score updated to {$newScore}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('No Data Found')
                                ->body('No evaluation data available for recalculation')
                                ->warning()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Recalculate Final Score')
                    ->modalDescription('Are you sure you want to recalculate the final score based on current student evaluations?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinalScores::route('/'),
            'create' => Pages\CreateFinalScore::route('/create'),
            'view' => Pages\ViewFinalScore::route('/{record}'),
            'edit' => Pages\EditFinalScore::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
