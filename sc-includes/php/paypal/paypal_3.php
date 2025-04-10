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
	$paypal_configuration=selectSQL("sc_paypal",$w=array('ID_paypal'=>1));
    
    // add_credits($_GET['user_id'], $cantidad, $_GET['adult']) (tipo de credito)
    
	mailAdPremium_list($_GET['id']);
}

?>