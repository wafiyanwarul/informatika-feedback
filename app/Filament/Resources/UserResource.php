<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\{TextInput, Select, DateTimePicker};
use Filament\Tables\Columns\{TextColumn, BadgeColumn, ImageColumn};

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manage Users and Roles';
    protected static ?string $navigationLabel = 'Users';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('foto_profil')
                ->label('Foto Profil (URL)')
                ->url()
                ->suffixIcon('heroicon-m-globe-alt')
                ->placeholder('https://...')
                ->maxLength(255)
                ->nullable(),
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
            TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn($state) => \Hash::make($state))
                ->required(fn(string $context): bool => $context === 'create')
                ->label('Password'),
            Select::make('role_id')
                ->label('Role')
                ->relationship('role', 'nama_role')
                ->required(),
            TextInput::make('foto_profil')->label('Foto Profil URL')->maxLength(255)->nullable(),
            TextInput::make('google_id')->label('Google ID')->nullable(),
            DateTimePicker::make('email_verified_at')->nullable(),
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
                ->getStateUsing(fn($record) => $record->foto_profil ?: 'https://api.dicebear.com/7.x/shapes/svg?seed=' . urlencode($record->name)),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('email')->searchable(),
            BadgeColumn::make('role.nama_role')->label('Role')->colors([
                'primary' => 'admin',
                'success' => 'mahasiswa',
                'info' => 'dosen',
                'danger' => 'eksternal',
            ]),
            TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
