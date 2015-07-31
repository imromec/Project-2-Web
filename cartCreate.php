<?php

$proxy = new SoapClient('http://localhost/magento/api/v2_soap/?wsdl'); // TODO : change url
$sessionId = $proxy->login('cats', 'mypassword'); // TODO : change login and pwd if necessary

$result = $proxy->shoppingCartCreate($sessionId, '1');
echo json_encode($result);