# Ecommerce Cart

[![Latest Version on Packagist](https://img.shields.io/packagist/v/itemvirtual/ecommerce-cart.svg?style=flat-square)](https://packagist.org/packages/itemvirtual/ecommerce-cart)
[![Total Downloads](https://img.shields.io/packagist/dt/itemvirtual/ecommerce-cart.svg?style=flat-square)](https://packagist.org/packages/itemvirtual/ecommerce-cart)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

Install the package via composer:

```bash
composer require itemvirtual/ecommerce-cart
```

Publish config (with `--force` option to update)

``` bash
php artisan vendor:publish --provider="Itemvirtual\EcommerceCart\EcommerceCartServiceProvider" --tag=config
```

You can change the `config` values for `cart_session_name` and `taxes_included`:

```dotenv
#ECOMMERCE_CART_SESSION_NAME="ecommerceCart"
ECOMMERCE_TAXES_INCLUDED=true
```

Add `EcommerceCart` to your `config/app` aliases array

```php
'EcommerceCart' => Itemvirtual\EcommerceCart\Facades\EcommerceCart::class,
```

## Usage

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

#### Set tax

```php
EcommerceCart::setTax($float);
```

If you save the tax_id value, you can change it with `updateCartItemsDataValue()`

```php
EcommerceCart::updateCartItemsDataValue($key, $value);
// EcommerceCart::updateCartItemsDataValue('tax_id', 1);
```

#### Set apply tax (p.ex if not European)

```php
EcommerceCart::setApplyTax($boolean);
```

#### Get Totals

```php
EcommerceCart::getTotal();
EcommerceCart::getSubtotal();
// return taxes array
EcommerceCart::getTaxTotals();
// return total tax amount
EcommerceCart::getTaxTotalValue();

// Get total without shipping
EcommerceCart::getTotalWithoutShipping();
```

#### Shipping

Add Global shipping

```php
$ShippingData = [
    'id' => $Shipping->id,
    'title' => $Shipping->title,
    'value' => $Shipping->value,
    'free_from' => $Shipping->free_from,
];
EcommerceCart::setShipping($ShippingData);
```

Remove Shipping data

```php
EcommerceCart::removeShipping();
```

Get Shipping data

```php
EcommerceCart::getShipping();

// Returns an object with 4 or 5 properties
{
  +"id": 3
  +"title": "Europe"
  +"value": 10
  +"free_from": 200
  +"free": true
}
```

#### Get Cart Items

```php
$cartItems = EcommerceCart::getItems();

EcommerceCart::hasItems();
EcommerceCart::countItems();

```

#### Destroy Cart

```php
EcommerceCart::destroyCart();
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

- [Sergio](https://github.com/sergio-item)
- [Itemvirtual](https://github.com/itemvirtual)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.