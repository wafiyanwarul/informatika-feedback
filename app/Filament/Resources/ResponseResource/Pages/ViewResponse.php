<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use App\Models\Survey;
use App\Models\User;
use App\Models\Response;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewResponse extends ViewRecord
{
    protected static string $resource = ResponseResource::class;

    public Survey $survey;
    public User $user;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record = Response::findOrFail($record);
        $this->survey = Survey::findOrFail($this->record->survey_id);
        $this->user = User::findOrFail($this->record->user_id);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Response')
                    ->schema([
                        Infolists\Components\TextEntry::make('survey.judul')
                            ->label('Survey'),

                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Mahasiswa'),

                        Infolists\Components\TextEntry::make('mataKuliah.nama')
                            ->label('Mata Kuliah'),

                        Infolists\Components\TextEntry::make('dosen.nama')
                            ->label('Dosen'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Waktu Jawab')
                            ->dateTime('d M Y H:i:s'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Pertanyaan & Jawaban')
                    ->schema([
                        Infolists\Components\TextEntry::make('question.pertanyaan')
                            ->label('Pertanyaan')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('question.tipe')
                            ->label('Tipe Pertanyaan')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'rating' => 'success',
                                'kritik_saran' => 'info',
                                default => 'gray'
                            }),

                        Infolists\Components\TextEntry::make('nilai')
                            ->label('Nilai Rating')
                            ->badge()
                            ->color('success')
                            ->formatStateUsing(fn ($state) => $state ? $state . '/4' : 'Tidak ada nilai')
                            ->visible(fn ($record) => $record->question->tipe === 'rating'),

                        Infolists\Components\TextEntry::make('kritik_saran')
                            ->label('Kritik & Saran')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada kritik atau saran')
                            ->visible(fn ($record) => $record->question->tipe === 'kritik_saran'),
                    ])
                    ->columns(2),
            ]);
    }

    public function getTitle(): string
    {
        return 'Response #' . $this->record->id;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make()
                ->url(fn() => ResponseResource::getUrl('edit', [
                    'survey' => $this->survey->id,
                    'user' => $this->user->id,
                    'response' => $this->record->id
                ])),

            \Filament\Actions\DeleteAction::make()
                ->modalHeading('Hapus Response')
                ->modalDescription('Apakah Anda yakin ingin menghapus response ini?')
                ->modalSubmitActionLabel('Hapus')
                ->successNotificationTitle('Response berhasil dihapus')
                ->successRedirectUrl(ResponseResource::getUrl('responses', [
                    'survey' => $this->survey->id,
                    'user' => $this->user->id
                ])),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            ResponseResource::getUrl('index') => 'Survey Responses',
            ResponseResource::getUrl('respondents', ['survey' => $this->survey->id]) => $this->survey->judul,
            ResponseResource::getUrl('responses', ['survey' => $this->survey->id, 'user' => $this->user->id]) => $this->user->name,
            '' => 'Response #' . $this->record->id,
        ];
    }
}
