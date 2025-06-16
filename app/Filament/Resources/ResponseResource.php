<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResponseResource\Pages;
use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResponseResource extends Resource
{
    protected static ?string $model = Survey::class; // Menggunakan Survey sebagai model utama

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Survey Responses';

    protected static ?string $modelLabel = 'Survey Response';

    protected static ?string $pluralModelLabel = 'Survey Responses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Survey Title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kategoriSurvey.nama_kategori')
                    ->label('Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('responses_count')
                    ->label('Total Responses')
                    ->counts('responses')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('unique_respondents_count')
                    ->label('Unique Respondents')
                    ->getStateUsing(function ($record) {
                        return $record->responses()->distinct('user_id')->count('user_id');
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Category')
                    ->relationship('kategoriSurvey', 'nama_kategori'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_respondents')
                    ->label('View Respondents')
                    ->icon('heroicon-o-users')
                    ->url(fn ($record) => static::getUrl('respondents', ['survey' => $record->id]))
                    ->visible(fn ($record) => $record->responses_count > 0),
            ])
            ->bulkActions([
                // No bulk actions needed for this view
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->withCount('responses')->has('responses');
            })
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSurveys::route('/'),
            'respondents' => Pages\ListRespondents::route('/survey/{survey}/respondents'),
            'responses' => Pages\ListUserResponses::route('/survey/{survey}/respondent/{user}/responses'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Disable create since we're managing responses, not creating surveys
    }
}
