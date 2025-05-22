<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MataKuliahResource\Pages;
use App\Filament\Resources\MataKuliahResource\RelationManagers;
use App\Models\MataKuliah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MataKuliahResource extends Resource
{
    protected static ?string $model = MataKuliah::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Manage Mata Kuliah and Dosen';
    protected static ?string $navigationLabel = 'Mata Kuliah';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_mk')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->label('Nama Mata Kuliah'),

            TextInput::make('sks')
                ->numeric()
                ->required()
                ->minValue(1)
                ->maxValue(8),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex(),
                ImageColumn::make('foto_profil')
                    ->label('Avatar')
                    ->circular()
                    ->getStateUsing(fn($record) => $record->foto_profil ?: 'https://api.dicebear.com/7.x/rings/svg?seed=' . urlencode($record->nama_mk)),
                TextColumn::make('nama_mk')
                    ->label('Nama Mata Kuliah')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('sks')
                    ->label('SKS')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListMataKuliahs::route('/'),
            'create' => Pages\CreateMataKuliah::route('/create'),
            'edit' => Pages\EditMataKuliah::route('/{record}/edit'),
        ];
    }
}
