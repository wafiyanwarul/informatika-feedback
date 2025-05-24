<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResource\Pages;
use App\Filament\Resources\SurveyResource\RelationManagers;
use App\Models\Survey;
use Filament\Forms;
use Filament\Forms\Components\Select;
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

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Manage Surveys';
    protected static ?string $navigationLabel = 'Surveys';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('judul')->required(),
                Select::make('kategori_survey_id')
                    ->relationship('KategoriSurvey', 'nama_kategori')
                    ->required()
                    ->label('Kategori Survey'),
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
                    ->getStateUsing(fn($record) => $record->avatar ?: 'https://api.dicebear.com/7.x/shapes/svg?seed=' . urlencode($record->judul)),
                TextColumn::make('judul')->searchable(),
                TextColumn::make('deskripsi')->label('Deskripsi')->limit(25),
                BadgeColumn::make('kategoriSurvey.nama_kategori')->label('Kategori')->colors([
                    'success' => 'mahasiswa',
                    'info' => 'dosen',
                    'warning' => 'eksternal',
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Danger Area to be used for delete action
                // Tables\Actions\DeleteAction::make(),
            ])
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
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }
}
