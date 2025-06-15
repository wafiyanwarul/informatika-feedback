<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use App\Models\Survey;
use App\Models\User;
use App\Models\Response;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class ListUserResponses extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ResponseResource::class;
    protected static string $view = 'filament.resources.response-resource.pages.list-user-responses';

    public Survey $survey;
    public User $user;

    public function mount(int|string $survey, int|string $user): void
    {
        $this->survey = Survey::findOrFail($survey);
        $this->user = User::findOrFail($user);
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
                Tables\Columns\TextColumn::make('question.pertanyaan')
                    ->label('Pertanyaan')
                    ->wrap()
                    ->limit(50),

                Tables\Columns\TextColumn::make('question.tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'rating' => 'success',
                        'kritik_saran' => 'info',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('mataKuliah.nama')
                    ->label('Mata Kuliah')
                    ->sortable(),

                Tables\Columns\TextColumn::make('dosen.nama')
                    ->label('Dosen')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nilai')
                    ->label('Nilai')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn($state) => $state ? $state . '/4' : '-')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('kritik_saran')
                    ->label('Kritik & Saran')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (!$state || strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Jawab')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn($record) => ResponseResource::getUrl('view', [
                        'survey' => $this->survey->id,
                        'user' => $this->user->id,
                        'response' => $record->id
                    ])),
                Tables\Actions\EditAction::make()
                    ->url(fn($record) => ResponseResource::getUrl('edit', [
                        'survey' => $this->survey->id,
                        'user' => $this->user->id,
                        'response' => $record->id
                    ])),

                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Hapus Response')
                    ->modalDescription('Apakah Anda yakin ingin menghapus response ini?')
                    ->modalSubmitActionLabel('Hapus')
                    ->successNotificationTitle('Response berhasil dihapus'),
            ])
            ->defaultSort('created_at');
    }

    public function getTitle(): string
    {
        return 'Jawaban - ' . $this->user->name;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getBreadcrumbs(): array
    {
        return [
            ResponseResource::getUrl('index') => 'Survey Responses',
            ResponseResource::getUrl('respondents', ['survey' => $this->survey->id]) => $this->survey->judul,
            '' => $this->user->name,
        ];
    }
}
