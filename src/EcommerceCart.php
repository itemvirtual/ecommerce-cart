<?php

namespace Itemvirtual\EcommerceCart;

use Illuminate\Support\Str;
use Itemvirtual\EcommerceCart\Services\CartItem;
use Itemvirtual\EcommerceCart\Traits\CalculateTotals;

class EcommerceCart
{
    use CalculateTotals;

    const APPLY_TAX = true;

    public function getCartId()
    {
        $cartData = $this->getCartData();
        if (array_key_exists('cart_uuid', $cartData)) {
            return $cartData['cart_uuid'];
        }

        $cartData['cart_uuid'] = Str::uuid()->toString();
        $this->setCartData($cartData);
        return $cartData['cart_uuid'];
    }

    public function hasItems()
    {
        $cartData = $this->getCartData();
        return array_key_exists('items', $cartData) && count($cartData['items']);
    }

    public function hasItem($itemId)
    {
        return $this->checkIdInCartItems($itemId);
    }

    public function countItems()
    {
        $cartData = $this->getCartData();
        if (array_key_exists('items', $cartData) && count($cartData['items'])) {
            return count($cartData['items']);
        }
        return 0;
    }

    public function getItems()
    {
        $cartData = $this->getCartData();
        if ($this->hasItems()) {
            return collect($cartData['items']);
        }
        return collect();
    }

    public function setTax($value)
    {
        $cartData = $this->getCartData();

        if ($this->hasItems()) {
            foreach ($cartData['items'] as $k_item => $item) {
                $cartData['items'][$k_item]->tax = floatval($value);
            }

            $this->setCartData($cartData);
        }

        // Recalculate cart items totals
        $this->recalculateCartItemTotals();
    }

    public function setApplyTax(bool $value)
    {
        $cartData = $this->getCartData();
        $cartData['apply_tax'] = $value;
        $this->setCartData($cartData);

        // Recalculate cart items totals
        $this->recalculateCartItemTotals();
    }

    public function destroyCart()
    {
        return session()->forget(config('ecommerce-cart.cart_session_name'));
    }

    private function destroyCartIfEmpty()
    {
        if (!$this->hasItems()) {
            $this->destroyCart();
        }
    }

    /* *********************************************************** */

    /**
     * Add item to cart if not exists
     * @param array $cartDataToAdd
     * @return void
     */
    public function addToCart(array $cartDataToAdd)
    {
        $alreadyInCart = $this->checkIdInCartItems($cartDataToAdd['id']);

        // If item is already in cart, remove it
        if ($alreadyInCart) {
            $this->removeCartItem($cartDataToAdd['id']);
        }

        // If no item amount, remove it
        if (array_key_exists('amount', $cartDataToAdd) && intval($cartDataToAdd['amount'] <= 0)) {
            $this->removeCartItem($cartDataToAdd['id']);
        } else {
            // create cart item
            $this->createCartItem($cartDataToAdd);
        }
    }

    /**
     * Create new CartItem
     * @param array $cartDataToAdd
     * @return void
     */
    private function createCartItem(array $cartDataToAdd)
    {
        // Validate required fields in ecommerceCart
        $this->validateCartRequiredData();

        $cartData = $this->getCartData();

        $CartItem = new CartItem($cartDataToAdd, $cartData['apply_tax']);

        array_push($cartData['items'], $CartItem);

        $this->setCartData($cartData);
    }

    public function incrementCartItem($itemId)
    {
        $cartData = $this->getCartData();

        if ($this->hasItems()) {
            foreach ($cartData['items'] as $item) {
                if ($item->id == $itemId) {
                    $item->setAmount(++$item->amount);
                    $item->setApplyTax($cartData['apply_tax']);
                    $item->calculateCartItemTotals();
                }
            }
        }

        $this->setCartData($cartData);
    }

    public function decrementCartItem($itemId)
    {
        $cartData = $this->getCartData();

        if ($this->hasItems()) {
            foreach ($cartData['items'] as $k_item => $item) {
                if ($item->id == $itemId) {
                    $item->setAmount(--$item->amount);
                    $item->setApplyTax($cartData['apply_tax']);
                    $item->calculateCartItemTotals();
                }
                // if not amount, remove item from the cart
                if ($item->amount <= 0) {
                    unset($cartData['items'][$k_item]);
                }
            }
        }

        $this->setCartData($cartData);

        // If cart is empty, destroy to remove shipping and other stuff
        $this->destroyCartIfEmpty();

    }

    public function removeCartItem($itemId)
    {
        $cartData = $this->getCartData();

        if ($this->hasItems()) {
            foreach ($cartData['items'] as $k_item => $item) {
                if ($item->id == $itemId) {
                    unset($cartData['items'][$k_item]);
                }
            }

            $this->setCartData($cartData);
        }

        // If cart is empty, destroy to remove shipping and other stuff
        $this->destroyCartIfEmpty();
    }

    public function updateCartItem($cartItem)
    {
        $cartData = $this->getCartData();

        if ($this->hasItems()) {
            foreach ($cartData['items'] as $k_item => $item) {
                if ($item->id == $cartItem->id) {
                    $cartData['items'][$k_item] = $cartItem;
                }
            }

            $this->setCartData($cartData);
        }
    }

    public function updateCartItemsDataValue($key, $value)
    {
        $cartData = $this->getCartData();

        if ($this->hasItems()) {
            foreach ($cartData['items'] as $k_item => $item) {
                $cartData['items'][$k_item]->data[$key] = $value;
            }

            $this->setCartData($cartData);
        }
    }

    public function checkIdInCartItems($itemId)
    {
        $cartData = $this->getCartData();

        if ($this->hasItems()) {
            foreach ($cartData['items'] as $item) {
                if ($item->id == $itemId) {
                    return true;
                }
            }
        }
        return false;
    }

    /* *********************************************************** */

    private function addCartData($key, $value)
    {
        $cartData = $this->getCartData();
        $cartData[$key] = $value;
        $this->setCartData($cartData);
    }

    private function removeCartData($key)
    {
        $cartData = $this->getCartData();
        if (array_key_exists($key, $cartData)) {
            unset($cartData[$key]);
            $this->setCartData($cartData);
        }
    }

    public function getCartDataByKey($key)
    {
        $cartData = $this->getCartData();
        if (array_key_exists($key, $cartData)) {
            return $cartData[$key];
        }
        return null;
    }

    /* *********************************************************** */

    public function recalculateCartItemTotals()
    {
        $cartData = $this->getCartData();
        if (array_key_exists('items', $cartData)) {
            foreach ($cartData['items'] as $item) {
                $item->setApplyTax($cartData['apply_tax']);
                $item->calculateCartItemTotals();
            }
            $this->setCartData($cartData);
        }
    }

    /* *********************************************************** */

    public function getCartData()
    {
        return session(config('ecommerce-cart.cart_session_name'), []);
    }

    private function setCartData($cartData)
    {
        session([config('ecommerce-cart.cart_session_name') => $cartData]);
    }

    public function setCustomCartData($key, $value)
    {
        $this->validateCustomCartData($key);
        $this->addCartData($key, $value);
    }

    public function removeCustomCartData($key)
    {
        $this->validateCustomCartData($key);
        $this->removeCartData($key);
    }

    /* *********************************************************** SHIPPING */

    public function hasShipping()
    {
        $cartData = $this->getCartData();
        return array_key_exists('shipping', $cartData) && count($cartData['shipping']);
    }

    public function setShipping($shipping)
    {
        $this->validateShippingRequiredData($shipping);
        $this->addCartData('shipping', $shipping);
    }

    public function removeShipping()
    {
        $this->removeCartData('shipping');
    }

    public function getShipping()
    {
        $cartData = $this->getCartData();

        if ($this->hasShipping()) {
            $shipping = $cartData['shipping'];
            if (!is_null($shipping['free_from'])) {
                if (floatval($this->getTotalWithoutShipping()) >= floatval($shipping['free_from'])) {
                    $shipping['free'] = true;
                }
            }
            return (object)$shipping;
        }
        return null;
    }

    /* *********************************************************** COUPON */

    public function hasCoupon()
    {
    }

    public function getCoupon()
    {
    }

    public function checkCoupon($couponCode)
    {
    }

    private function checkValidCoupon($Coupon)
    {
    }

    /* *********************************************************** TOTALS */

    public function getSubtotal()
    {
        $cartData = $this->getCartData();
        if ($this->hasItems()) {
            return $this->calculateSubtotal($cartData);
        }
        return 0;
    }

    public function getCouponTotal()
    {
    }

    public function getShippingTotal()
    {
        $shippingTotal = 0;
        if ($this->hasShipping()) {
            $shipping = $this->getShipping();
            if (is_null($shipping->free_from)) {
                return $shipping->value;
            }
            if (floatval($this->getTotalWithoutShipping()) < floatval($shipping->free_from)) {
                return $shipping->value;
            }
        }
        return $shippingTotal;
    }

    public function getTaxTotals()
    {
        $cartData = $this->getCartData();
        $taxesTotals = [];

        if (!array_key_exists('apply_tax', $cartData)) {
            $cartData['apply_tax'] = self::APPLY_TAX;
        }

        if ($this->hasItems()) {
            $taxesTotals = $this->calculateTaxTotalsGroupByTax($cartData);
        }
        return $taxesTotals;
    }

    public function getTaxTotalValue()
    {
        $taxTotal = 0;
        foreach ($this->getTaxTotals() as $tax) {
            $taxTotal += $tax;
        }
        return $taxTotal;
    }

    public function getTotalWithoutShipping()
    {
        $cartData = $this->getCartData();
        if ($this->hasItems()) {
            return $this->calculateTotal($cartData);
        }
        return 0;
    }

    public function getTotal()
    {
        $cartData = $this->getCartData();
        if ($this->hasItems()) {
            return $this->calculateTotal($cartData) + $this->getShippingTotal();
        }
        return 0;
    }

    /* *********************************************************** CUSTOMER */
    /*
        public function setCustomer($customerId)
        {
        }

        public function getCustomer()
        {
        }

        public function removeCustomer()
        {
        }

        public function setCustomerData($customerData)
        {
        }

        public function getCustomerData()
        {
        }

        public function removeCustomerData()
        {
        }

        public function setCustomerShipping($customerShippingId)
        {
        }

        public function getCustomerShipping()
        {
        }

        public function removeCustomerShipping()
        {
        }

        public function setCustomerShippingData($customerShippingData)
        {
        }

        public function getCustomerShippingData()
        {
        }

        public function removeCustomerShippingData()
        {
        }
    */

    /* *********************************************************** TRANSACTION */

//    public function getCartForTransaction(){}

//    public function getCartTotalsForTransaction(){}

    /* *********************************************************** VALIDATIONS */

    /**
     *
     */
    private function validateCartRequiredData()
    {
        $cartData = $this->getCartData();

        if (!array_key_exists('cart_uuid', $cartData)) {
            $cartData['cart_uuid'] = Str::uuid()->toString();
        }

        if (!array_key_exists('items', $cartData)) {
            $cartData['items'] = [];
        }

        if (!array_key_exists('apply_tax', $cartData)) {
            $cartData['apply_tax'] = self::APPLY_TAX;
        }

        $this->setCartData($cartData);
    }

    /**
     * @param $key
     * @throws \Exception
     */
    private function validateCustomCartData($key)
    {
        $reservedFields = ['cart_uuid', 'items', 'apply_tax', 'shipping'];
        if (in_array($key, $reservedFields)) {
            throw new \Exception($key . ' is a reserved field name in the ecommerceCart.');
        }
    }

    /**
     * @param $shippingData
     * @throws \Exception
     */
    private function validateShippingRequiredData($shippingData)
    {
        $requiredFields = ['id', 'title', 'value', 'free_from'];
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $shippingData)) {
                throw new \Exception($field . ' is a required field in shipping data. [' . implode(', ', $requiredFields) . '] are required');
            }
        }
    }

}
