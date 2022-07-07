# Ecommerce Cart

[![Latest Version on Packagist](https://img.shields.io/packagist/v/itemvirtual/ecommerce-cart.svg?style=flat-square)](https://packagist.org/packages/itemvirtual/ecommerce-cart)
[![Total Downloads](https://img.shields.io/packagist/dt/itemvirtual/ecommerce-cart.svg?style=flat-square)](https://packagist.org/packages/itemvirtual/ecommerce-cart)


This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require itemvirtual/ecommerce-cart
```

## Usage

Publish config (with `--force` option to update)

``` bash
php artisan vendor:publish --provider="Itemvirtual\EcommerceCart\EcommerceCartServiceProvider" --tag=config
```

Use the `EcommerceCart` Facade

```php
use Itemvirtual\EcommerceCart\Facades\EcommerceCart;
```

#### Add products to cart

```php
EcommerceCart::addToCart([
    'id' => $Product->id,
    'title' => $Product->name,
    'price' => floatval($Product->price),
    'tax' => 21.0,
    'amount' => 1,
    'data' => [ // add your custom data here
        'tax_id' => 1
    ]
]);
```

#### Increment or decrement amount

```php
EcommerceCart::incrementCartItem($Product->id);
EcommerceCart::decrementCartItem($Product->id);
```

#### Remove from cart

```php
EcommerceCart::removeCartItem($Product->id);
```

#### Set apply tax (p.ex if not European)

```php
EcommerceCart::setApplyTax($boolean);
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [Sergio](https://github.com/sergio-item)
-   [Itemvirtual](https://github.com/itemvirtual)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.