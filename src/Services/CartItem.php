<?php

namespace Itemvirtual\EcommerceCart\Services;

use Illuminate\Support\Str;

#[AllowDynamicProperties]
class CartItem
{
    public $id;
    public $title;
    public $price;
    public $tax;
    public $amount;
    private $totals;
    public $discount;
    public $coupon;
    public $shipping;
    public $data = [];
    private $applyTax;
    public $isValidTax;
    private $requiredFields = ['id', 'title', 'price', 'tax', 'amount'];

    /**
     * @param array $cartItemData
     * @param bool $applyTax
     * @throws \Exception
     */
    public function __construct(array $cartItemData, bool $applyTax)
    {
        $this->validateCartItemRequiredData($cartItemData);

        $this->applyTax = $applyTax;

        foreach ($cartItemData as $key => $value) {
            $this->createProperty($key, $value);
        }

        $this->isValidTax = $this->tax && floatval($this->tax) >= 0;

        $this->calculateCartItemTotals();
    }

    /**
     * Validate required fields in CartItem
     *
     * @param $cartItemData
     * @return void
     * @throws \Exception
     */
    private function validateCartItemRequiredData($cartItemData)
    {
        foreach ($this->requiredFields as $field) {
            if (!array_key_exists($field, $cartItemData)) {
                throw new \Exception($field . ' is a required field in the ecommerceCart. [' . implode(', ', $this->requiredFields) . '] are required');
            }
        }

        if (array_key_exists('data', $cartItemData) && !is_array($cartItemData['data'])) {
            throw new \Exception('data must be an array.');
        }

        if (array_key_exists('totals', $cartItemData)) {
            throw new \Exception('totals is a reserved field name in the ecommerceCart.');
        }
    }

    /**
     * Calculate CartItem Totals
     *
     * @return void
     */
    public function calculateCartItemTotals()
    {
        $this->totals = [
            'subtotal' => $this->calculateItemSubtotal(),
            'tax' => $this->calculateItemTaxTotal(),
            'total' => $this->calculateItemTotal(),
        ];
    }

    /**
     * Get CartItem Totals
     *
     * @return void
     */
    public function getCartItemTotals()
    {
        $this->calculateCartItemTotals();
        return $this->totals;
    }

    /* *************************************************************************************************** Calculate totals */

    /**
     * @return float
     */
    public function calculateItemSubtotal()
    {
        $itemSubtotal = 0;
        if (config('ecommerce-cart.taxes_included') && $this->isValidTax) {
            $itemSubtotal = $this->amount * floatval($this->price / (1 + ($this->tax / 100)));
        } else {
            $itemSubtotal = $this->amount * floatval($this->price);
        }

        return round($itemSubtotal, 2);
    }

    /**
     * Total amount of Tax for an item
     *
     * @return float
     */
    public function calculateItemTaxTotal()
    {
        $taxesItemTotals = 0;

        if ($this->applyTax && $this->isValidTax) {
            if (config('ecommerce-cart.taxes_included')) {
                $taxesItemTotals = $this->amount * (floatval($this->price) - floatval($this->price / (1 + ($this->tax / 100))));
            } else {
                $taxesItemTotals = ($this->amount * floatval($this->price)) * ($this->tax / 100);
            }
        }

        return round($taxesItemTotals, 2);
    }

    /**
     *
     * @return float
     */
    public function calculateItemTotal()
    {
        $itemTotal = 0;

        if ($this->applyTax && $this->isValidTax) {
            if (config('ecommerce-cart.taxes_included')) {
                $itemTotal = $this->amount * floatval($this->price);
            } else {
                $itemTotal = ($this->amount * floatval($this->price)) * (1 + ($this->tax / 100));
            }
        } else {
            $itemTotal = $this->amount * floatval($this->price);
        }

        return round($itemTotal, 2);
    }

    /* *************************************************************************************************** Get item totals */

    public function getItemSubtotal()
    {
        return $this->totals['subtotal'];
    }

    public function getItemTax()
    {
        return $this->totals['tax'];
    }

    public function getItemTotal()
    {
        return $this->totals['total'];
    }

    /* *************************************************************************************************** Getters and Setters */

    /**
     * Set properties of CartItem
     *
     * @param $name
     * @param $value
     * @return void
     */
    public function createProperty($name, $value)
    {
        $name = Str::camel($name);
        $this->{$name} = $value;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param mixed $tax
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * @param mixed $totals
     * @return CartItem
     */
    public function setTotals($totals)
    {
        $this->totals = $totals;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getApplyTax()
    {
        return $this->applyTax;
    }

    /**
     * @param mixed $applyTax
     */
    public function setApplyTax($applyTax)
    {
        $this->applyTax = $applyTax;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     * @return CartItem
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @param mixed $coupon
     * @return CartItem
     */
    public function setCoupon($coupon)
    {
        $this->coupon = $coupon;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param mixed $shipping
     * @return CartItem
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
        return $this;
    }

}
