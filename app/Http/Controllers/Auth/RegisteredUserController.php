<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $normalizedEmail = strtolower(trim((string) $request->input('email')));
        $normalizedPostcode = $request->postcode ? strtoupper(trim((string) $request->postcode)) : null;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (User::query()
                        ->where('email', $value)
                        ->whereNotNull('email_verified_at')
                        ->exists()) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:12', 'regex:/^[A-Z]{1,2}\d[A-Z\d]?\s?\d[A-Z]{2}$/i'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::query()
            ->where('email', $normalizedEmail)
            ->whereNull('email_verified_at')
            ->first();

        if ($user !== null) {
            $user->fill([
                'name' => $request->name,
                'email' => $normalizedEmail,
                'address' => $request->address,
                'postcode' => $normalizedPostcode,
                'role' => User::ROLE_RESIDENT,
                'password' => Hash::make($request->password),
            ]);
            $user->email_verified_at = null;
            $user->setRememberToken(null);
            $user->save();
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $normalizedEmail,
                'address' => $request->address,
                'postcode' => $normalizedPostcode,
                'role' => User::ROLE_RESIDENT,
                'password' => Hash::make($request->password),
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))
            ->with('registration_status', 'Registration successful.');
    }
}
