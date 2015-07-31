<?php 

$email = $_REQUEST['email'];

require_once ('../app/Mage.php');
Mage::app();

 $customer = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email);
        if (count($customer->getId())==1) {
            try {
                $newResetPasswordLinkToken =  Mage::helper('customer')->generateResetPasswordLinkToken();
                $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                $customer->sendPasswordResetConfirmationEmail();
				
				  $response['response'] = "success";
				  $response['message']="Password Reset Link Has Been Sent to Your Email Please Check, Your Mail Box!";
				  echo json_encode($response);
            	} catch (Exception $exception) {
                Mage::log($exception);
				}
		}
		else{
				  $response['response'] = "failed";
				  $response['message']="Please Enter a Valid Email!";
				  echo json_encode($response);
		}