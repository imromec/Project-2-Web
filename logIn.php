<?php 

$email = $_REQUEST['email'];
$password = $_REQUEST['password'];


require_once ('../app/Mage.php');
//umask(0);
Mage::app();
//Mage::getSingleton('core/session', array('name'=>'frontend'));
$session = Mage::getSingleton('customer/session');
try{
$session->login($email,$password);
$session->setCustomerAsLoggedIn($session->getCustomer());
//Load the customer modules
$customer = Mage::getModel('customer/customer')
->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
//Get customer attribbutes
$customer->loadByEmail($email);

//print_r($customer);

$customer_id = $customer['entity_id'];

$client = new SoapClient('http://www.magento.com/api/soap/?wsdl');

$session = $client->login('cat_tree', 'mypassword');

$result = $client->call($session, 'customer.info', $customer_id );

$response['response'] = "success";

$address_id = $result['default_billing'];

$result2 = $client->call($session, 'customer_address.info', $address_id);

$result['default_billing']=$result2;

$address_id2 = $result['default_shipping'];

$result3 = $client->call($session, 'customer_address.info', $address_id2);

$result['default_shipping']=$result3;

$response['response_data'] = $result;



echo json_encode($response);

}catch (Exception $e){
$key ="authenticate=N";

$response['response'] = "failed";
$response['reason'] = "Please SignUp!";

echo json_encode($response);



}