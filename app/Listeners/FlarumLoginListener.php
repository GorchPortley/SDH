<?php

namespace App\Listeners;

use Maicol07\SSO\Flarum;
use Illuminate\Auth\Events\Login;

class FlarumLoginListener
{
    public function handle(Login $event)
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
            $flarum_user->login();
        } catch (\Exception $e) {
            report($e);
        }
    }
}
