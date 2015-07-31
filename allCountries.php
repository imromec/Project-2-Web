<?php

$proxy = new SoapClient('http://www.isayorganic.com/api/v2_soap/?wsdl'); // TODO : change url
$sessionId = $proxy->login('cat_tree', 'mypassword'); // TODO : change login and pwd if necessary

$result = $proxy->directoryCountryList($sessionId);
echo json_encode($result);