<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Maicol07\SSO\Flarum;
use Illuminate\Support\Facades\Log;

class UserLoggedIn
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        Log::info('Starting Flarum SSO login for user:', [
            'username' => $user->username,
            'email' => $user->email
        ]);

        $options = [
            'url' => env('FLARUM_URL'),
            'api_key' => env('FLARUM_API_KEY'),
            'password_token' => env('FLARUM_PASSWORD_TOKEN'),
        ];

        Log::info('SSO Options:', $options);

        try {
            $flarum = new Flarum($options);
            $flarum_user = $flarum->user($user->username);

            // Set the user attributes
            $flarum_user->attributes->username = $user->username;
            $flarum_user->attributes->email = $user->email;

            Log::info('Attempting Flarum login');
            $response = $flarum_user->login();
            Log::info('Flarum login response:', ['response' => $response]);

        } catch (\Exception $e) {
            Log::error('Flarum SSO Login Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            report($e);
        }
}}
