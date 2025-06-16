<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use App\Models\Survey;
use App\Models\User;
use App\Models\Response;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;

class ListUserResponses extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ResponseResource::class;

    protected static string $view = 'filament.resources.response-resource.pages.list-user-responses';

    public Survey $survey;
    public User $user;

    public function mount(Survey $survey, User $user): void
    {
        $this->survey = $survey;
        $this->user = $user;
    }

    public function getTitle(): string
    {
        return "Responses by {$this->user->name}";
    }

    public function getHeading(): string
    {
        return "Responses by {$this->user->name}";
    }

    public function getBreadcrumbs(): array
    {
        return [
            ResponseResource::getUrl() => 'Survey Responses',
            ResponseResource::getUrl('respondents', ['survey' => $this->survey->id]) => $this->survey->judul,
            '' => $this->user->name,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Response::query()
                    ->where('survey_id', $this->survey->id)
                    ->where('user_id', $this->user->id)
                    ->with(['question', 'mataKuliah', 'dosen'])
            )
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('question.pertanyaan')
                    ->label('Question')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->question->pertanyaan;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('question.tipe')
                    ->label('Question Type')
                    ->badge()
                    ->color(function ($state) {
                        return match($state) {
                            'rating' => 'success',
                            'text' => 'info',
                            'multiple_choice' => 'warning',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('mataKuliah.nama_mk')
                    ->label('Subject')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('dosen.nama_dosen')
                    ->label('Lecturer')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nilai')
                    ->label('Rating/Value')
                    ->badge()
                    ->color(function ($state, $record) {
                        if ($record->question->tipe === 'rating') {
                            return match(true) {
                                $state >= 4 => 'success',
                                $state >= 3 => 'warning',
                                default => 'danger',
                            };
                        }
                        return 'gray';
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->question->tipe === 'rating') {
                            return $state . '/4';
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('kritik_saran')
                    ->label('Comments/Feedback')
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->kritik_saran;
                    })
                    ->toggleable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('question.tipe')
                    ->label('Question Type')
                    ->options([
                        'rating' => 'Rating',
                        'text' => 'Text',
                        'multiple_choice' => 'Multiple Choice',
                    ]),

                Tables\Filters\SelectFilter::make('mk_id')
                    ->label('Subject')
                    ->relationship('mataKuliah', 'nama_mk'),

                Tables\Filters\SelectFilter::make('dosen_id')
                    ->label('Lecturer')
                    ->relationship('dosen', 'nama_dosen'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn ($record) => 'Response Details')
                    ->modalContent(function ($record) {
                        return view('filament.resources.response-resource.view-response-modal', [
                            'record' => $record,
                        ]);
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto refresh every 30 seconds
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_respondents')
                ->label('Back to Respondents')
                ->icon('heroicon-o-arrow-left')
                ->url(ResponseResource::getUrl('respondents', ['survey' => $this->survey->id])),

            Actions\Action::make('export_responses')
                ->label('Export Responses')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Implement export functionality here
                    Notification::make()
                        ->title('Export functionality will be implemented')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add user response statistics widget here if needed
        ];
    }
}
