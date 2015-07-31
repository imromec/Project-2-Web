<?php
$product_id = $_REQUEST['product_id'];

$proxy = new SoapClient('http://www.magento.com/api/v2_soap/?wsdl'); // TODO : change url
$sessionId = $proxy->login('cat_tree', 'mypassword'); // TODO : change login and pwd if necessary


//Get image of product
$response = array();
$result2 = $proxy->catalogProductAttributeMediaList($sessionId, $product_id);

foreach($result2 as &$blog) {
        $blog     = get_object_vars($blog);
        $url    = $blog['url'];
}

//get qnt of product

require_once '../app/Mage.php';
Mage::app();
$model = Mage::getModel('catalog/product'); 
$_product = $model->load($product_id); 
$stocklevel = (int)Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($_product)->getQty();

$result = $proxy->catalogProductInfo($sessionId, $product_id);
$result = json_decode(json_encode($result));

//add to result array
$result->quantity = "$stocklevel";
$result->image = $url;
$response['response'] = "success";
$response['response_data'] = $result;

echo json_encode($response);