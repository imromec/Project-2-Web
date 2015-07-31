<?php

$customer_id = $_REQUEST['customer_id'];

$client = new SoapClient('http://localhost/magento/api/soap/?wsdl');

$session = $client->login('cats', 'mypassword');

$result = $client->call($session, 'customer.info', $customer_id );
//var_dump($result);
echo json_encode($result);
// If you don't need the session anymore
//$client->endSession($session);

?>