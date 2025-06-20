<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Selcom Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Selcom payment gateway.
    | You can override these values in your .env file.
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Live Mode
    |--------------------------------------------------------------------------
    |
    | This value determines if the application is in live mode or test mode.
    | Set this to true for production environment.
    |
    */

    'live' => env('SELCOM_IS_LIVE', false),

    /*
    |--------------------------------------------------------------------------
    | Vendor ID
    |--------------------------------------------------------------------------
    |
    | This is your Selcom vendor ID.
    |
    */
    'vendor_id' => env('SELCOM_VENDOR_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | This is your Selcom API key.
    |
    */
    'api_key' => env('SELCOM_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | API Secret
    |--------------------------------------------------------------------------
    |
    | This is your Selcom API secret.
    |
    */
    'api_secret' => env('SELCOM_API_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | This is the base URL for the Selcom API.
    |
    */
    'api_url' => env('SELCOM_API_URL', 'https://apigw.selcommobile.com/v1/'),

    /*
    |--------------------------------------------------------------------------
    | Redirect URL
    |--------------------------------------------------------------------------
    |
    | This is the URL where the user will be redirected after a successful payment.
    |
    */
    'redirect_url' => env('SELCOM_REDIRECT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Cancel URL
    |--------------------------------------------------------------------------
    |
    | This is the URL where the user will be redirected if they cancel the payment.
    |
    */
    'cancel_url' => env('SELCOM_CANCEL_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook URL
    |--------------------------------------------------------------------------
    |
    | This is the URL where Selcom will send payment notifications.
    |
    */
    'webhook_url' => env('SELCOM_WEBHOOK_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Payment Expiry Time
    |--------------------------------------------------------------------------
    |
    | This is the time in minutes after which the payment will expire.
    |
    */
    'expiry' => env('SELCOM_PAYMENT_EXPIRY', 30),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Colors
    |--------------------------------------------------------------------------
    |
    | These are the colors used in the payment gateway.
    |
    */
    'colors' => [
        'header' => env('SELCOM_HEADER_COLOR', '#000000'),
        'link' => env('SELCOM_LINK_COLOR', '#0000FF'),
        'button' => env('SELCOM_BUTTON_COLOR', '#008000'),
    ],
];
