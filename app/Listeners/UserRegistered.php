<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Maicol07\SSO\Flarum;
use App\Models\User;

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

        // Configure Flarum SSO options
        $options = [
            'url' => env('FLARUM_URL', 'http://your-domain.com/forum'),
            'api_key' => env('lZOH@zY8C%HmDSg&Jvxi*7&ym6ZQXR1d^Sk4q5qa'),
            'password_token' => env('FLARUM_PASSWORD_TOKEN', 'your_password_token'),
            // Add other necessary options from the maicol07-flarum-sso documentation
        ];

        // Initialize Flarum SSO
        $flarum = new Flarum($options);

        // Create/Update Flarum user with the registered user's data
        $flarum_user = $flarum->user($user->username);
        $flarum_user->attributes->email = $user->email;
        $flarum_user->attributes->password = $user->password;
        // You might want to set other attributes like:
        // $flarum_user->attributes->nickname = $user->name;

        try {
            $flarum_user->register();
        } catch (\Exception $e) {
            // Handle any registration errors
            report($e);
        }
    }
}
