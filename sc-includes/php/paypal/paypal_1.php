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
// 	$time = time() + ($paypal_configuration[0]['time_1'] * 24 * 3600);
// 	updateSQL("sc_ad",$d=array('premium1' => 1, 'date_premium1' => $time),$w=array('ID_ad' => $_GET['id']));
    
    
    $time = // time para el set premium es 'value' en tabla sc_time_options de la opcion comprada
    // set_premium(getAddData($_GET['id']),);
	
	
	mailAdPremium_home($_GET['id']);
}
?>