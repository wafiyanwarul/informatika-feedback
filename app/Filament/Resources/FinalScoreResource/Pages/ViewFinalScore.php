<?php

namespace App\Filament\Resources\FinalScoreResource\Pages;

use App\Filament\Resources\FinalScoreResource;
use App\Models\FinalScore;
use App\Models\PenilaianDosen;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;

class ViewFinalScore extends ViewRecord
{
    protected static string $resource = FinalScoreResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Final Score Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('dosen.nama_dosen')
                            ->label('Dosen Name')
                            ->weight(FontWeight::Bold),

                        Infolists\Components\TextEntry::make('dosen.email')
                            ->label('Dosen Email')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('mataKuliah.nama_mk')
                            ->label('Mata Kuliah')
                            ->weight(FontWeight::Bold),

                        Infolists\Components\TextEntry::make('mataKuliah.sks')
                            ->label('SKS')
                            ->badge(),

                        Infolists\Components\TextEntry::make('final_score')
                            ->label('Final Score')
                            ->badge()
                            ->color(fn($state) => match (true) {
                                $state >= 4.5 => 'success',
                                $state >= 3.5 => 'info',
                                $state >= 2.5 => 'warning',
                                default => 'danger',
                            })
                            ->formatStateUsing(fn($state) => number_format($state, 2)),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Evaluation Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('evaluation_count')
                            ->label('Total Evaluations')
                            ->getStateUsing(function ($record) {
                                return PenilaianDosen::where('dosen_id', $record->dosen_id)
                                    ->where('mk_id', $record->mata_kuliah_id)
                                    ->count();
                            }),

                        Infolists\Components\TextEntry::make('score_breakdown')
                            ->label('Score Breakdown')
                            ->getStateUsing(function ($record) {
                                $scores = PenilaianDosen::where('dosen_id', $record->dosen_id)
                                    ->where('mk_id', $record->mata_kuliah_id)
                                    ->pluck('nilai');

                                if ($scores->isEmpty()) {
                                    return 'No data available';
                                }

                                return sprintf(
                                    'Min: %.2f | Max: %.2f | Avg: %.2f',
                                    $scores->min(),
                                    $scores->max(),
                                    $scores->avg()
                                );
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),

            Actions\Action::make('recalculate')
                ->label('Recalculate Score')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function () {
                    $record = $this->getRecord();
                    $newScore = FinalScore::calculateFinalScore($record->dosen_id, $record->mata_kuliah_id);

                    if ($newScore !== null) {
                        $record->update(['final_score' => $newScore]);

                        $this->refreshFormData([
                            'final_score' => $newScore,
                            'updated_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Score Recalculated')
                            ->body("Final score updated to {$newScore}")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('No Data Found')
                            ->body('No evaluation data available for recalculation')
                            ->warning()
                            ->send();
                    }
                })
                ->requiresConfirmation(),
        ];
    }
}
