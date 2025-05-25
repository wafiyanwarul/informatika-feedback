<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

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
        // 1. Check rate limiting first
        $this->checkRateLimit($data['email']);

        // 2. Validate Turnstile before authentication
        $this->validateTurnstile();

        return $data;
    }

    protected function checkRateLimit(string $email): void
    {
        $key = $this->throttleKey($email);

        // Check if too many attempts
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            Log::warning('Login rate limit exceeded', [
                'email' => $email,
                'ip' => request()->ip(),
                'retry_after' => $seconds
            ]);

            // Dispatch event to reset turnstile
            $this->dispatch('reset-turnstile');

            throw ValidationException::withMessages([
                'email' => [
                    "Too many login attempts. Please try again in {$seconds} seconds."
                ],
            ]);
        }
    }

    protected function handleAuthentication(array $data): void
    {
        $email = $data['email'];
        $key = $this->throttleKey($email);

        try {
            // Try authentication
            parent::handleAuthentication($data);

            // Clear rate limit on successful login
            RateLimiter::clear($key);

        } catch (ValidationException $e) {
            // Hit rate limiter on failed authentication
            RateLimiter::hit($key, 300); // 5 minutes decay

            Log::warning('Failed login attempt', [
                'email' => $email,
                'ip' => request()->ip(),
                'attempts' => RateLimiter::attempts($key)
            ]);

            // Dispatch event to reset turnstile
            $this->dispatch('reset-turnstile');

            throw $e;
        }
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

        try {
            $response = Http::timeout(10)->asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret'),
                'response' => $turnstileResponse,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            if (!$result['success']) {
                Log::warning('Turnstile verification failed', [
                    'ip' => request()->ip(),
                    'errors' => $result['error-codes'] ?? []
                ]);

                // Dispatch event to reset turnstile
                $this->dispatch('reset-turnstile');
                throw ValidationException::withMessages([
                    'email' => ['Security verification failed. Please try again.'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Turnstile validation error', [
                'error' => $e->getMessage(),
                'ip' => request()->ip()
            ]);

            // Dispatch event to reset turnstile
            $this->dispatch('reset-turnstile');
            throw ValidationException::withMessages([
                'email' => ['Security verification error. Please try again.'],
            ]);
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(string $email): string
    {
        return Str::transliterate(Str::lower($email) . '|' . request()->ip());
    }
}
