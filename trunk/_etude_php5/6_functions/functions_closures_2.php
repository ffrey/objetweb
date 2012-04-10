<?php
$var = function () { return 'hello'; };
var_dump($var() );
echo "====================\n";
// http://fr.php.net/manual/en/functions.anonymous.php
// A basic shopping cart which contains a list of added products
// and the quantity of each product. Includes a method which
// calculates the total price of the items in the cart using a
// closure as a callback.
class Cart
{
    const PRICE_BUTTER  = 1.00;
    const PRICE_MILK    = 3.00;
    const PRICE_EGGS    = 5.00;

    protected $products = array();
    
    public function add($product, $quantity)
    {
        $this->products[$product] = $quantity;
    }
    
    public function getQuantity($product)
    {
        return isset($this->products[$product]) ? $this->products[$product] :
               FALSE;
    }
    
    public function getTotal($tax)
    {
        // global $total, $tax;
        $total = 0.00;
        
        $callback =
            function ($quantity, $product) use ($tax, &$total)
            {
                // global $tax, $total;
                $pricePerItem = constant(__CLASS__ . "::PRICE_" .
                    strtoupper($product));
                    echo "before product $product : total is $total and tax is $tax\n";
                $total += ($pricePerItem * $quantity) * ($tax + 1.0);
                    echo "after : $total \n";
            };
        array_walk($this->products, $callback);
        return round($total, 2);
    }
}

$my_cart = new Cart;

// Add some items to the cart
$my_cart->add('butter', 1);
$my_cart->add('milk', 3);
$my_cart->add('eggs', 6);

// Print the total with a 5% sales tax.
// you need to assign $tax here if you want to use it as a global from closure ! $tax = 0.05;
print $my_cart->getTotal(0.05) . "\n";
// The result is 54.29
?>