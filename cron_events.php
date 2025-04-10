<?php 
define('ABSPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
require_once "settings.inc.php";


$files = scandir(ABSPATH . 'cron');
foreach($files as $file) {
	if(strpos($file, '.php') !== false) {
		include_once ABSPATH . 'cron/' . $file;
	}
}

Events::run();
