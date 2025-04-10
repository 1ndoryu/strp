<?
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

include("../../../settings.inc.php");

if(isset($_POST['region'])){
	$consulta=mysqli_query($Connection, "SELECT * FROM sc_city WHERE ID_region=".$_POST['region']."");
	echo '<option value="0">'.$language['load.txt_select'].'</option>';
	while($city=mysqli_fetch_array($consulta)){
		echo '<option value="'.$city['ID_city'].'">'.$city['name'].'</option>';
	}
}


?>