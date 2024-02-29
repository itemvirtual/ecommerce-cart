<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cart session name
    |--------------------------------------------------------------------------
    |
    | Nothing yet
    |
    */
    'cart_session_name' => env('ECOMMERCE_CART_SESSION_NAME', 'ecommerceCart'),


    /*
    |--------------------------------------------------------------------------
    | Taxes included
    |--------------------------------------------------------------------------
    |
    | Nothing yet
    |
    */
    'taxes_included' => env('ECOMMERCE_TAXES_INCLUDED', false),


    /*
    |--------------------------------------------------------------------------
    | Calculate Totals
    |--------------------------------------------------------------------------
    |
    | Allow EcommerceCart to calculate cart totals.
    | You can use your own Service, get the data saved in the cart with EcommerceCart::getItems()
    | and calculate your totals according to your needs
    |
    */
    'calculate_totals' => env('ECOMMERCE_CALCULATE_TOTALS', false),

];