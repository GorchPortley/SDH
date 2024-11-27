<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Wave\User as WaveUser;
use Illuminate\Notifications\Notifiable;
use Wave\Traits\HasProfileKeyValues;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends WaveUser
{
    use Notifiable, HasProfileKeyValues, HasFactory;

    public $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'avatar',
        'password',
        'role_id',
        'verification_code',
        'verified',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function boot()
    {
        parent::boot();

        // Listen for the creating event of the model
        static::creating(function ($user) {
            // Check if the username attribute is empty
            if (empty($user->username)) {
                // Use the name to generate a slugified username
                $username = Str::slug($user->name, '');
                $i = 1;
                while (self::where('username', $username)->exists()) {
                    $username = Str::slug($user->name, '') . $i;
                    $i++;
                }
                $user->username = $username;
            }
        });

        // Listen for the created event of the model
        static::created(function ($user) {
            // Remove all roles
            $user->syncRoles([]);
            // Assign the default role
            $user->assignRole( config('wave.default_user_role', 'registered') );
        });

        static::created(function ($user) {
            try {
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

        static::retrieved(function ($user) {
            try {
                if (auth()->check() && auth()->id() === $user->id) {
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
                }
            } catch (\Exception $e) {
                report($e);
            }
        });
    }

    public function designs(): HasMany
    {
    return $this->hasMany(Design::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    public function designPurchases(): HasMany
    {
        return $this->hasMany(DesignPurchase::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class)
            ->with('items');
    }



}
