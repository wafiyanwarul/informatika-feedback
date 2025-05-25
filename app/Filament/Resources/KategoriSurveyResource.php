<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriSurveyResource\Pages;
use App\Filament\Resources\KategoriSurveyResource\RelationManagers;
use App\Models\KategoriSurvey;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriSurveyResource extends Resource
{
    protected static ?string $model = KategoriSurvey::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Manage Surveys';
    protected static ?string $navigationLabel = 'Survey Categories';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_kategori')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex(),
                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->getStateUsing(fn($record) => $record->avatar ?: 'https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($record->nama_kategori)),
                BadgeColumn::make('nama_kategori')->label('Nama Kategori')->searchable()->sortable()
                    ->colors([
                        'success' => 'mahasiswa',
                        'info' => 'dosen',
                        'warning' => 'eksternal',
                    ]),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListKategoriSurveys::route('/'),
            'create' => Pages\CreateKategoriSurvey::route('/create'),
            'edit' => Pages\EditKategoriSurvey::route('/{record}/edit'),
        ];
    }
}
