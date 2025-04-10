<?
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

include("../../../settings.inc.php");

$tot = count($_COOKIE['fav']);
if($tot==0){
	echo "";
}else{
	echo "(".$tot.")";
}


?>