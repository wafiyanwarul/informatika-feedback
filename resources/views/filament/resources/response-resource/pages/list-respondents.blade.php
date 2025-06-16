<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Survey Information Card --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $survey->judul }}
                    </h3>
                    @if($survey->deskripsi)
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ $survey->deskripsi }}
                        </p>
                    @endif
                    <div class="mt-4 flex flex-wrap gap-4">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-tag class="w-4 h-4 mr-1" />
                            Category: {{ $survey->kategoriSurvey->nama_kategori ?? 'N/A' }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-calendar class="w-4 h-4 mr-1" />
                            Created: {{ $survey->created_at->format('d M Y') }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-question-mark-circle class="w-4 h-4 mr-1" />
                            Questions: {{ $survey->questions()->count() }}
                        </div>
                    </div>
                </div>
                <div class="ml-6 flex flex-col items-end space-y-2">
                    <x-filament::badge color="success" size="lg">
                        {{ $survey->responses()->distinct('user_id')->count('user_id') }} Respondents
                    </x-filament::badge>
                    <x-filament::badge color="info" size="lg">
                        {{ $survey->responses()->count() }} Total Responses
                    </x-filament::badge>
                </div>
            </div>
        </div>

        {{-- Survey Statistics Cards --}}
        @php
            $totalRespondents = $survey->responses()->distinct('user_id')->count('user_id');
            $totalResponses = $survey->responses()->count();
            $totalQuestions = $survey->questions()->count();
            $averageRating = $survey->responses()
                ->whereHas('question', function($q) {
                    $q->where('tipe', 'rating');
                })
                ->avg('nilai');
            $completionRate = $totalQuestions > 0 && $totalRespondents > 0 ?
                round(($totalResponses / ($totalRespondents * $totalQuestions)) * 100, 1) : 0;
            $responsesWithComments = $survey->responses()
                ->whereNotNull('kritik_saran')
                ->where('kritik_saran', '!=', '')
                ->count();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-users class="w-8 h-8 text-blue-500" />
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $totalRespondents }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Total Respondents
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-chat-bubble-left-right class="w-8 h-8 text-green-500" />
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $totalResponses }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Total Responses
                        </div>
                    </div>
                </div>
            </div>

            @if($averageRating)
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-star class="w-8 h-8 text-yellow-500" />
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold {{ $averageRating >= 4 ? 'text-green-600 dark:text-green-400' : ($averageRating >= 3 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                {{ number_format($averageRating, 1) }}/5
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Average Rating
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-chart-bar class="w-8 h-8 text-purple-500" />
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold {{ $completionRate >= 80 ? 'text-green-600 dark:text-green-400' : ($completionRate >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                            {{ $completionRate }}%
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Completion Rate
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional Statistics Row --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <div class="text-center">
                    <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                        {{ $responsesWithComments }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Responses with Comments
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <div class="text-center">
                    <div class="text-xl font-bold text-teal-600 dark:text-teal-400">
                        {{ $totalQuestions }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Total Questions
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <div class="text-center">
                    <div class="text-xl font-bold text-orange-600 dark:text-orange-400">
                        {{ $totalResponses > 0 ? round(($responsesWithComments / $totalResponses) * 100, 1) : 0 }}%
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Comment Rate
                    </div>
                </div>
            </div>
        </div>

        {{-- Respondents Table --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Survey Respondents
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Click on any respondent to view their detailed responses
                </p>
            </div>
            <div class="p-4">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
