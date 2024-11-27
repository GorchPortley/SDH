<?php

namespace App\Listeners;

use Wave\Models\User;
use Illuminate\Auth\Events\Registered;
use Maicol07\SSO\Flarum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class UserRegistered
{
    public function handle(Registered $event): void
    {
        $user = $event->user;

        Log::info('Starting Flarum SSO registration for user:', [
            'username' => $user->name,
            'email' => $user->email
        ]);

        $options = [
            'url' => env('FLARUM_URL'),
            'api_key' => env('FLARUM_API_KEY'),
            'password_token' => env('FLARUM_PASSWORD_TOKEN'),
            'setCookie' => true  // Make sure cookie setting is enabled
        ];

        Log::info('SSO Options:', $options);

        try {
            $flarum = new Flarum($options);
            $flarum_user = $flarum->user($user->username);

            // Set all possible attributes
            $flarum_user->attributes->username = $user->username;
            $flarum_user->attributes->email = $user->email;
            $flarum_user->attributes->password = $user->password;

            Log::info('Attempting Flarum signUp');
            $token = $flarum_user->signUp();

            // Manually set the token cookie if needed
            if ($token) {
                $cookie = cookie('flarum_remember', $token, 60 * 24 * 30, '/', null, false, true);
                Cookie::queue($cookie);
                Log::info('Flarum token cookie set:', ['token' => $token]);
            }

        } catch (\Exception $e) {
            Log::error('Flarum SSO Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            report($e);
        }
    }
}
