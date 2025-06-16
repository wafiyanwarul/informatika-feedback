<div class="space-y-6">
    {{-- Question Information --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Question</h4>
        <p class="text-gray-700 dark:text-gray-300">{{ $record->question->pertanyaan }}</p>
        <div class="mt-2">
            <x-filament::badge color="primary" size="sm">
                {{ ucfirst($record->question->tipe) }}
            </x-filament::badge>
        </div>
    </div>

    {{-- Context Information --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($record->mataKuliah)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Subject</h4>
                <p class="text-gray-700 dark:text-gray-300">{{ $record->mataKuliah->nama_mk }}</p>
            </div>
        @endif

        @if($record->dosen)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Lecturer</h4>
                <p class="text-gray-700 dark:text-gray-300">{{ $record->dosen->nama_dosen }}</p>
            </div>
        @endif
    </div>

    {{-- Response Information --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Response</h4>

        @if($record->question->tipe === 'rating')
            <div class="flex items-center space-x-2 mb-3">
                <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $record->nilai }}/5</span>
                <div class="flex space-x-1">
                    @for($i = 1; $i <= 5; $i++)
                        <x-heroicon-s-star class="w-5 h-5 {{ $i <= $record->nilai ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" />
                    @endfor
                </div>
            </div>
        @else
            <p class="text-gray-700 dark:text-gray-300 mb-3">{{ $record->nilai }}</p>
        @endif

        @if($record->kritik_saran)
            <div class="border-t pt-3">
                <h5 class="font-medium text-gray-900 dark:text-white mb-2">Comments/Feedback</h5>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $record->kritik_saran }}</p>
            </div>
        @endif
    </div>

    {{-- Metadata --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Metadata</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600 dark:text-gray-400">Submitted:</span>
                <span class="text-gray-900 dark:text-white ml-2">{{ $record->created_at->format('d M Y H:i:s') }}</span>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Updated:</span>
                <span class="text-gray-900 dark:text-white ml-2">{{ $record->updated_at->format('d M Y H:i:s') }}</span>
            </div>
        </div>
    </div>
</div>
