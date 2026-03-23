<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Traits\Ldap\AutenticacionTrait;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{

    use AutenticacionTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'min:4'],
            'password' => ['required', 'min:8'],
        ];
    }

    public function authenticate(): void
    {

        $this->ensureIsNotRateLimited();

        $requestArray = [
            'username' => strtoupper(trim($this->input('username'))),
            'password' => $this->input('password')
        ];

        $resultado = $this->autenticarConLDAP(
            $requestArray['username'],
            $requestArray['password'],
            $this->ip(),
            $this->userAgent()
        );

        if (!$resultado['success']) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => $resultado['message']
            ]);
        }

        $user = User::where('usuario', $resultado['usuario'])
            ->where('activo', true)
            ->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => 'No tiene acceso a esta aplicación.',
            ]);
        }

        Auth::login($user);
        RateLimiter::clear($this->throttleKey());

    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }
        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('username')) . '|' . $this->ip());
    }

    public function messages()
    {
        return [
            'username.required' => 'Debe proporcionar un Usuario.',
            'password.required' => 'Debe proporcionar una Contraseña.',
            'username.min' => 'El usuario debe tener al menos 4 caracteres.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
        ];
    }
}
