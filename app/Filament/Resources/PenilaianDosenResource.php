<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenilaianDosenResource\Pages;
use App\Filament\Resources\PenilaianDosenResource\RelationManagers;
use App\Models\PenilaianDosen;
use App\Models\Response;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;

class PenilaianDosenResource extends Resource
{
    protected static ?string $model = PenilaianDosen::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Penilaian Dosen';

    protected static ?string $modelLabel = 'Penilaian Dosen';

    protected static ?string $pluralModelLabel = 'Penilaian Dosen';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Penilaian')
                    ->schema([
                        Forms\Components\Select::make('mahasiswa_id')
                            ->label('Mahasiswa')
                            ->relationship('mahasiswa', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('dosen_id')
                            ->label('Dosen')
                            ->relationship('dosen', 'nama_dosen')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('mk_id')
                            ->label('Mata Kuliah')
                            ->relationship('mataKuliah', 'nama_mk')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('survey_id')
                            ->label('Survey')
                            ->relationship('survey', 'judul')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Hasil Penilaian')
                    ->schema([
                        Forms\Components\TextInput::make('nilai')
                            ->label('Nilai Rata-rata')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(1)
                            ->maxValue(4)
                            ->suffix('/4')
                            ->helperText('Nilai akan dihitung otomatis berdasarkan response mahasiswa')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dosen.nama_dosen')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('mataKuliah.nama_mk')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('mataKuliah.sks')
                    ->label('SKS')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('survey.judul')
                    ->label('Survey')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('nilai')
                    ->label('Nilai')
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        $state >= 3.5 => 'success',
                        $state >= 3.0 => 'primary',
                        $state >= 2.5 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn($state) => number_format($state, 2) . '/4')
                    ->sortable()
                    ->summarize(Average::make()->label('Rata-rata')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Penilaian')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),


            ])
            ->filters([
                SelectFilter::make('dosen_id')
                    ->label('Dosen')
                    ->relationship('dosen', 'nama_dosen')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('mk_id')
                    ->label('Mata Kuliah')
                    ->relationship('mataKuliah', 'nama_mk')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('survey_id')
                    ->label('Survey')
                    ->relationship('survey', 'judul')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('nilai_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nilai_from')
                                    ->label('Nilai Minimum')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(1)
                                    ->maxValue(4),
                                Forms\Components\TextInput::make('nilai_to')
                                    ->label('Nilai Maksimum')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(1)
                                    ->maxValue(4),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['nilai_from'],
                                fn(Builder $query, $nilai): Builder => $query->where('nilai', '>=', $nilai),
                            )
                            ->when(
                                $data['nilai_to'],
                                fn(Builder $query, $nilai): Builder => $query->where('nilai', '<=', $nilai),
                            );
                    }),
            ])
            ->actions([
                Action::make('recalculate')
                    ->label('Hitung Ulang')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->action(function (PenilaianDosen $record) {
                        $responses = Response::where('user_id', $record->mahasiswa_id)
                            ->where('survey_id', $record->survey_id)
                            ->whereHas('question', function ($query) {
                                $query->where('tipe', 'rating');
                            })
                            ->pluck('nilai');

                        if ($responses->count() > 0) {
                            $averageScore = round($responses->avg(), 2);
                            $record->update(['nilai' => $averageScore]);
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hitung Ulang Nilai')
                    ->modalDescription('Apakah Anda yakin ingin menghitung ulang nilai berdasarkan response terbaru?')
                    ->modalSubmitActionLabel('Ya, Hitung Ulang'),
                Tables\Actions\DeleteAction::make(),
            ])

            ->headerActions([
                Action::make('recalculate_all')
                    ->label('Hitung Ulang Semua')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->action(function () {
                        $penilaians = PenilaianDosen::all();

                        foreach ($penilaians as $penilaian) {
                            $responses = Response::where('user_id', $penilaian->mahasiswa_id)
                                ->where('survey_id', $penilaian->survey_id)
                                ->whereHas('question', function ($query) {
                                    $query->where('tipe', 'rating');
                                })
                                ->pluck('nilai');

                            if ($responses->count() > 0) {
                                $averageScore = round($responses->avg(), 2);
                                $penilaian->update(['nilai' => $averageScore]);
                            }
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hitung Ulang Semua Nilai')
                    ->modalDescription('Apakah Anda yakin ingin menghitung ulang semua nilai berdasarkan response terbaru? Proses ini mungkin memakan waktu.')
                    ->modalSubmitActionLabel('Ya, Hitung Ulang Semua'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                // ]),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('recalculate_selected')
                    ->label('Hitung Ulang Terpilih')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $responses = Response::where('user_id', $record->mahasiswa_id)
                                ->where('survey_id', $record->survey_id)
                                ->whereHas('question', function ($query) {
                                    $query->where('tipe', 'rating');
                                })
                                ->pluck('nilai');

                            if ($responses->count() > 0) {
                                $averageScore = round($responses->avg(), 2);
                                $record->update(['nilai' => $averageScore]);
                            }
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hitung Ulang Nilai Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin menghitung ulang nilai untuk data yang dipilih?')
                    ->modalSubmitActionLabel('Ya, Hitung Ulang'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Mahasiswa & Dosen')
                    ->schema([
                        Infolists\Components\TextEntry::make('mahasiswa.name')
                            ->label('Nama Mahasiswa'),

                        Infolists\Components\TextEntry::make('mahasiswa.email')
                            ->label('Email Mahasiswa'),

                        Infolists\Components\TextEntry::make('dosen.nama_dosen')
                            ->label('Nama Dosen'),

                        Infolists\Components\TextEntry::make('dosen.email')
                            ->label('Email Dosen'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Informasi Mata Kuliah & Survey')
                    ->schema([
                        Infolists\Components\TextEntry::make('mataKuliah.nama_mk')
                            ->label('Mata Kuliah'),

                        Infolists\Components\TextEntry::make('mataKuliah.sks')
                            ->label('SKS')
                            ->badge(),

                        Infolists\Components\TextEntry::make('survey.judul')
                            ->label('Judul Survey'),

                        Infolists\Components\TextEntry::make('survey.deskripsi')
                            ->label('Deskripsi Survey')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Hasil Penilaian')
                    ->schema([
                        Infolists\Components\TextEntry::make('nilai')
                            ->label('Nilai Rata-rata')
                            ->badge()
                            ->size('lg')
                            ->color(fn (string $state): string => match (true) {
                                $state >= 3.5 => 'success',
                                $state >= 3.0 => 'primary',
                                $state >= 2.5 => 'warning',
                                default => 'danger',
                            })
                            ->formatStateUsing(fn ($state) => number_format($state, 2) . '/4'),

                        Infolists\Components\TextEntry::make('kategori_nilai')
                            ->label('Kategori')
                            ->badge()
                            ->color(fn ($record): string => match (true) {
                                $record->nilai >= 3.5 => 'success',
                                $record->nilai >= 3.0 => 'primary',
                                $record->nilai >= 2.5 => 'warning',
                                default => 'danger',
                            })
                            ->formatStateUsing(fn ($record) => match (true) {
                                $record->nilai >= 3.5 => 'Sangat Baik',
                                $record->nilai >= 3.0 => 'Baik',
                                $record->nilai >= 2.5 => 'Cukup',
                                default => 'Perlu Perbaikan',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detail Response')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('responses')
                            ->label('Jawaban Mahasiswa')
                            ->schema([
                                Infolists\Components\TextEntry::make('question.pertanyaan')
                                    ->label('Pertanyaan'),

                                Infolists\Components\TextEntry::make('nilai')
                                    ->label('Nilai')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        '1' => 'danger',
                                        '2' => 'warning',
                                        '3' => 'success',
                                        '4' => 'primary',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => $state ? $state . '/4' : '-'),

                                Infolists\Components\TextEntry::make('kritik_saran')
                                    ->label('Kritik & Saran')
                                    ->placeholder('Tidak ada kritik atau saran')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->state(function ($record) {
                                return Response::where('user_id', $record->mahasiswa_id)
                                    ->where('survey_id', $record->survey_id)
                                    ->with(['question'])
                                    ->get()
                                    ->toArray();
                            }),
                    ]),

                Infolists\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Penilaian')
                            ->dateTime('d M Y H:i:s'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Terakhir Diupdate')
                            ->dateTime('d M Y H:i:s'),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListPenilaianDosens::route('/'),
            'create' => Pages\CreatePenilaianDosen::route('/create'),
            'edit' => Pages\EditPenilaianDosen::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['mahasiswa', 'dosen', 'mataKuliah', 'survey']);
    }
}
