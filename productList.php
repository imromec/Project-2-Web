<?php
$proxy = new SoapClient('http://localhost/magento/api/v2_soap/?wsdl'); // TODO : change url
$sessionId = $proxy->login('cats', 'mypassword'); // TODO : change login and pwd if necessary

require_once '../app/Mage.php';
Mage::app();

$complexFilter = array(
    'complex_filter' => array(
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

$result = $proxy->catalogProductList($sessionId, $complexFilter);

$result = json_decode(json_encode($result));
echo count($result);
if(count($result)!=0)
{
	$response['response'] = "success";
	for($i=0;$i<count($result);$i++)
	{
		$arr = array();
		$product_id = $result[$i]->product_id;
		
		$result2 = $proxy->catalogProductAttributeMediaList($sessionId, $product_id);
		$stdClass = json_decode(json_encode($result2));
		
		$model = Mage::getModel('catalog/product'); 
		$_product = $model->load($product_id); 
		$stocklevel = (int)Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($_product)->getQty();
		
		$result[$i]->quantity = "$stocklevel";	
		$result[$i]->image = $stdClass[0]->url;	
		
	}
	$response['response_data'] = $result;
}

echo json_encode($response);