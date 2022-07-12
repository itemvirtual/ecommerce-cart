<?php

namespace Itemvirtual\EcommerceCart\Traits;

trait CalculateTotals
{
    /**
     * @param $cartData
     * @return float
     */
    public function calculateSubtotal($cartData)
    {
        $subtotal = 0;
        foreach ($cartData['items'] as $item) {
            $subtotal += $item->getTotals()['subtotal'];
        }
        return $subtotal;
    }

    /**
     * @param $cartData
     * @return float
     */
    public function calculateTotal($cartData)
    {
        $subtotal = 0;
        foreach ($cartData['items'] as $item) {
            $subtotal += $item->getTotals()['total'];
        }
        return $subtotal;
    }

    /**
     * Total amount of Taxes group by tax id
     *
     * @param $cartData
     * @return array
     */
    public function calculateTaxTotalsGroupByTax($cartData)
    {
        $taxesTotals = [];

        foreach ($cartData['items'] as $item) {
            if (!array_key_exists((string)$item->tax, $taxesTotals)) {
                $taxesTotals[(string)$item->tax] = 0;
            }
            $taxesTotals[(string)$item->tax] += $item->getTotals()['tax'];
        }

        return $taxesTotals;
    }
}