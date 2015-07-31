<?php

$cat_id = $_REQUEST['cat_id'];

$proxy = new SoapClient('http://www.magento.com/api/v2_soap/?wsdl'); // TODO : change url
$sessionId = $proxy->login('cat_tree', 'mypassword'); // TODO : change login and pwd if necessary

$result = $proxy->catalogCategoryInfo($sessionId, $cat_id);
echo json_encode($result);
?>