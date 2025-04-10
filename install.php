<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///
include("sc-includes/php/install/install.inc.php"); ?>
<!-- ScriptClasificados.com !-->
<!doctype html>
<html lang="es-ES" xmlns:fb="http://ogp.me/ns/fb#" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">

<head>
	<title><?= $language['install.title_page'] ?></title>
	<!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="generator" content="ScriptClasificados.com">
	<meta name="copyright" content="Cyrweb">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" media="all" type="text/css" href="src/css/fonts/font-awesome.min.css">
	<link href="src/css/style.css" rel="stylesheet" type="text/css" />
	<link href="src/css/install.css" rel="stylesheet" type="text/css" />
</head>

<body>
	<div id="wrapper">
		<h1><?= $language['install.title_install'] ?></h1>
		<? if (!isset($_GET['start']) && !isset($_GET['config'])) { ?>
			<div class="part">
				<?= $language['install.txt_info'] ?>
			</div>
			<input type="button" onClick="location.href='install.php?start'" value="<?= $language['install.button_start'] ?>" />
		<? } ?>
		<? if (isset($_GET['start'])) { ?>
			<?
			include('config.php');
			$Connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
			if ($Connection == FALSE) {
				// CONNECTION FAILED
			?>
				<div class="part">
					<div class="info_invalid">
						Los datos de conexión del archivo "<strong>config.php</strong>" no son correctos, no se puede conectar a la base de datos.<br>
						Revisa los datos de conexión antes de intentarlo de nuevo.
					</div>
				</div>
				<input type="button" onClick="location.href='install.php?start'" value="<?= $language['install.button_restart'] ?>" />
			<? } elseif (!mysqli_select_db($Connection, DB_NAME)) { ?>
				<div class="part">
					<div class="info_invalid">
						La base de datos que has indicado no existe, debes crearla. Revisa los datos de conexión del archivo "<strong>config.php</strong>".<br>
						Revisa los datos de conexión antes de intentarlo de nuevo.
					</div>
				</div>
				<input type="button" onClick="location.href='install.php?start'" value="<?= $language['install.button_restart'] ?>" />
				<? } else {
				// CONNECTION VALID
				mysqli_query($Connection, "USE " . DB_NAME);
				// IMPORT SQL
				include("sc-includes/php/func/db_setup.php");
				$db_create = new db_setup(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				$db_create->import();
				if ($db_create->error) {
					// DATABASE NOT IMPORTED
					echo $db_create->error;
				?>
					<div class="part">
						<div class="info_invalid">
							No se ha podido crear la base de datos. Comprueba si la base de datos ya existe.<br>
							Si la base de datos ya existe, eliminala antes de continuar con la instalación
						</div>
					</div>
					<input type="button" onClick="location.href='install.php?start'" value="<?= $language['install.button_restart'] ?>" />
				<?
				} else {
					mysqli_query($Connection, 'SET NAMES "utf8"');
					@unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR . "sc-includes/php/func/script_tables.sql");
					// DATABASE IMPORTED 
				?>
					<div class="part">
						<div class="info_valid">
							La base de datos se ha creado correctamente.
						</div>
						<p>Ahora vamos a configurar los datos básicos del sitio</p>
						<form action="install.php?config" method="post" id="config_form">
							<?
							include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "sc-includes/php/func/func.php");
							$param_config_form = array('LICENSE_KEY', 'SITE_NAME', 'SITE_URL');
							for ($i = 0; $i < count($param_config_form); $i++)
								echo '<div><label>' . getConfText($param_config_form[$i]) . '</label>
                      <input type="text" name="' . $param_config_form[$i] . '" value="' . getConfParam($param_config_form[$i]) . '"></div>
                      ';
							echo '<hr/>';
							$param_config_form = array('ADMIN_USER', 'ADMIN_PASS', 'ADMIN_MAIL');
							for ($i = 0; $i < count($param_config_form); $i++)
								echo '<div><label>' . getConfText($param_config_form[$i]) . '</label>
                      <input type="text" name="' . $param_config_form[$i] . '" value="' . getConfParam($param_config_form[$i]) . '"></div>
                      ';
							?>
						</form>
					</div>
					<input type="button" onClick="document.getElementById('config_form').submit()" value="<?= $language['install.button_continue'] ?>" />
				<? } ?>
			<?
			}
			?>
		<? } elseif (isset($_GET['config'])) {
			if (isset($_POST['SITE_NAME'])) {
				include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "config.php");
				include(dirname(__FILE__) . DIRECTORY_SEPARATOR . "sc-includes/php/mysql/conn.php");
				$url_post = trim($_POST['SITE_URL'], "/");
				$config_save = array(
					'LICENSE_KEY' => $_POST['LICENSE_KEY'],
					'SITE_NAME' => $_POST['SITE_NAME'],
					'SITE_URL' => $url_post . "/",
					'ADMIN_USER' => $_POST['ADMIN_USER'],
					'ADMIN_MAIL' => $_POST['ADMIN_MAIL'],
					'ADMIN_PASS' => md5($_POST['ADMIN_PASS']),
				);
				foreach ($config_save as $param => $value) {
					updateSQL("sc_config", $s = array('value_param' => $value), $w = array('name_param' => $param));
				}
				@activateSite(DB_HOST, DB_USER, DB_PASS, DB_NAME, $_POST['SITE_URL'], $_POST['LICENSE_KEY']);
			}
		?>
			<div class="part">
				<div class="info_valid">
					ScriptClasificados ya está instalado en tu web!
				</div>
				<p>Ahora puedes acceder al panel de administración con los datos de acceso que has indicado o ir directamente a tu web!</p>
				<p>Para entrar al panel de administrador haz clíck aquí: <a href="sc-admin/">Panel de Administrador</a></p>
				<p>Para ir a tu sitio web haz clíck aquí: <a href="index.php">Página principal</a></p>
				<p class="note">* Recuerda configurar el resto de opciones de la web en el panel de administrador</p>
			</div>
		<? } ?>
	</div>
</body>

</html>