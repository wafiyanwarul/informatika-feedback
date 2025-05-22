<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MataKuliahDosenResource\Pages;
use App\Filament\Resources\MataKuliahDosenResource\RelationManagers;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\MataKuliahDosen;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MataKuliahDosenResource extends Resource
{
    protected static ?string $model = MataKuliahDosen::class;

    protected static ?string $navigationGroup = 'Manage Mata Kuliah and Dosen';

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'Dosen Pengampu Mata Kuliah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('mata_kuliah_id')
                    ->label('Mata Kuliah')
                    ->options(MataKuliah::all()->pluck('nama_mk', 'id'))
                    ->required(),
                Select::make('dosen_id')
                    ->label('Dosen')
                    ->options(Dosen::all()->pluck('nama_dosen', 'id'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('mataKuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('dosen.nama_dosen')
                    ->label('Dosen')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListMataKuliahDosens::route('/'),
            'create' => Pages\CreateMataKuliahDosen::route('/create'),
            'edit' => Pages\EditMataKuliahDosen::route('/{record}/edit'),
        ];
    }
}
