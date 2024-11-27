<?php

/*
 * These are some default authentication settings
 */
return [
    'redirect_after_auth' => '/dashboard',
    'registration_show_password_same_screen' => true,
    'registration_include_name_field' => true,
    'registration_require_email_verification' => true,
    'enable_branding' => false,
    'dev_mode' => true,
    'enable_2fa' => false, // Enable or disable 2FA functionality globally
    'login_show_social_providers' => true,
    'social_providers_location' => 'bottom',
];
