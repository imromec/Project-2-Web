<?php
ini_set('max_execution_time', 300);

$proxy = new SoapClient('http://www.magento.com/api/v2_soap/?wsdl'); // TODO : change url
$sessionId = $proxy->login('cat_tree', 'mypassword'); // TODO : change login and pwd if necessary

require_once '../app/Mage.php';
Mage::app();

$start = $_REQUEST['start'];
$limit = 25;
$collection = Mage::getModel('catalog/product')->getCollection();
$collection->addAttributeToSelect('*');;
$collection->getSelect()
   ->limit($limit,$start);
$response['response'] = "success";
$all = array();
foreach($collection as $product)
{
	$result['product_id'] = $product->getId();
	$result['name'] = $product->getName();
	$result['price'] = $product->getPrice();
	$result['description'] = $product->getDescription();
	//$category = array();
	$cat = array();
	$product = Mage::getModel('catalog/product')->load($result['product_id']);
	$cats = $product->getCategoryIds();
	
	foreach ($cats as $category_id) {
	$_cat = Mage::getModel('catalog/category')->load($category_id) ;
		$cat[] = $_cat->getId();
	
	} 
	//array_push($category, $cat);
	$result['categories'] = $cat;
	$result['status'] = $product->getStatus();
	$result['Large_image'] = $product->getImageUrl();
	$result['Small_image'] = $product->getSmallImageUrl();
	$result['Thumbnail_image'] = $product->getThumbnailUrl();
	$qtyStock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
	$result['quantity'] = "$qtyStock";
	
	array_push($all, $result);
}
	
$response['response_data'] = $all;


echo json_encode($response);