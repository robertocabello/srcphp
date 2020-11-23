<?php
	include('includes/config.php');
	include("includes/class.phpmailer.php");


	require_once('webpay-sdk-php/libwebpay/webpay.php');
	require_once('webpay-sdk-php/cert-oneclick.php');

	$webpay_settings = array(
		"MODO" => "INTEGRACION",
		"PRIVATE_KEY" => $certificate['private_key'],
		"PUBLIC_CERT" => $certificate['public_cert'],
		"WEBPAY_CERT" => $certificate['webpay_cert'],
		"COMMERCE_CODE" => $certificate['commerce_code'],
		"URL_RETURN" => $baseurl."webpay_ws_oneclick.php?action=result",
		"URL_FINAL" => $baseurl."webpay_ws_oneclick.php?action=end",
	);

	$request = array(
        "username" => $username,
        "email" => $email,
        "urlReturn" => $urlReturn
    );

	$webpay = new WebPaySOAP($webpay_settings);
	$webpay = $webpay->getNormalTransaction();
?>