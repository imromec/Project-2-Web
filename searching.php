<?php

$keyword = $_REQUEST['keyword'];
$keyword = '%'.$keyword.'%';
$client = new SoapClient('http://www.magento.com/api/v2_soap/?wsdl');
$session = $client->login('cat_tree', 'mypassword');

$complexFilter = array(
    'complex_filter' => array(
        array(
            'key' => 'name',
            'value' => array('key' => 'like', 'value' => $keyword)
        ),
		array(
	       'key' => 'status',
           'value' => array('key' => '=', 'value' => 1),
        ),
		array(
	      'key' => 'type',
         'value' => array('key' => '=', 'value' => 'simple')
        )
    )
);
$result = $client->catalogProductList($session, $complexFilter);
$result = json_decode(json_encode($result));

require_once '../app/Mage.php';
Mage::app();

if(count($result)!=0)
{
	$response['response'] = "success";
	for($i=0;$i<count($result);$i++)
	{
		$arr = array();
		$product_id = $result[$i]->product_id;
		$result2 = $client->catalogProductInfo($session, $product_id);
		$stdClass = json_decode(json_encode($result2));
		
		$result2 = $client->catalogProductAttributeMediaList($session, $product_id);
		$stdClass = json_decode(json_encode($result2));
		
		$model = Mage::getModel('catalog/product'); 
		$_product = $model->load($product_id); 
		$stocklevel = (int)Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($_product)->getQty();
		
		$result[$i]->quantity = "$stocklevel";	
		$result[$i]->image = $stdClass[0]->url;	
		
		$result[$i]->type = $stdClass->type_id;	
		$result[$i]->desc = $stdClass->short_description;
		$result[$i]->weight = $stdClass->weight;
		$result[$i]->status = $stdClass->status;
		$result[$i]->price = $stdClass->price;
	}
	$response['response_data'] = $result;
}
else{
$response['response'] = "failed";
$response['reason'] = "No item found!";
}

echo json_encode($response);
