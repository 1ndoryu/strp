<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

	// Start Session on server
	session_start();
	// Path root
	define('ABSPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
	define('PATH',  '');
	
	// Directories and Constant
	define( 'SRC_PATH', PATH.'src/' );
	define( 'IMG_PATH', PATH.'src/images/' );
	define( 'IMG_BANNERS', PATH.'src/images/banners/' );
	define( 'CSS_PATH', PATH.'src/css/' );
	define( 'JS_PATH', PATH.'src/js/' );
	define( 'IMG_USER', PATH.'src/profile/' );
	define( 'IMG_ADS', PATH.'src/photos/' );
	define( 'IMG_CATEGORY', PATH.'src/images/category/' );
	define( 'IMG_AD_DEFAULT', 'default.png' );
	define( 'IMG_AD_DEFAULT_MIN', 'default_min.png' );

	// Settings
	define( 'DEBUG', true ); //false
	define( 'CHARSET', 'UTF-8');
	define('ADS_TIME', 180);

	date_default_timezone_set('Europe/Madrid');
	if (!DEBUG){
		error_reporting(0);
		ini_set('display_errors','off');
	}else{
		error_reporting(E_ALL | E_STRICT);
		ini_set('display_errors','off');
	}
	
	if (extension_loaded('zlib')) {
		if(!ob_start('ob_gzhandler')) ob_start();
	}

	if ( !defined('MEMORY_LIMIT') ) {
				define('MEMORY_LIMIT', '256M');
	}
	ini_set('memory_limit', MEMORY_LIMIT);

	if ( !defined('UPLOAD_MAX_FILESIZE') ) {
				define('UPLOAD_MAX_FILESIZE', '30M');
	}
	ini_set('upload_max_filesize', UPLOAD_MAX_FILESIZE);
	ini_set('max_execution_time','3000');
	
	include(PATH.'config.php');
	include(PATH.'sc-includes/php/func/country.php');
	include(PATH.'sc-includes/php/func/locations.php');
	include(PATH.'sc-includes/php/func/searchs.php');
	include(PATH.'sc-includes/php/func/lang.'.COUNTRY_LANGUAGE.'.php');
	include(PATH.'sc-includes/php/func/lang_admin.'.COUNTRY_LANGUAGE.'.php');
	include(PATH.'sc-includes/php/mysql/conn.php');
	include(PATH.'sc-includes/php/func/enums.php');
	include(PATH.'sc-includes/php/func/images.php');
	include(PATH.'sc-includes/php/func/func.php');
	include(PATH.'sc-includes/php/func/functions_images.php');
	include(PATH.'sc-includes/php/func/seo.php');
	include(PATH.'sc-includes/php/func/email.'.COUNTRY_LANGUAGE.'.php');
	include(PATH.'sc-includes/php/func/session.php');
	include(PATH.'sc-includes/php/func/updatefav.php');
	include(PATH.'sc-includes/php/func/service.php');
	include(PATH.'sc-includes/php/func/notice.php');
	include(PATH.'sc-includes/php/func/maintenance.php');
	include(PATH.'sc-includes/php/func/filter.php');
	include(PATH.'sc-includes/php/func/categories.php');
	include(PATH.'sc-includes/php/func/user.php');
	include(PATH.'sc-includes/php/func/item.php');
	include(PATH.'sc-includes/php/func/payment.php');
	include(PATH.'sc-includes/php/func/orders.php');
	include(PATH.'sc-includes/php/func/statistic.php');
	include(PATH.'sc-includes/php/func/tickets.php');
	include(PATH.'sc-includes/php/func/datos-structurados.php');
	include_once(PATH.'cron/events.php');

	require 'dompdf/autoload.inc.php';	
