<x-filament-panels::page>
    <div class="space-y-6">
        {{-- User and Survey Information Card --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-4">
                        @if($user->foto_profil)
                            <img src="{{ $user->foto_profil }}" alt="{{ $user->name }}"
                                 class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                <span class="text-lg font-semibold text-gray-600 dark:text-gray-300">
                                    {{ substr($user->name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $user->name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $user->email }}
                            </p>
                            @if($user->role)
                                <x-filament::badge color="primary" size="sm" class="mt-1">
                                    {{ $user->role->nama_role }}
                                </x-filament::badge>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>Survey:</strong> {{ $survey->judul }}
                        </p>
                    </div>
                </div>
                <div class="ml-6 flex flex-col items-end space-y-2">
                    <x-filament::badge color="success" size="lg">
                        {{ $user->responses()->where('survey_id', $survey->id)->count() }} Responses
                    </x-filament::badge>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Last response: {{ $user->responses()->where('survey_id', $survey->id)->latest()->first()?->created_at?->format('d M Y H:i') ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Response Summary Statistics --}}
        @php
            $totalResponses = $user->responses()->where('survey_id', $survey->id)->count();
            $ratingResponses = $user->responses()->where('survey_id', $survey->id)
                ->whereHas('question', function($q) { $q->where('tipe', 'rating'); })->get();
            $averageRating = $ratingResponses->avg('nilai');
        @endphp

        @if($totalResponses > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $totalResponses }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Total Responses
                        </div>
                    </div>
                </div>

                @if($averageRating)
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $averageRating >= 4 ? 'text-green-600 dark:text-green-400' : ($averageRating >= 3 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                {{ number_format($averageRating, 1) }}/5
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Average Rating
                            </div>
                        </div>
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $user->responses()->where('survey_id', $survey->id)->whereNotNull('kritik_saran')->where('kritik_saran', '!=', '')->count() }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            With Comments
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Responses Table --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
