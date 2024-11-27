<?php

namespace App\Listeners;

use Wave\Models\User;
use Illuminate\Auth\Events\Registered;
use Maicol07\SSO\Flarum;

class UserRegistered
{
    public function handle(Registered $event): void
    {
        $user = $event->user;

        $options = [
            'url' => env('FLARUM_URL'),
            'api_key' => env('FLARUM_API_KEY'),
            'password_token' => env('FLARUM_PASSWORD_TOKEN'),
        ];

        try {
            $flarum = new Flarum($options);
            $flarum_user = $flarum->user($user->username);
            $flarum_user->attributes->email = $user->email;
            $flarum_user->attributes->password = $user->password;
            $flarum_user->attributes->username = $user->username;
            $flarum_user->signup();
        } catch (\Exception $e) {
            report($e);
        }
    }
}
