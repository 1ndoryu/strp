<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

include("../../../settings.inc.php");
if(check_login_admin()){
	if(isset($_GET['f'])){
		download_file(ABSPATH."sc-admin/backup/".$_GET['f'],$_GET['f']);
	}
}
?>