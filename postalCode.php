<?php
error_reporting(0);
mysql_connect("localhost","isoindia_all","All@123");
mysql_select_db("isoindia_organic_new");

$postalcode = $_REQUEST['postcode'];

$sql = mysql_query("select * from zip_price where zipcode='".$postalcode."'");
$num = mysql_num_rows($sql);

if($num == 1)
{
	$reponse['response']="success";
	$fetch = mysql_fetch_array($sql);
	
	$res['postal_code'] = $fetch['zipcode'];
	$res['time'] = $fetch['time'];
	$res['days'] = $fetch['day'];
	$res['shipping_cost'] = $fetch['shipping_cost'];
	$res['min_price_without_shipping_cost'] = $fetch['price'];
	
	$response['response_data']=$res;
}
else
{
	$reponse['response']="failed";
	$res['message'] = "We currently don't deliver to your area.";
	$reponse['response_data']=$res;
}

echo json_encode($reponse);
