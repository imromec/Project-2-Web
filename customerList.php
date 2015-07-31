<?php

$proxy = new SoapClient('http://www.magento.com/api/v2_soap/?wsdl'); // TODO : change url
$sessionId = $proxy->login('cat_tree', 'mypassword'); // TODO : change login and pwd if necessary

$result = $proxy->customerCustomerList($sessionId);
echo json_encode($result);