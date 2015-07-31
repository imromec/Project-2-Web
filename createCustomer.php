<?php
error_reporting(0);

$type = $_REQUEST['type'];

if($type=="REGISTER")
{
		$email = $_REQUEST['email'];
		$firstname = $_REQUEST['firstname'];
		$lastname = $_REQUEST['lastname'];
		$password = $_REQUEST['password'];
		$gender = $_REQUEST['gender'];
		$dob = $_REQUEST['dob'];
		$email_notification = $_REQUEST['email_notification'];
		$sms_notification = $_REQUEST['sms_notification'];
		$id = 1;
		
require_once '../app/Mage.php';
umask(0);
$websiteId = Mage::app()->getWebsite()->getId();
//$email = 'ajzele@someserver123.com';// Your Customers Email Here

function IscustomerEmailExists($email, $websiteId = null){
    $customer = Mage::getModel('customer/customer');

    if ($websiteId) {
        $customer->setWebsiteId($websiteId);
    }
    $customer->loadByEmail($email);
    if ($customer->getId()) {
        return $customer->getId();
    }
    return false;
}

$cust_exist = IscustomerEmailExists($email,$websiteId);

	if($cust_exist){
		$response['response'] = "failed";
		$response['reason'] = "Email Already Exist.";
		echo json_encode($response);
	}
	else{
		$client = new SoapClient('http://www.isayorganic.com/api/v2_soap/?wsdl');
			
			$session = $client->login('cat_tree', 'mypassword');
			
			$result = $client->customerCustomerCreate($session, array('email' => $email, 
																			'firstname' => $firstname, 
																			'lastname' => $lastname, 
																			'password' => $password,
																			'gender' => $gender,
																			'dob'   => $dob,
																			'website_id' => 1, 
																			'store_id' => 1, 
																			'group_id' => 1));		
			$response['response'] = "success";
			$response['customer_id'] = "$result";
			echo json_encode($response);
	}

}

else if($type=="Add Address")
{

$newCustomerId = $_REQUEST['customer_id'];

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

$proxy = new SoapClient('http://www.isayorganic.com/api/?wsdl');
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

$newAddressId = $proxy->call($sessionId, 'customer_address.create', array($newCustomerId, $newCustomerAddress));
$result = $proxy->call($sessionId, 'customer_address.list', $newCustomerId);

$response['response'] = "success";

//array_push($response, $result);
$response['response_data'] = $result;

echo json_encode($response);

}