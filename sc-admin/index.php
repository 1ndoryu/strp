<? include("../settings.inc.php");
noCache();
if(isset($_GET['exit'])){
	destroy_sesion_admin();
	echo '<script type="text/javascript">
					location.href = "index.php";
			</script>';
}
?>
<!doctype html>
<html lang="<?=getConfParam('LANGUAGE');?>" >
<head>
<base href="<?=getConfParam('SITE_URL');?>sc-admin/">
<!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta charset="UTF-8">
<meta http-equiv="cache-control" content="no-cache">
<title><?=$language_admin['index.title']?></title>
<link rel="stylesheet" href="../src/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../src/css/select2.min.css">
<link rel="stylesheet" media="all" type="text/css" href="<?=getConfParam('SITE_URL');?><?=CSS_PATH;?>style.css?v=0.1">
<link rel="stylesheet" media="all" type="text/css" href="<?=getConfParam('SITE_URL');?><?=CSS_PATH;?>main.css?v=0.1">
<link rel="stylesheet" type="text/css" href="<?=getConfParam('SITE_URL');?>sc-admin/res/admin_style.css?v=0.1">
<link rel="shortcut icon" type="image/x-icon" href="<?=getConfParam('SITE_URL');?><?=IMG_PATH;?>favicon.ico">
<!-- <link rel="stylesheet" href="../src/css/glide.core.min.css">
<link rel="stylesheet" href="../src/css/glide.theme.css"> -->
<link rel="stylesheet" href="../src/css/photoswipe.css">
<link rel="stylesheet" href="../src/css/webfonts/fuentes.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="../src/js/jquery-3.5.1.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script src="../src/js/jquery-ui.min.js"></script>
<!-- <script src="../src/js/glide.min.js"></script> -->
<script src="../src/js/select2.js"></script>
<script src="../src/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>jquery.sortable.min.js"></script>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>jquerynumeric.js"></script>
<script type="text/javascript">
var site_url = '<?=getConfParam('SITE_URL');?>';
</script>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>js.js"></script>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?>sc-admin/res/admin-js.js"></script>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?>sc-admin/res/jscolor.min.js"></script>
</head>
<body>
<header>
    <div class="center_content">
    	<? 	if(check_login_admin()){ ?>
        <i class="fa fa-bars open-menu" aria-hidden="true"></i>
        <? } ?>
        <a href="index.php"><span <?php if(!check_login_admin()){ ?>class="cent"<? }?>></span></a>
	    <nav><a href="<?=getConfParam('SITE_URL');?>">Ver sitio</a> <b>|</b>
			 <a href="index.php?exit">Desconectar</a> <b>|</b> 
		<i class="d-none d-md-inline"><?=$language_admin['index.server_time']?>: <?=date("H:i:s");?></i>
		</nav>
    	<? 	if(check_login_admin()){ ?>
        <div class="menu-content">
        <?php create_menu_admin(); ?>
        </div>
        <? } ?>
    </div>
</header>
<div id="content" <?php if(!check_login_admin()){ ?>class="no-back"<? }?>>
<?php 
	if(!check_login_admin()){
		if(isset($_GET['token'])){
			include("recpass.php");
		}else
			include("login.php");
	}else{?>
<div class="col_single"><?
		if(isset($_GET["id"])){
			$id = $_GET["id"]; 
			if(file_exists("$id.php")){
				include("$id.php");
			}else{
				if(file_exists("$id")){
					include("$id.php");
				}else{
					include("error.php");
				}
			}
		}else
			include("default.php");
	?>
</div>
    <?
	}
?>
</div>
<!-- javsacript no prioritario -->
<script defer src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script defer src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

</body>
</html>