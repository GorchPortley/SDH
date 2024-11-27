<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Maicol07\SSO\Flarum;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRegistered
{
    /**
     * Create the event listener.
     */

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;
        $flarum = new Flarum($options);
        $user = $flarum->user($username)
        $user_alias = $flarum->user();
        $flarum_user->attributes->email = 'user@example.com';
        $flarum_user->attributes->password = 'userpassword';
        $flarum_user->register();
    }
}
