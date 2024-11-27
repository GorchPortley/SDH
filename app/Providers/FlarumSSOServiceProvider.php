<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Maicol07\SSO\Flarum;

class FlarumSSOServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Event::listen(Login::class, function ($event) {
            try {
                $user = $event->user;
                $flarum = new Flarum([
                    'url' => env('FLARUM_URL'),
                    'api_key' => env('FLARUM_API_KEY'),
                    'password_token' => env('FLARUM_PASSWORD_TOKEN'),
                    'setCookie' => true
                ]);

                $flarum_user = $flarum->user($user->username);
                $flarum_user->attributes->username = $user->username;
                $flarum_user->attributes->email = $user->email;
                $flarum_user->login();
            } catch (\Exception $e) {
                report($e);
            }
        });

        Event::listen(Registered::class, function ($event) {
            try {
                $user = $event->user;
                $flarum = new Flarum([
                    'url' => env('FLARUM_URL'),
                    'api_key' => env('FLARUM_API_KEY'),
                    'password_token' => env('FLARUM_PASSWORD_TOKEN'),
                    'setCookie' => true
                ]);

                $flarum_user = $flarum->user($user->username);
                $flarum_user->attributes->username = $user->username;
                $flarum_user->attributes->email = $user->email;
                $flarum_user->attributes->password = $user->password;
                $flarum_user->signUp();
            } catch (\Exception $e) {
                report($e);
            }
        });
    }
}
