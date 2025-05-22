<x-filament-panels::page.simple>
    @if (filament()->hasRegistration())
    <x-slot name="subheading">
        {{ __('filament-panels::pages/auth/login.actions.register.before') }}

        {{ $this->registerAction }}
    </x-slot>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.before') }}

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <!-- Turnstile Appear Here -->
        <div class="mt-4 flex justify-center">
            <div class="rounded-xl overflow-hidden bg-white shadow">
                <div class="cf-turnstile"
                    data-sitekey="{{ config('services.turnstile.key') }}"
                    data-theme="light"
                    data-callback="onTurnstileSuccess"
                    data-error-callback="onTurnstileError"
                    data-expired-callback="onTurnstileExpired">
                </div>
            </div>
        </div>

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()" />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.after') }}

    <!-- Turnstile Scripts -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <script>
        function onTurnstileSuccess(token) {
            console.log('Turnstile success:', token);
        }

        function onTurnstileError(error) {
            console.error('Turnstile error:', error);
        }

        function onTurnstileExpired() {
            console.log('Turnstile expired');
            if (window.turnstile) {
                window.turnstile.reset();
            }
        }

        // Reset on validation errors
        document.addEventListener('livewire:init', () => {
            Livewire.on('reset-turnstile', () => {
                if (window.turnstile) {
                    setTimeout(() => window.turnstile.reset(), 100);
                }
            });
        });
    </script>
</x-filament-panels::page.simple>
