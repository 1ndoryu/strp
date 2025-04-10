<?
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

include("../../../settings.inc.php");

if(isset($_POST['region'])){
	$consulta=mysqli_query($Connection, "SELECT * FROM sc_city WHERE ID_region IN (SELECT ID_region FROM sc_region WHERE name_seo='".$_POST['region']."')");
	echo '<option value="">'.$language['load.txt_select'].'</option>';
	while($city=mysqli_fetch_array($consulta)){
		echo '<option value="'.$city['name_seo'].'">'.$city['name'].'</option>';
	}
}


?>