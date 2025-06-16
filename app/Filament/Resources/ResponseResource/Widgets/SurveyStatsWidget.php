<?php

namespace App\Filament\Resources\ResponseResource\Widgets;

use App\Models\Survey;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SurveyStatsWidget extends BaseWidget
{
    public Survey $survey;

    public function __construct($survey = null)
    {
        $this->survey = $survey;
    }

    protected function getStats(): array
    {
        $totalRespondents = $this->survey->responses()->distinct('user_id')->count('user_id');
        $totalResponses = $this->survey->responses()->count();
        $averageRating = $this->survey->responses()
            ->whereHas('question', function($q) {
                $q->where('tipe', 'rating');
            })
            ->avg('nilai');

        $totalQuestions = $this->survey->questions()->count();
        $completionRate = $totalQuestions > 0 ?
            round(($totalResponses / ($totalRespondents * $totalQuestions)) * 100, 1) : 0;

        return [
            Stat::make('Total Respondents', $totalRespondents)
                ->description('Unique users who responded')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Responses', $totalResponses)
                ->description('Individual question responses')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),

            Stat::make('Average Rating', $averageRating ? number_format($averageRating, 1) . '/4' : 'N/A')
                ->description('Average rating across all questions')
                ->descriptionIcon('heroicon-m-star')
                ->color($averageRating >= 3 ? 'success' : ($averageRating >= 2 && $averageRating < 3 ? 'warning' : 'danger')),

            Stat::make('Completion Rate', $completionRate . '%')
                ->description('Response completion percentage')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($completionRate >= 80 ? 'success' : ($completionRate >= 60 ? 'warning' : 'danger')),
        ];
    }
}
