<?php

namespace App\Http\Controllers\auth;

use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class GoogleController
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $client = new \GuzzleHttp\Client(['verify' => false]);

            $googleUser = Socialite::driver('google')
                ->stateless()
                ->setHttpClient($client)
                ->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null
                ]
            );

            if ($user->wasRecentlyCreated) {
                $user->assignRole('user');
                return redirect()->route('login')
                    ->with('success', 'Đăng ký bằng Google thành công! Vui lòng đăng nhập.');
            }

            Auth::login($user);

            $user->last_login = now();
            $user->save();

            $hasAccess = $user->roles()->whereIn('name', ['admin', 'staff'])->exists();

            if ($hasAccess) {
                return redirect('/admin');
            } else {
                return redirect('/')->with('success', 'Đăng nhập Google thành công!');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Đăng nhập Google thất bại: ' . $e->getMessage());
        }
    }
}
