<?php
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);

require_once './src/Checkout.php';
//use App\Checkout;


/* $array[0] = array('key_a' => 'z', 'key_b' => 7);
$array[1] = array('key_a' => 'x', 'key_b' => 1.2);
$array[2] = array('key_a' => 'y', 'key_b' => 5);
$array[3] = array('key_a' => 'y', 'key_b' => 4);


print_r($array);

function build_sorter($key) {
    return function ($a, $b) use ($key) {
        return strnatcmp($a[$key], $b[$key]);
    };
}

usort($array, build_sorter('key_b'));

echo "<br><br>";
print_r($array); */

//exit;

$a = array(array("sku"=>"A","quantity"=>10));
echo array_search("C",array_column($a,"sku"));

session_start();
//
if($_POST['add']){
    //Add item
    $_SESSION["store_checkout_items"][]=array('sku'=>$_POST['sku'],'quantity'=>$_POST['quantity']);
    header("Location:".$_SERVER['PHP_SELF']);
}

if($_POST['clear']){
    //Clear Cart
    $_SESSION["store_checkout_items"]=array();
    header("Location:".$_SERVER['PHP_SELF']);
}

if($_GET['delete']!=""){
    //Remove cart item
    if(count($_SESSION["store_checkout_items"])==1){
        $_SESSION["store_checkout_items"]=array();
    }else{
        $temp = array_filter($_SESSION["store_checkout_items"],function($item,$index){
            return $index != $_GET['delete'];
        },ARRAY_FILTER_USE_BOTH);

        $_SESSION["store_checkout_items"] = $temp;                
    }
    header("Location:".$_SERVER['PHP_SELF']);
}



$obj_checkout= new App\Checkout($_SESSION["store_checkout_items"]);
$obj_checkout->calculateTotalCartPrice();
$result = $obj_checkout->getCartChargesPerItem();
$gTotal = $obj_checkout->getCartTotalCharges();

//
//If there are ordered items build the display elements
if($_SESSION["store_checkout_items"]){
    foreach($_SESSION["store_checkout_items"] as $key=>$item){
        $cart.= '<tr><td>'.$item['sku'].'</td><td class="text-center">'.$item['quantity'].'</td><td class="text-end">'.$result[$key].'</td><td><a href="?delete='.$key.'">Delete</a></td></tr>';
    }
    if($gTotal) $footer ='<tfoot><tr><td class="fs-3 fw-bold">Total Price</td><td class="text-end fs-3 fw-bold" colspan="2">'.$gTotal.'</td><td></td></tr></tfoot>';
}

include './gui/gui_index.php';