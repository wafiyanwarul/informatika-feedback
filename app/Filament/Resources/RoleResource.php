<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Manage Users and Roles';
    protected static ?string $navigationLabel = 'Roles';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_role')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->label('Nama Role'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('no')
                ->label('No.')
                ->rowIndex(),
            ImageColumn::make('foto_profil')
                ->label('Avatar')
                ->circular()
                ->getStateUsing(fn($record) => $record->foto_profil ?: 'https://api.dicebear.com/7.x/pixel-art/svg?seed=' . urlencode($record->nama_role)),
            BadgeColumn::make('nama_role')->label('Nama Role')->searchable()->colors([
                'primary' => 'admin',
                'success' => 'mahasiswa',
                'info' => 'dosen',
                'danger' => 'eksternal',
            ]),
            TextColumn::make('created_at')->label('Created')->dateTime('d M Y'),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
