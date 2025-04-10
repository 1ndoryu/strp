<?
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

include("../../../settings.inc.php");

if(isset($_GET['p'])){
	$ad=getDataAd($_GET['p']);
	echo $ad['ad']['phone'];
}


?>