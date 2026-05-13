<?php

namespace App\Http\Controllers\auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class FacebookController
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }
    public function handleFacebookCallback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->fields([
                'name',
                'email'
            ])->user();

            $user = User::firstOrCreate(
                ['email' => $fbUser->getEmail()],
                [
                    'name' => $fbUser->getName(),
                    'password' => null,
                    'provider' => 'facebook',
                    'provider_id' => $fbUser->getId(),
                    'email_verified_at' => now(),
                    'avatar' => $fbUser->getAvatar(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $user->assignRole('user');
                return redirect()->route('login')
                    ->with('success', 'Đăng ký bằng Facebook thành công! Vui lòng đăng nhập.');
            }

            Auth::login($user);

            $user->last_login = now();
            $user->save();

            $hasAccess = $user->roles()->whereIn('name', ['admin', 'staff'])->exists();

            if ($hasAccess) {
                return redirect('/admin');
            } else {
                return redirect('/')->with('success', 'Đăng nhập Facebook thành công!');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Đăng nhập Facebook thất bại: ' . $e->getMessage());
        }
    }
}
