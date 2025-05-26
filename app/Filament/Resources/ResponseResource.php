<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResponseResource\Pages;
use App\Filament\Resources\ResponseResource\RelationManagers;
use App\Models\Response;
use App\Models\SurveyQuestion;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;

class ResponseResource extends Resource
{
    protected static ?string $model = Response::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Manage Responses & Final Scores';
    protected static ?string $navigationLabel = 'Responses';
    protected static ?string $modelLabel = 'Response';
    protected static ?string $pluralModelLabel = 'Responses';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Response')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Mahasiswa')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('survey_id')
                            ->label('Survey')
                            ->relationship('survey', 'judul')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn(callable $set) => $set('question_id', null)),

                        Forms\Components\Select::make('question_id')
                            ->label('Pertanyaan')
                            ->options(function (callable $get) {
                                $surveyId = $get('survey_id');
                                if (!$surveyId) {
                                    return [];
                                }
                                return SurveyQuestion::where('survey_id', $surveyId)
                                    ->pluck('pertanyaan', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if ($state) {
                                    $question = SurveyQuestion::find($state);
                                    if ($question && $question->tipe === 'kritik_saran') {
                                        $set('nilai', null);
                                    } elseif ($question && $question->tipe === 'rating') {
                                        $set('kritik_saran', null);
                                    }
                                }
                            }),
                    ])
                    ->columns(2),

                Section::make('Jawaban')
                    ->schema([
                        Forms\Components\Select::make('nilai')
                            ->label('Nilai Rating')
                            ->options([
                                1 => '1 - Sangat Tidak Baik',
                                2 => '2 - Tidak Baik',
                                3 => '3 - Baik',
                                4 => '4 - Sangat Baik'
                            ])
                            ->visible(function (callable $get) {
                                $questionId = $get('question_id');
                                if (!$questionId) return false;
                                $question = SurveyQuestion::find($questionId);
                                return $question && $question->tipe === 'rating';
                            })
                            ->required(function (callable $get) {
                                $questionId = $get('question_id');
                                if (!$questionId) return false;
                                $question = SurveyQuestion::find($questionId);
                                return $question && $question->tipe === 'rating';
                            }),

                        Forms\Components\Textarea::make('kritik_saran')
                            ->label('Kritik & Saran')
                            ->rows(4)
                            ->columnSpanFull()
                            ->visible(function (callable $get) {
                                $questionId = $get('question_id');
                                if (!$questionId) return false;
                                $question = SurveyQuestion::find($questionId);
                                return $question && $question->tipe === 'kritik_saran';
                            })
                            ->required(function (callable $get) {
                                $questionId = $get('question_id');
                                if (!$questionId) return false;
                                $question = SurveyQuestion::find($questionId);
                                return $question && $question->tipe === 'kritik_saran';
                            }),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('survey.judul')
                    ->label('Survey')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('question.pertanyaan')
                    ->label('Pertanyaan')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->searchable(),

                TextColumn::make('question.tipe')
                    ->badge()
                    ->label('Tipe')
                    ->colors([
                        'success' => 'rating',
                        'info' => 'kritik_saran',
                    ])
                    ->icons([
                        'heroicon-s-star' => 'rating',
                        'heroicon-s-chat-bubble-left' => 'kritik_saran',
                    ]),

                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'danger',
                        '2' => 'warning',
                        '3' => 'success',
                        '4' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state ? $state . '/4' : '-')
                    ->sortable(),

                TextColumn::make('kritik_saran')
                    ->label('Kritik & Saran')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (!$state || strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    })
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('survey_id')
                    ->label('Survey')
                    ->relationship('survey', 'judul')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('user_id')
                    ->label('Mahasiswa')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('question.tipe')
                    ->label('Tipe Pertanyaan')
                    ->options([
                        'rating' => 'Rating',
                        'kritik_saran' => 'Kritik & Saran'
                    ]),

                SelectFilter::make('nilai')
                    ->label('Nilai Rating')
                    ->options([
                        1 => '1 - Sangat Tidak Baik',
                        2 => '2 - Tidak Baik',
                        3 => '3 - Baik',
                        4 => '4 - Sangat Baik'
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Response')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Mahasiswa'),

                        TextEntry::make('user.email')
                            ->label('Email Mahasiswa'),

                        TextEntry::make('survey.judul')
                            ->label('Survey'),

                        TextEntry::make('survey.deskripsi')
                            ->label('Deskripsi Survey')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                InfolistSection::make('Detail Pertanyaan & Jawaban')
                    ->schema([
                        TextEntry::make('question.pertanyaan')
                            ->label('Pertanyaan')
                            ->columnSpanFull(),

                        TextEntry::make('question.tipe')
                            ->label('Tipe Pertanyaan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'rating' => 'success',
                                'kritik_saran' => 'info',
                                default => 'gray',
                            }),

                        TextEntry::make('nilai')
                            ->label('Nilai Rating')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                '1' => 'danger',
                                '2' => 'warning',
                                '3' => 'success',
                                '4' => 'primary',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => $state ? $state . '/4' : 'Tidak ada nilai')
                            ->visible(fn ($record) => $record->question->tipe === 'rating'),

                        TextEntry::make('kritik_saran')
                            ->label('Kritik & Saran')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada kritik atau saran')
                            ->visible(fn ($record) => $record->question->tipe === 'kritik_saran'),
                    ])
                    ->columns(2),

                InfolistSection::make('Informasi Tambahan')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->dateTime('d M Y H:i:s'),

                        TextEntry::make('updated_at')
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
            'index' => Pages\ListResponses::route('/'),
            'create' => Pages\CreateResponse::route('/create'),
            'edit' => Pages\EditResponse::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'survey', 'question']);
    }
}
