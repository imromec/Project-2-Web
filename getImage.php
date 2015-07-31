
<?php

require_once('../app/Mage.php');
Mage::app();

$catId = $_REQUEST['cat_id'];
$type = $_REQUEST['type'];
$productId = $_REQUEST['product_id'];


if(isset($type))
{
	if($type == "category")
	{
		$image['cat_image'] = Mage::getModel('catalog/category')->load($catId)->getImageUrl();
	}
	else if($type == "product")
	{
		$product = Mage::getModel('catalog/product')->load($productId);	       
       	$image['product_image'] = $product->getImageUrl();		
	}
}


echo json_encode($image);


?>


