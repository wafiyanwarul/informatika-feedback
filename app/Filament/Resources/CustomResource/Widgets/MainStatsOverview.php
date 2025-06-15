<?php

namespace App\Filament\Resources\CustomResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Role;
use App\Models\User;
use App\Models\Survey;
use App\Models\KategoriSurvey;
use App\Models\SurveyQuestion;
use App\Models\BobotNilai;

class MainStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalQuestions = SurveyQuestion::count();
        $ratingQuestions = SurveyQuestion::where('tipe', 'rating')->count();
        $kritikSaranQuestions = SurveyQuestion::where('tipe', 'kritik_saran')->count();

        $surveysWithQuestions = Survey::withCount('questions')->get();
        $avgQuestionsPerSurvey = $surveysWithQuestions->count() > 0
            ? round($surveysWithQuestions->avg('questions_count'), 1)
            : 0;

        $totalBobotNilai = BobotNilai::count();
        $bobotNilaiTertinggi = BobotNilai::max('skor') ?? 0;
        $bobotNilaiTerendah = BobotNilai::min('skor') ?? 0;

        return [
            Stat::make('Total Dosen', Dosen::count())
                ->description('Jumlah dosen aktif')
                ->icon('heroicon-o-academic-cap')
                ->color('success'),

            Stat::make('Total Mata Kuliah', MataKuliah::count())
                ->description('Jumlah mata kuliah terdaftar')
                ->icon('heroicon-o-book-open')
                ->color('info'),

            Stat::make('Total Role', Role::count())
                ->description('Jumlah role sistem')
                ->icon('heroicon-o-lock-closed')
                ->color('warning'),

            Stat::make('Total Users', User::count())
                ->description('Jumlah pengguna terdaftar')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Total Mahasiswa', User::where('role_id', '2')->count())
                ->description('Jumlah mahasiswa terdaftar')
                ->icon('heroicon-o-user-group')
                ->color('danger'),

            Stat::make('Total Surveys', Survey::count())
                ->description('Survey yang telah dibuat')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Survey Categories', KategoriSurvey::count())
                ->description('Kategori survey tersedia')
                ->icon('heroicon-o-tag')
                ->color('warning'),

            Stat::make('Rating Weights', $totalBobotNilai)
                ->description($totalBobotNilai > 0 ? "Range: {$bobotNilaiTerendah} - {$bobotNilaiTertinggi}" : 'Belum ada bobot nilai')
                ->icon('heroicon-o-star')
                ->color('purple'),

            Stat::make('Total Questions', $totalQuestions)
                ->description("Rating: {$ratingQuestions} | Kritik & Saran: {$kritikSaranQuestions}")
                ->icon('heroicon-o-question-mark-circle')
                ->color('info'),

            Stat::make('Avg Questions/Survey', $avgQuestionsPerSurvey)
                ->description('Rata-rata pertanyaan per survey')
                ->icon('heroicon-o-calculator')
                ->color('gray'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return 'Utama'; // You can customize this text as needed
    }
}
