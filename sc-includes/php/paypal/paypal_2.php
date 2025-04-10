<?php
ini_set('log_errors', true);
include('ipnlistener.php');
$listener = new IpnListener();
$listener->use_sandbox = false;
try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}
if ($verified) {
	include("../../../settings.inc.php");
// 	$paypal_configuration=selectSQL("sc_paypal",$w=array('ID_paypal'=>1));
// 	$time = time() + ($paypal_configuration[0]['time_2'] * 24 * 3600);
// 	updateSQL("sc_ad",$d=array('premium2' => 1, 'date_premium2' => $time),$w=array('ID_ad' => $_GET['id']));
	
// 	set_banner() //funcion no creada
	
	mailAdPremium_list($_GET['id']);
}

?>