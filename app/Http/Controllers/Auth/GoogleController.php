<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        // Restrict login to authorium.com domain
        if (!str_ends_with($googleUser->email, '@authorium.com')) {
            return redirect('/login')->with('error', 'Only @authorium.com emails are allowed.');
        }

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'password' => Hash::make(Str::random(16)), // Generate a hashed random password
            ]
        );

        // Log in the user
        Auth::login($user);

        return redirect('/releases');
    }
}
