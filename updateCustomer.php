<?php
error_reporting(0);
$email = $_REQUEST['email'];
$firstname = $_REQUEST['firstname'];
$lastname = $_REQUEST['lastname'];
$password = $_REQUEST['password'];
$gender = $_REQUEST['gender'];
$dob = $_REQUEST['dob'];
$type = $_REQUEST['type'];		

if($type == "Update User")
{	
	$customer_id = $_REQUEST['customer_id'];	
	$client = new SoapClient('http://www.magento.com/api/v2_soap/?wsdl');
	$session = $client->login('cat_tree', 'mypassword');
	
	$result = $client->customerCustomerUpdate($session, $customer_id, array('email' => $email, 
																		 'firstname' => $firstname,
																		 'lastname' => $lastname,
																		 'password' => $password,
																		 'gender'   => $gender,
																		 'dob' 		 => $dob,
																		 'website_id' => 1,
																		 'store_id' => 1,
																		 'group_id' => 1));
	$response['response'] = "success";
	
	
	$proxy = new SoapClient('http://www.magento.com/api/?wsdl');
	$sessionId = $proxy->login('cat_tree', 'mypassword');
	$result = $proxy->call($sessionId, 'customer.info', $customer_id);

	$response['response'] = "success";
	$response['response_data'] = $result;

	echo json_encode($response);
}
else if($type == "Update Address")
{
	
$address_id = $_REQUEST['address_id'];

$firstname = $_REQUEST['firstname'];
$lastname = $_REQUEST['lastname'];
$country = $_REQUEST['country_id'];
$region = $_REQUEST['region'];
$city = $_REQUEST['city'];
$street1 = $_REQUEST['street1'];
$street2 = $_REQUEST['street2'];
$telephone = $_REQUEST['telephone'];
$postcode = $_REQUEST['postcode'];
$is_default_billing = $_REQUEST['is_default_billing'];
$is_default_shipping = $_REQUEST['is_default_shipping'];

$proxy = new SoapClient('http://www.magento.com/api/?wsdl');
$sessionId = $proxy->login('cat_tree', 'mypassword');


//Create new customer address
$newCustomerAddress = array(
    'firstname'  => $firstname,
    'lastname'   => $lastname,
    'country_id' => $country,
    'region'     => $region,
    'city'       => $city,
    'street'     => array($street1,$street2),
    'telephone'  => $telephone,
    'postcode'   => $postcode,

    'is_default_billing'  => $is_default_billing,
    'is_default_shipping' => $is_default_shipping
);

$newAddressId = $proxy->call($sessionId, 'customer_address.update', array($address_id, $newCustomerAddress));

$result = $proxy->call($sessionId, 'customer_address.info', $address_id);

$response['response'] = "success";

//array_push($response, $result);
$response['response_data'] = $result;
echo json_encode($response);

}