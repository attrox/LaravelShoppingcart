<?php

namespace Gloudemans\Shoppingcart\Rules;

use Gloudemans\Shoppingcart\Cart;

abstract class CartRulesAbstract implements CartRulesInterface
{
    protected $apply_to = array(); // Search cart for this condition and apply to that particular item
    protected $rules = array();
    protected $closure;

    public function __construct($rules = '', $apply_to = array(), $closure = null)
    {
        $this->setRules($rules);
        $this->apply_to = $apply_to;
        $this->closure = $closure;
    }

    public function setAction(\Closure $closure)
    {
        if (!$closure instanceof \Closure) throw new \Exception;
        $this->closure = $closure;
    }

    public function setRules($string)
    {
        $rules = explode(' ', $string);
        if (sizeof($rules) !== 3) throw new \Exception;
        $this->rules = $rules;
    }

    public function isApplicable(Cart $cart)
    {
        $operator = $this->rules[1];
        $compare_base = $this->rules[2];

        if (empty($this->apply_to)) {
            // Apply the rules to the whole cart
            if (!method_exists($cart, $this->rules[0])) throw new \Exception;

            $method = $this->rules[0];
            $to_compare = $cart->{$method}();
            if (!$to_compare) return false;

            try {
                if (is_numeric($compare_base) && is_numeric($to_compare)) {
                    return eval("return {$to_compare} {$operator} {$compare_base};");
                }else {
                    return eval("return '{$to_compare}' {$operator} '{$compare_base}';");
                }
            } catch (\Exception $e) {
                throw new \Exception;
            }
        }

        if (!$items = $cart->search($this->apply_to)) return false;
        if (sizeof($items) > 1) throw new \Exception('Rule is applicable to the whole cart or just 1 particular item');

        if (!$to_compare = array_get($cart->get($items[0])->toArray(), $this->rules[0])) return false;

        try {
            if (is_numeric($compare_base) && is_numeric($to_compare)) {
                return eval("return {$to_compare} {$operator} {$compare_base};");
            }else {
                return eval("return '{$to_compare}' {$operator} '{$compare_base}';");
            }
        } catch (\Exception $e) {
            throw new \Exception;
        }
    }
}