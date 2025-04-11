<!doctype html>
<?php
define('SITE_KEY', '6Lf99qUZAAAAAFj81VklB-mbobzIljUiKcYyrAXq');
define('SECRET_KEY', '6Lf99qUZAAAAAA3N-761jR2zGSB-HHWtkZeOh9Mz');
?>
<html lang="<?=getConfParam('LANGUAGE');?>" xmlns:fb="http://ogp.me/ns/fb#" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">
<head>
<base href="<?=getConfParam('SITE_URL');?>">
<? getCanonical();?>
<!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link rel="shortcut icon" type="image/x-icon" href="<?=getConfParam('SITE_URL');?><?=IMG_PATH;?>favicon.ico">
<title><?=$TITLE_;?></title>
<meta name="description" content="<?=$DESCRIPTION_;?>" />
<meta name="keywords" content="<?=$KEYWORDS_;?>">
<meta property="og:title" content="<?=$TITLE_;?>">        
<meta property="og:description" content="<?=$DESCRIPTION_;?>">
<meta property="og:site_name" content="<?=getConfParam('SITE_NAME');?>">
<meta property="og:url" content="<?=trim(getConfParam('SITE_URL'),'/');?><?=$_SERVER['REQUEST_URI'];?>">
<meta property="og:type" content="<?=$TYPE_SITE;?>">
<? getImageHead(); ?>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css" integrity="sha384-HzLeBuhoNPvSl5KYnjx0BT+WB0QEEqLprO+NBkkk5gbc67FTaL7XIGa2w1L0Xbgc" crossorigin="anonymous">
<!--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />-->

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="src/js/jquery-ui.min.js"></script>

<!--- para agregar -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
<script src="../src/js/glide.min.js"></script>
<link rel="stylesheet" href="src/css/glide.core.min.css">
<link rel="stylesheet" href="src/css/glide.theme.css">
<link rel="stylesheet" href="src/css/jquery-ui.min.css">



<link rel="stylesheet" href="src/css/select2.css">
<link rel="stylesheet" media="all" type="text/css" href="<?=getConfParam('SITE_URL');?><?=CSS_PATH;?>style.css">
<link rel="alternate" type="application/rss+xml" title="<?=$TITLE_;?>" href="<?=getConfParam('SITE_URL');?>feed/">
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>-->
<!--<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>-->
<script src="src/js/select2.js"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>jquery.sortable.min.js"></script>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>jquerynumeric.js"></script>
<link rel="stylesheet" type="text/css" href="src/css/style.css?v=0.1">
<link rel="stylesheet" type="text/css" href="src/css/w-formPost.css">
<script type="text/javascript">
var site_url = '<?=getConfParam('SITE_URL');?>';
</script>

<!--<link rel="stylesheet" href="node_modules/@glidejs/glide/dist/css/glide.core.min.css">-->
<!--<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>-->
<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide">-->
<style>

footer {
  text-align: center;
  padding: 3px;
  background-color: #1c609c;
  width: 100%;
  height:4%;
  color: white;
}


</style>
</head>
<body <? getColor('BODY_COLOR')?>>
<? if(getConfParam('ANALYTICS_ID')!==""){?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-<?=getConfParam('ANALYTICS_ID')?>', 'auto');
  ga('send', 'pageview');
</script>
<? } ?>
