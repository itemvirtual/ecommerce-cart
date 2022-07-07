<?php

namespace Itemvirtual\EcommerceCart\Facades;

use Illuminate\Support\Facades\Facade;


class EcommerceCart extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ecommerce-cart';
    }
}
