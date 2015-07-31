<?php

require_once '../app/Mage.php'; 
umask(0);  
Mage::init('default');

$id = $_REQUEST['id'];
$qnt = $_REQUEST['qnt'];
Mage::getSingleton('core/session', array('name' => 'frontend'));  

// Get customer session
$session = Mage::getSingleton('customer/session'); 

// Get cart instance
$cart = Mage::getSingleton('checkout/cart'); 
$cart->init();

// Add a product (simple); id:12,  qty: 3 
$cart->addProduct($id, $qnt);

// Add a product with custom options
$productInstance = Mage::getModel('catalog/product')->load($productId);
$param = array(
    'product' => $productInstance->getId(),
    'qty' => 1,
    'options' => array(
        234 => 'A value'  // Custom option with id: 234
    )
);
$request = new Varien_Object();
$request->setData($param);
$cart->addProduct($productInstance, $request);

// Set shipping method
$quote = $cart->getQuote();
$shippingAddress = $quote->getShippingAddress();
$shippingAddress->setShippingMethod('flatrate_flatrate')->save();               

// update session
$session->setCartWasUpdated(true);

// save the cart
$cart->save(); 

print_r($cart);