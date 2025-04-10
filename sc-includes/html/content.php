<?php
if( isset( $_GET['logout'] )) logout();
?>
<?php
// Mensajes
if(isset($_SESSION['data'])){
			$messages_no_read=mysqli_query($Connection, "SELECT ID_message FROM sc_messages WHERE leido=1 AND (recibe='".$_SESSION['data']['ID_user']."' AND recibe_del=0)");
      $tot_messages_no_read=mysqli_num_rows($messages_no_read);
      $messages = mysqli_query($Connection, "SELECT ID_message FROM sc_messages WHERE (recibe='".$_SESSION['data']['ID_user']."' AND recibe_del=0)");
      $tot_messages = mysqli_num_rows($messages);
}
$tot_fav=0;
if(isset($_COOKIE['fav'])){ $tot_fav=count($_COOKIE['fav']); }

if(!isset($_COOKIE['cookie_advice']) || $_COOKIE['cookie_advice'] != '1'){
  echo loadBlock('cookies');
} 

?>
<div class="main-content" <? getColor('BODY_COLOR')?>>
<header > 
  <div class="center_content">
    <div class="row">
      <div class="col-md-1">
        <i class="fa fa-bars menu-navbar" aria-hidden="true"></i>
        <a href="index.php">
          <span class="logo d-inline-block d-md-none"></span>
        </a>
        <a href="<?=$urlfriendly['url.post_item']?>" class="post_item_link transition d-block d-md-none"><i class="fa fa-pencil-alt"></i> <span><?=$language['content.button_post']?></span></a>
      </div>
      <div class="col-md-11 px-0 d-none d-md-block">
        <div class="w-100 h-100" >
            <div class="topbar-content w-content to-right to-bottom" >

              <?php if(!isset($_SESSION['data'])){?>
                <!--<a href="crear-cuenta/">Registro</a>-->
                <a class="user-menu login" href="javascript:void(0);"><i class="fa fa-user pr-2"></i> Mi cuenta</a>
                <a href="contactar/"><i class="fa fa-envelope pr-2"></i> Contactar</a>
                <a href="favoritos/"><i style="color: var(--rosa);" class="fa fa-heart pr-2"></i> Favoritos (<?=$tot_fav?>)</a>
                <a href="javascript:void(0);" class="btn-post-premium">
                  <img height="55" width="168" src="<?=Images::getImage("top-destacar.webp")?>" alt="btn-premium">
                </a>
                <a class="btn-post" href="<?=$urlfriendly['url.post_item']?>"> 
                      PUBLICAR ANUNCIO GRATIS
                </a>
                <?php }else{ ?>
                  <?php if($_SESSION['data']['rol'] != UserRole::Visitante ): ?>
                    <a href="mi-cuenta/">
                      <img src="<?=getPhotoUser($_SESSION['data']['ID_user'])?>" alt="<?=$_SESSION['data']['name']?>" title="<?=$_SESSION['data']['name']?>" width="50" height="50" class="image-user">
                      <!-- <span class="user_name_topbar"><?=$_SESSION['data']['name']?></span> -->
                    </a>
                    <a href="mis-anuncios/"><i class="fa fa-list icon-alt"></i> Mis anuncios</a>
                    <a href="mis-mensajes/"><span class="user-message<?php if($tot_messages_no_read>0) echo ' on';?>">
                      <i class="fa fa-comments icon-alt "></i> Mis Mensajes (<i><?=$tot_messages?></i>)</span>
                    </a>
                    <!-- <a href="mis-notificaciones/">
                      <?php if($_SESSION['data']['notify'] == 1): ?>
                        <i class="fa fa-bell icon-alt"></i>
                      <?php else: ?>
                        <i class="fa fa-bell-slash icon-alt"></i>
                      <?php endif ?>
                      Mis alertas
                    </a> -->
                    <!-- <a href="favoritos/"><i class="fa icon-alt fa-heart"></i> Favoritos (<?=$tot_fav?>)</a> -->
                    <a href="cerrar-sesion/" class="close-session"> Desconectar</a>
                
                    <a class="btn-post" href="<?=$urlfriendly['url.post_item']?>"> 
                      PUBLICAR ANUNCIO GRATIS
                    </a>
                  <?php else: ?>  
                    <a href="mi-cuenta/">
                      <img src="<?=getPhotoUser($_SESSION['data']['ID_user'])?>" alt="<?=$_SESSION['data']['name']?>" title="<?=$_SESSION['data']['name']?>" width="50" height="50" class="image-user">
                      <!-- <span class="user_name_topbar"><?=$_SESSION['data']['name']?></span> -->
                    </a>
                    <a href="mis-mensajes/"><span class="user-message<?php if($tot_messages_no_read>0) echo ' on';?>">
                      <i class="fa fa-comments icon-alt "></i> Mis Mensajes (<i><?=$tot_messages?></i>)</span>
                    </a>
                    <a href="mis-notificaciones/">
                      <?php if($_SESSION['data']['notify'] == 1): ?>
                        <i class="fa fa-bell icon-alt"></i>
                      <?php else: ?>
                        <i class="fa fa-bell-slash icon-alt"></i>
                      <?php endif ?>
                      Mis alertas
                    </a>
                    <a href="mis-favoritos/"><i class="fa icon-alt fa-heart"></i> Favoritos (<?=$tot_fav?>)</a>
                    <a href="cerrar-sesion/" class="close-session"> Desconectar</a>
                  <?php endif ?>


                <?php } ?>
            </div>
          </div>
      </div>
    </div>
  </div>
</header>

<? 
  loadBlock("header-banner");
?>




<div class="topbar" <?php getColor('HEADER_COLOR')?> >
  <div class="center_content">
      <div class="navbar p-0 d-block d-md-none <?= isset($_GET['menuopen']) ? 'opened' : ''?>">
        <i class="fa fa-times close-navbar" aria-hidden="true"></i>
        <div class="w-100" >
          <div class="topbar-content w-content to-right px-2" >

            <?php if(!isset($_SESSION['data'])){?>
              <!--<a href="crear-cuenta/">Registro</a>-->
              <a class="user-menu login" href="javascript:void(0);"><i class="fa fa-user pr-2"></i> Mi cuenta</a>
              <a href="contactar/"><i class="fa fa-envelope pr-2"></i> Contactar</a>
              <?php }else{ ?>
              <a href="mi-cuenta/" style="border: none;
    padding: 0;">
              <span class="user_item_photo" style="background-image:url(<?=getPhotoUser($_SESSION['data']['ID_user'])?>"></span>
              <span class="user_name_topbar">Mi cuenta</span>
              </a>
              <?php if($_SESSION['data']['rol'] != UserRole::Visitante ): ?>
                <a href="mis-anuncios/"><i class="fa fa-list icon-alt pr-2"></i> Mis anuncios</a>
              <?php endif ?>
              <a href="mis-mensajes/">
                <span class="user-message <?php if($tot_messages_no_read>0) echo ' on';?>"> 
                <i class="fa icon-alt fa-comments pr-2"></i> Mis Mensajes (<i><?=$tot_messages?></i>)
                </span>
              </a>
              <a href="mis-notificaciones/">
                <?php if($_SESSION['data']['notify'] == 1): ?>
                  <i class="fa fa-bell icon-alt pr-2"></i>
                <?php else: ?>
                  <i class="fa fa-bell-slash icon-alt pr-2"></i>
                <?php endif ?>
                 Mis alertas
              </a>
             

              <a href="mis-favoritos/"><i class="fa fa-heart icon-alt pr-2"></i>  Favoritos (<?=$tot_fav?>)</a>
              <a href="mis-tickets/"><i class="fa fa-tag icon-alt pr-2"></i>  Mis tickets | servicios</a>
              <a href="mis-precios/"><i class="fa fa-tag icon-alt pr-2"></i>  Tarifas</a>
              <a href="cerrar-sesion/" class="close-session">Desconectar</a>
              <?php } ?>
          </div>
        </div>
       </div>
  </div>
</div>

<div class="search d-block d-md-none" <?php getColor('SEARCHBAR_COLOR')?>>
	<div class="center_content">
        <div class="input_search">
        <input itemprop="query-input" type="text" name="_search" id="keyword_search1" required placeholder="<?=$language['content.keyword_search']?>" value="<?php if(isset($query_s)) echo $query_s;?>">
        </div>
      
        <span class="button_search transition" id="but_search_main1"><i class="fa fa-search"></i></span>
	</div>
</div>

<?php 
  if(!isset($_GET["id"]) || (isset($_GET["id"]) && $_GET["id"] == "item"))
      loadBlock("menu-simple");
  else
      loadBlock("menu");
?>



<?php 
if(isset($_GET["id"]) && $_GET["id"] == "item")
include(PATH ."sc-includes/php/func/bread.php");
?>
<div id="content" class="<?=!isset($_GET["id"]) ? 'pb-2': ''?>">
	<?php
	if(isset($_GET["id"])){
		$id = $_GET["id"];
		if(file_exists(PATH ."sc-includes/"."$id.php")){

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

