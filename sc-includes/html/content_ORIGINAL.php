<?php
if( isset( $_GET['logout'] )) logout();
?>
<?
// Mensajes
if(isset($_SESSION['data'])){
			$messages_no_read=mysqli_query($Connection, "SELECT ID_message FROM sc_messages WHERE leido=1 AND (recibe='".$_SESSION['data']['ID_user']."' AND recibe_del=0)");
			$tot_messages_no_read=mysqli_num_rows($messages_no_read);
}
$tot_fav=0;
if(isset($_COOKIE['fav'])){ $tot_fav=count($_COOKIE['fav']); }
?>
<div class="topbar" <? getColor('TOPBAR_COLOR')?>>
	<div class="center_content">
    	<div class="navbar">
	    	<i class="fa fa-times close-navbar" aria-hidden="true"></i>
			<? if(!isset($_SESSION['data'])){?>
            <a href="crear-cuenta/">Registro</a>
            <a class="user-menu login" href="javascript:void(0);">Mi cuenta</a>
            <? }else{ ?>
            <span class="user_item_photo" style="background-image:url(<?=getPhotoUser($_SESSION['data']['ID_user'])?>"></span>
            <span class="user_name_topbar"><?=$_SESSION['data']['name']?></span>
            <a href="mis-mensajes/"><span class="user-message<? if($tot_messages_no_read>0) echo ' on';?>">Mis Mensajes (<i><?=$tot_messages_no_read?></i>)</span></a>
            <a href="mi-cuenta/">Mi cuenta</a>
            <a href="mis-anuncios/">Mis anuncios</a>
            <a href="favoritos/">Favoritos (<?=$tot_fav?>)</a>
            <a href="cerrar-sesion/" class="close-session">Desconectar</a>
            <? } ?>
       </div>
	</div>
</div>
<header <? getColor('HEADER_COLOR')?>>
    <div class="center_content">
    	<i class="fa fa-bars menu-navbar" aria-hidden="true"></i>
        <a href="index.php"><span class="logo"></span></a>
        <a href="<?=$urlfriendly['url.post_item']?>" class="post_item_link transition"><i class="fa fa-pencil"></i> <span><?=$language['content.button_post']?></span></a>
	</div>
</header>
<div class="search" <? getColor('SEARCHBAR_COLOR')?>>
	<div class="center_content">
        <div class="input_search">
        <input itemprop="query-input" type="text" name="keyword_search" id="keyword_search" required placeholder="<?=$language['content.keyword_search']?>" value="<? if(isset($query_s)) echo $query_s;?>">
        </div>
       	<select id="region_search">
          <option value=""><?=$language['content.select_region']?></option>
          <?
          $region = selectSQL("sc_region","","name ASC");
          for($i=0;$i<count($region);$i++){ ?>
          <option value="<?=$region[$i]['name_seo']; ?>" <? if(isset($_GET['zone']) && $_GET['zone']==$region[$i]['name_seo']) echo 'selected';?>><?=$region[$i]['name']; ?></option>
          <? } ?>
    	</select>
	    <select id="search_cat">
          <option value=""><?=$language['content.select_category']?></option>
          <?
          $parent = selectSQL("sc_category",$w=array('parent_cat'=>-1),"name ASC");
          for($i=0;$i<count($parent);$i++){ ?>
          <option value="<?=$parent[$i]['name_seo']; ?>" <? if(isset($_GET['s']) && $_GET['s']==$parent[$i]['name_seo']) echo 'selected';?> style="background: #FFECB8;color: #000;"><?=$parent[$i]['name']; ?></option>
          <?
          $child = selectSQL("sc_category",$ma=array("parent_cat"=>$parent[$i]['ID_cat']),"name ASC");
            for($j=0;$j<count($child);$j++){	?>
                <option value="<?=$parent[$i]['name_seo']; ?>/<?=$child[$j]['name_seo']; ?>" <? if(isset($_GET['se']) && $_GET['se']==$child[$j]['name_seo']) echo 'selected';?>><?=$child[$j]['name']; ?></option>
          <? }
          } ?>
        </select>
        <span class="button_search transition" id="but_search_main"><i class="fa fa-search"></i> Buscar anuncios</span>
	</div>
</div>
<?
if(isset($_GET["id"]))	include(PATH ."sc-includes/php/func/bread.php");
?>
<div id="content">
	<?php
	if(isset($_GET["id"])){
		$id = $_GET["id"];
		if(file_exists(PATH ."sc-includes/"."$id.php")){
			include(PATH ."sc-includes/main-reducido.php");
			include(PATH ."sc-includes/"."$id.php");
		}else{
			if(file_exists(PATH ."sc-includes/"."$id")){
		    	include(PATH ."sc-includes/"."$id.php");
			}else{
				include(PATH ."sc-includes/"."404.php");
			}
		}
	}else include(PATH ."sc-includes/main.php");
	?>
</div>
<footer <? getColor('FOOTER_COLOR')?>>
    <div class="center_content">
    <?=$language['content.footer_txt_1']?><a href="<?=getConfParam('SITE_URL');?>" class="txt"><?=getConfParam('SITE_NAME');?></a><?=$language['content.footer_txt_2']?> | <a href="http://www.cyrweb.com/" class="txt">Desarrollo web</a>
<div class="social_bookmakers">
    	<a href="<?=getConfParam('FB_PAGE_LINK');?>"><span class="facebook_link"></span></a>
        <a href="<?=getConfParam('TW_PAGE_LINK');?>"><span class="twitter_link"></span></a>
        <a href="<?=getConfParam('GP_PAGE_LINK');?>"><span class="google_link"></span></a>
        <a href="<?=getConfParam('SITE_URL');?>feed/"><span class="rss_link"></span></a>
    </div>
	</div>
</footer>
