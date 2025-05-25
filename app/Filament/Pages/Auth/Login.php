<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

// Custom Login Page (Not from Filament)
class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->autocomplete('username'),
            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->required()
                ->autocomplete('current-password'),
        ];
    }

    protected function mutateFormDataBeforeAuthenticate(array $data): array
    {
        // Validate Turnstile before authentication
        $this->validateTurnstile();
        return $data;
    }

    protected function validateTurnstile(): void
    {
        $turnstileResponse = request()->input('cf-turnstile-response');

        if (!$turnstileResponse) {
            // Dispatch event to reset turnstile
            $this->dispatch('reset-turnstile');

            throw ValidationException::withMessages([
                'email' => ['Please complete the security verification.'],
            ]);
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret'),
            'response' => $turnstileResponse,
            'remoteip' => request()->ip(),
        ]);

        $result = $response->json();

        if (!$result['success']) {
            // Dispatch event to reset turnstile
            $this->dispatch('reset-turnstile');
            throw ValidationException::withMessages([
                'email' => ['Security verification failed. Please try again.'],
            ]);
        }
    }
}
