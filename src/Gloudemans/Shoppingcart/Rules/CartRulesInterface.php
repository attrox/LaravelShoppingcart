<?php

namespace Gloudemans\Shoppingcart\Rules;

use Gloudemans\Shoppingcart\Cart;

interface CartRulesInterface
{
    public function isApplicable(Cart $cart);
    public function setRules($string);
    public function getResult(Cart $cart);
    public function setAction(\Closure $closure);
}