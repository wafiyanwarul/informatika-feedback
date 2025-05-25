<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BobotNilaiResource\Pages;
use App\Filament\Resources\BobotNilaiResource\RelationManagers;
use App\Models\BobotNilai;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BobotNilaiResource extends Resource
{
    protected static ?string $model = BobotNilai::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Manage Surveys';
    protected static ?string $navigationLabel = 'Rating Weights';
    protected static ?string $pluralModelLabel = 'Rating Weights';
    protected static ?string $modelLabel = 'Rating Weight';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('deskripsi')
                    ->label('Deskripsi')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('Contoh: Sangat Puas, Puas, Kurang Puas, dst.')
                    ->columnSpanFull(),

                Select::make('skor')
                    ->label('Skor')
                    ->required()
                    ->options([
                        1 => '1 - Tidak Puas',
                        2 => '2 - Kurang Puas',
                        3 => '3 - Puas',
                        4 => '4 - Sangat Puas',
                    ])
                    ->default(3)
                    ->helperText('Pilih skor dari 1 (terendah) sampai 4 (tertinggi)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex(),

                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('skor')
                    ->badge()
                    ->label('Skor')
                    ->sortable()
                    ->colors([
                        1 => '1 - Tidak Puas',
                        2 => '2 - Kurang Puas',
                        3 => '3 - Puas',
                        4 => '4 - Sangat Puas',
                    ])
                    ->icons([
                        'heroicon-o-minus' => 1,
                        'heroicon-o-hand-thumb-up' => 2,
                        'heroicon-o-hand-thumb-up' => 3,
                        'heroicon-o-star' => 4,
                    ]),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('skor', 'asc')
            ->filters([
                SelectFilter::make('skor')
                    ->label('Filter Skor')
                    ->options([
                        1 => '1 - Tidak Puas',
                        2 => '2 - Kurang Puas',
                        3 => '3 - Puas',
                        4 => '4 - Sangat Puas',
                    ])
                    ->multiple(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    // ->emptyStateHeading('Belum ada bobot nilai')
                    // ->emptyStateDescription('Mulai dengan membuat bobot nilai untuk sistem rating.')
                    // ->emptyStateIcon('heroicon-o-star')
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
            'index' => Pages\ListBobotNilais::route('/'),
            'create' => Pages\CreateBobotNilai::route('/create'),
            'edit' => Pages\EditBobotNilai::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 4 ? 'success' : 'warning';
    }
}
