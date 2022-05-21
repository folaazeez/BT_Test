<?php
namespace App;

class SKUs{
    private $sku_Price  = array("A"=>0.50,"B"=>0.30,"C"=>0.20,"D"=>0.15,"E"=>0.05);
    private $sku_Special_Price  = array(
        "A"=>array("quantity"=>3,"price"=>1.30),
        "B"=>array("quantity"=>2,"price"=>0.45),
        "C"=>array(
            array("quantity"=>2,"price"=>0.38),
            array("quantity"=>3,"price"=>0.50)
        )
    );
    private $sku_Special_Price_CoPurchase  = array(
        "D"=>array("copurchase"=>"A","price"=>0.05)
    );
    
    public function getPrice($sku){
        return $this->sku_Price[$sku];
    }
    public function getSpecialPrices($sku){ 
        if(array_key_exists($sku,$this->sku_Special_Price)){
            return $this->sku_Special_Price[$sku];
        }else{
            return null;
        }
    }
    public function getSpecialPriceCoPurchase($sku){ 
        if(array_key_exists($sku,$this->sku_Special_Price_CoPurchase)){
            return $this->sku_Special_Price_CoPurchase[$sku];
        }else{
            return null;
        }
    }
}
//
class Checkout extends SKUs {
    //Class handles All Cart Computations
    private $orders=array();
    public $charges=array();

    function __construct($purchases){
        if(is_array($purchases)){
            $this->orders = $purchases;
        }
    }
    function calculateTotalCartPrice() : void {
        //
        $this->computePrice(); 
        $this->checkAndComputeSpecialPrices();
        $this->checkAndComputeSpecialPricesForCoPurchases();
    }

    public function getCartChargesPerItem() : array{
        //Return all the charges per item ordered
        return $this->charges;
    }
    public function addCartItem($item) : void{
        //Add an item to cart
        if(is_array($item)) array_push($this->orders,$item);
        $this->calculateTotalCartPrice();
    }    

    public function getCartTotalCharges() : float{
        //Return total charges of the purchases
        return array_sum($this->charges);
    }    

    public function getCartOrders() : array{
        return $this->orders;
    }

    public function getCartItemsCount() : int{
        return count($this->orders);
    }

    private function computePrice() : void{
        //Regular Price
        $this->charges = array_map(function($item){
            return $this->getPrice($item['sku']) * $item['quantity'];
        }, $this->orders);
    } 

    private function build_sorter($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }

    private function checkAndComputeSpecialPrices() : void{
        //Special Price
        foreach($this->orders as $index=>$orderItem){
            //special prices sorted in descending order
            $specialPs = $this->getSpecialPrices($orderItem['sku']);
            if($specialPs == null || $specialPs== "") continue;
                if(count($specialPs)>0){
                    //multiple or single special price
                    $is_multidimensional_array=0;
                    foreach($specialPs as $multidimensionalarray){
                        if (is_array($multidimensionalarray)==1) $is_multidimensional_array = 1;
                    }
                    //convert multi-dimensional array if it isn't
                    if($is_multidimensional_array!=1) $specialPs=array($specialPs);
                    
                    //compare the inner arrays and rearrange in natural order based on the key quantity
                    usort($specialPs, $this->build_sorter('quantity'));
                    //
                    //Do this to reversely arrange the array without maintaing the index. Giving priority to the largest valued special prices
                    rsort($specialPs);

                    //initilise temp
                    $tempComputingCharges = 0;
                    $leftOver=0;
                    //
                    $noofitems=$orderItem['quantity'];

                    foreach($specialPs as $a_specialprice){
                        //Eligibility check
                        if($noofitems >= $a_specialprice['quantity']){
                                            
                            //get the non-elligible items
                            $leftOver = $noofitems % $a_specialprice['quantity'];                
                            
                            //take out the leftover, compute the charges and save
                            $tempComputingCharges += (($noofitems - $leftOver)/$a_specialprice['quantity']) * $a_specialprice['price'];
            
                            //reset the no of items with the leftover
                            $noofitems = $leftOver;
                        }
                    }                    
                    //
                    //use regular pricing for whatever is left
                    $tempComputingCharges += $noofitems * $this->getPrice($orderItem['sku']);
                    //store the charges
                    if($tempComputingCharges) $this->charges[$index]=$tempComputingCharges;
                }
        }
    }
    //
    private function checkAndComputeSpecialPricesForCoPurchases() : void{
        //Special price based on co-purchase
        foreach($this->orders as $index=>$orderItem){
            $specialPs = $this->getSpecialPriceCoPurchase($orderItem['sku']);
            if($specialPs == null) continue;

            if(array_search($specialPs['copurchase'],array_column($this->orders,"sku"))!==false) $this->charges[$index] = $specialPs['price']*$orderItem['quantity'];
        }
    }
}
?>