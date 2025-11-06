<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v3 Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour Google reCAPTCHA v3 (invisible captcha)
    | https://www.google.com/recaptcha/admin
    |
    */

    'site_key' => env('RECAPTCHA_SITE_KEY', ''),

    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Score minimum (0.0 - 1.0)
    |--------------------------------------------------------------------------
    |
    | Score minimum requis pour valider le captcha.
    | 0.0 = Bot probable, 1.0 = Humain probable
    | Recommandé: 0.5
    |
    */

    'min_score' => env('RECAPTCHA_MIN_SCORE', 0.5),

];
