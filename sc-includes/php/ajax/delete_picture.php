<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

include("../../../settings.inc.php");

if(isset($_POST['name_image'])) {
	$image=selectSQL("sc_images",$wm=array('name_image'=>$_POST['name_image']));
	if(count($image)!=0){
			updateSQL("sc_images",$d=array('status'=>ImageStatus::Delete),$wm=array('name_image'=>$_POST['name_image']));
			die ("1");
	}else die("2");
}else die("3");


?>