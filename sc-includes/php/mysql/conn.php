<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

		
		$Connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS) or die($language['conn.error_connection']);
		
		if(!mysqli_select_db($Connection, DB_NAME)) die ($language['install.error_db_not_found']);
		
		mysqli_query($Connection, 'SET NAMES "utf8"');
	
	
	require_once("sql.inc.php");