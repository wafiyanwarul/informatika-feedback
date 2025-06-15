<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use App\Models\Survey;
use App\Models\User;
use App\Models\Response;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;

class EditResponse extends EditRecord
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Response')
                    ->schema([
                        Forms\Components\TextInput::make('survey.judul')
                            ->label('Survey')
                            ->disabled(),

                        Forms\Components\TextInput::make('user.name')
                            ->label('Mahasiswa')
                            ->disabled(),

                        Forms\Components\TextInput::make('mataKuliah.nama')
                            ->label('Mata Kuliah')
                            ->disabled(),

                        Forms\Components\TextInput::make('dosen.nama')
                            ->label('Dosen')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pertanyaan & Jawaban')
                    ->schema([
                        Forms\Components\Textarea::make('question.pertanyaan')
                            ->label('Pertanyaan')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('nilai')
                            ->label('Nilai Rating')
                            ->options([
                                1 => '1 - Sangat Tidak Baik',
                                2 => '2 - Tidak Baik',
                                3 => '3 - Baik',
                                4 => '4 - Sangat Baik'
                            ])
                            ->visible(fn ($record) => $record->question->tipe === 'rating')
                            ->required(fn ($record) => $record->question->tipe === 'rating'),

                        Forms\Components\Textarea::make('kritik_saran')
                            ->label('Kritik & Saran')
                            ->rows(4)
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->question->tipe === 'kritik_saran')
                            ->required(fn ($record) => $record->question->tipe === 'kritik_saran'),
                    ])
                    ->columns(1),
            ]);
    }

    public function getTitle(): string
    {
        return 'Edit Response #' . $this->record->id;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ViewAction::make()
                ->url(fn() => ResponseResource::getUrl('view', [
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

    protected function getRedirectUrl(): string
    {
        return ResponseResource::getUrl('view', [
            'survey' => $this->survey->id,
            'user' => $this->user->id,
            'response' => $this->record->id
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            ResponseResource::getUrl('index') => 'Survey Responses',
            ResponseResource::getUrl('respondents', ['survey' => $this->survey->id]) => $this->survey->judul,
            ResponseResource::getUrl('responses', ['survey' => $this->survey->id, 'user' => $this->user->id]) => $this->user->name,
            ResponseResource::getUrl('view', ['survey' => $this->survey->id, 'user' => $this->user->id, 'response' => $this->record->id]) => 'Response #' . $this->record->id,
            '' => 'Edit',
        ];
    }
}
