<?php

namespace App\Filament\Resources\FinalScoreResource\Pages;

use App\Filament\Resources\FinalScoreResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListFinalScores extends ListRecords
{
    protected static string $resource = FinalScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Scores'),
            'excellent' => Tab::make('Excellent (3.5 - 4.0)')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('final_score', [3.5, 4.0]))
                ->badge($this->getModel()::where('final_score', [3.5, 4.0])->count()),
            'good' => Tab::make('Good (3.0-3.49)')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('final_score', [3.0, 3.49]))
                ->badge($this->getModel()::whereBetween('final_score', [3.0, 3.49])->count()),
            'satisfactory' => Tab::make('Satisfactory (2.5-3.0)')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('final_score', [2.5, 3.0]))
                ->badge($this->getModel()::whereBetween('final_score', [2.5, 3.0])->count()),
            'needs_improvement' => Tab::make('Needs Improvement (<2.5)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('final_score', '<', 2.5))
                ->badge($this->getModel()::where('final_score', '<', 2.5)->count()),
        ];
    }
}
