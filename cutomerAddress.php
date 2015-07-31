<?php

$customer_id = $_REQUEST['customer_id'];

$proxy = new SoapClient('http://www.magento.com/api/v2_soap/?wsdl'); // TODO : change url
$sessionId = $proxy->login('cat_tree', 'mypassword'); // TODO : change login and pwd if necessary

$result = $proxy->customerAddressInfo($sessionId, $customer_id);
echo json_encode($result);