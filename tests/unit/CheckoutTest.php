<?php
declare(strict_types=1);
use App\Checkout;
use PHPUnit\Framework\TestCase;

final class CheckoutTest extends TestCase {

    protected $cart;

    function setUp(): void
    {
        $this->cart = new App\Checkout(null);
    }

    public function testCartEmptyAtStart(){
        //Ensure cart starts off as empty
        $this->assertEmpty($this->cart->getCartOrders());
    }

    public function testIsNumberOfItemsCorrect(){
        $this->cart->addCartItem(array('sku'=>"A",'quantity'=>100));
        $this->cart->addCartItem(array('sku'=>"B",'quantity'=>100));

        $this->assertEquals($this->cart->getCartItemsCount(),2);
    }

    public function testIsSpecialPricingAppliedCorrectly(){
        $this->cart->addCartItem(array('sku'=>"A",'quantity'=>3));
        $this->cart->calculateTotalCartPrice();

        $this->assertEquals($this->cart->getCartTotalCharges(),1.30);
    }

    public function testIsSpecialPricingCoPurchaseAppliedCorrectly(){
        $this->cart->addCartItem(array('sku'=>"A",'quantity'=>1));
        $this->cart->addCartItem(array('sku'=>"D",'quantity'=>1));
        $this->cart->calculateTotalCartPrice();
        
        $this->assertEquals($this->cart->getCartTotalCharges(),0.55);
    }

    public function testIsTotalPriceOfCartItemsCorrect(){
        $this->cart->addCartItem(array('sku'=>"A",'quantity'=>100));
        $this->cart->addCartItem(array('sku'=>"B",'quantity'=>100));
        $this->cart->addCartItem(array('sku'=>"C",'quantity'=>100));
        $this->cart->addCartItem(array('sku'=>"D",'quantity'=>100));
        $this->cart->addCartItem(array('sku'=>"E",'quantity'=>100));

        $this->cart->calculateTotalCartPrice();
        
        $this->assertEquals($this->cart->getCartTotalCharges(),92.6);
    }

}