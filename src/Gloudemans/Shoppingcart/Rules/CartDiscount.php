<?php

namespace Gloudemans\Shoppingcart\Rules;

use Gloudemans\Shoppingcart\Cart;
use Gloudemans\Shoppingcart\CartRowCollection;

class CartDiscount extends CartRulesAbstract
{
    public function getResult(Cart $cart)
    {
        if (!$this->isApplicable($cart)) return null;

        $closure = $this->closure;
        if (!$closure instanceof \Closure) throw new \Exception;

        $qty = 1;
        $price = $closure($cart);
        $newRow = new CartRowCollection([
            'rowid' => null,
            'id' => null,
            'name' => 'Shopping Cart Discount',
            'qty' => 1,
            'price' => $price,
            'options' => [],
            'subtotal' => $qty * $price
        ], null, null);

        return $newRow;
    }
}