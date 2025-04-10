<?
check_login();
if(isset($_GET['del_account']) && $_GET['del_account']==true){
	deleteUser($_SESSION['data']['ID_user']);
}

User::autoLogin();

updateLogin();
User::resetRenovations();

?>
<div class="nav_account d-none d-md-block mt-md-4">
    <ul>
        <?php if($_SESSION['data']['rol'] != UserRole::Visitante): ?>
          
            <a href="<?=$urlfriendly['url.my_items']?>">
                <li <? if(!isset($_GET['p'])){?>class="sel"<? } ?>>
                    <i class="fa fa-bars"></i>
                    <span><?=$language['account.my_items']?></span>
                </li>
            </a>
            <a href="mis-favoritos/">
                <li <? if($_GET['p']==2){?>class="sel"<? } ?>>
                    <i class="fa fa-heart"></i>

                    <span>Favoritos (<?= isset($_COOKIE['fav']) ? count($_COOKIE['fav']) : '0'?>)</span>
                </li>
            </a>

            <a href="<?=$urlfriendly['url.my_account']?>">
                <li <? if($_GET['p']==1){?>class="sel"<? } ?>>
                    <i class="fa fa-user"></i>
                    <span>Datos de usuario</span>
                </li>
            </a>

            <a href="mis-mensajes/">
                <li <? if($_GET['p']==3){?>class="sel"<? } ?>>
                    <i class="fa fa-comments"></i>
                     <span>Mensajes (<?=$tot_messages?>)</span>
                </li>
            </a>

        
            <a href="javascript:void(0)">
                <li class="ml-md-2" >
                    <i class="fa fa-coins"></i>
                    Créditos <?= $_SESSION['data']['credits'] ?>
                </li>
            </a>
            <a href="comprar-creditos/">
                <li <? if($_GET['p']==5){?>class="sel"<? } ?> >
                    <i class="fa fa-plus-circle"></i>
                    <span>Recargar Créditos</span>
                </li>
            </a>
            <a href="mis-tickets/">
                <li <? if($_GET['p']==4){?>class="sel"<? } ?>>
                <svg id="Layer_1" enable-background="new 0 0 512.006 512.006" height="512" viewBox="0 0 512.006 512.006" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m456.637 178.532c-7.438-4.768-17.335-2.604-22.104 4.835-7.204 11.238-19.043 24.635-34.53 24.635-12.932 0-27.117-7.227-37.341-24h21.341c8.837 0 16-7.164 16-16s-7.163-16-16-16h-31.613c-.502-5.353-.53-10.348 0-16h31.613c8.837 0 16-7.164 16-16s-7.163-16-16-16h-21.341c10.231-16.785 24.421-24 37.341-24 15.487 0 27.326 13.396 34.53 24.635 4.768 7.44 14.668 9.604 22.104 4.835 7.439-4.769 9.604-14.666 4.835-22.105-41.349-64.507-111.535-43.147-134.268 16.635h-7.202c-8.837 0-16 7.164-16 16 0 8.452 6.666 16 16.29 16-.381 5.58-.392 10.249 0 16-9.619 0-16.29 7.544-16.29 16 0 8.836 7.163 16 16 16h7.202c22.741 59.804 92.907 81.16 134.268 16.635 4.77-7.439 2.605-17.336-4.835-22.105z"/><path d="m184.002 368.002h-7.572c-7.082 0-13.578-4.432-15.689-11.192-2.556-8.187 1.723-16.748 9.521-19.738l120.016-46.159c8.429-3.242 12.477-12.799 8.999-21.134-47.344-113.465-59.191-253.421-59.325-255.067-1.219-15.054-20.837-20.202-29.26-7.59l-23.13 34.69-34.68-23.12c-9.179-6.127-21.719-1.313-24.4 9.43l-10.5 41.97-29.1-19.4c-9.179-6.127-21.719-1.313-24.4 9.43l-10.5 41.97-29.1-19.4c-11.086-7.391-25.874 1.264-24.83 14.55.32 4.15 8.23 102.86 38.24 203.34 17.88 59.85 39.98 107.77 65.68 142.45 18.034 24.311 45.676 36.97 73.602 36.97h5.579c30.732 0 56.376-24.405 56.843-55.134.475-31.275-24.826-56.866-55.994-56.866zm-102.86-170.06c-3.28-8.2.71-17.51 8.92-20.8l80-32c8.2-3.28 17.51.71 20.8 8.92 3.28 8.2-.71 17.51-8.92 20.8-86.636 34.654-81.523 33.14-85.94 33.14-6.34 0-12.35-3.8-14.86-10.06zm36.8 72.92c-8.085 3.234-17.472-.619-20.8-8.92-3.28-8.2.71-17.51 8.92-20.8l80-32c8.2-3.28 17.51.71 20.8 8.92 3.28 8.2-.71 17.51-8.92 20.8zm314.06 1.14c-4.179 0 4.086-2.709-111.59 41.78-.141-.234-77.388 29.589-77.257 29.538-5.69 2.189-6.909 9.647-2.262 13.593 48.686 41.344 38.574 118.94-19.023 146.514-4.3 2.059-2.859 8.567 1.908 8.575 13.287.022 26.037-3.221 37.184-9.04 0 0 225.772-92.439 228.95-95.76 48.334-50.675 12.124-135.2-57.91-135.2z"/></g></svg>
                    <span>Tickets | Servicios</span>
                </li>
            </a>
            <a href="mis-precios/">
                <li <? if($_GET['p']==6){?>class="sel"<? } ?>>
                    <i class="fa fa-tag"></i>
                    <span>Tarifas</span>
                </li>
            </a>
        
        <?php else: ?>

            <a href="<?=$urlfriendly['url.favorites']?>"><li <? if(!isset($_GET['p']) || $_GET['p']==2){?>class="sel"<? } ?>><i class="fa fa-heart" aria-hidden="true"></i> <span>Favoritos (<?= isset($_COOKIE['fav']) ? count($_COOKIE['fav']) : '0'?>)</span></li></a>
            <a href="mis-mensajes/"><li <? if($_GET['p']==3){?>class="sel"<? } ?>><i class="fa fa-comments" aria-hidden="true"></i> <span>Mensajes (<?=$tot_messages?>)</span></li></a>
            <a href="<?=$urlfriendly['url.my_account']?>">
                <li <? if($_GET['p']==1){?>class="sel"<? } ?>><i class="fa fa-user" aria-hidden="true"></i> <span><?=$language['account.my_data']?></span>
                </li>
            </a>
        

        <?php endif ?>

    </ul>
</div>
<?php if($_SESSION['data']['rol'] != UserRole::Visitante): ?>
<div class="nav_account nav_account_responsive d-block d-md-none">
    <ul>
        <?php if(isset($_GET['p'])): ?>
          
            <a href="javascript:void(0)">   
                <li class="ml-md-2 sel">
                    <?php if($_GET['p']==1): ?>
                        <i class="fa fa-user"></i>
                    <?php endif ?>
                    <?php if($_GET['p']==2): ?>
                        <i class="fa fa-heart"></i>
                    <?php endif ?>
                    <?php if($_GET['p']==3): ?>
                        <i class="fa fa-comments"></i>
                    <?php endif ?>
                    <?php if($_GET['p']==4): ?>
                        <i class="fa fa-tag"></i>
                    <?php endif ?>
                    <?php if($_GET['p']==5): ?>
                        <i class="fa fa-coins"></i>
                    <?php endif ?>
                    <?php if($_GET['p']==6): ?>
                        <i class="fa fa-tags"></i>
                    <?php endif ?>
                </li>
            </a>
            <?php else: ?>
            <a href="javascript:void(0)">   
                <li class="ml-md-2 sel">
                    <i class="fa fa-list"></i>
                </li>
            </a>
        <?php endif ?>
        <?php if(!isset($_GET['p'])): ?>
          
            <a href="javascript:void(0)">
                <li class="ml-md-2 main" ><i class="fa fa-coins" aria-hidden="true"></i> Créditos <span class="credits"><?= $_SESSION['data']['credits'] ?></span></li>
            </a>
            <a href="comprar-creditos/">
                <li class="main" ><i class="fa fa-plus-circle" aria-hidden="true"></i> <span>Recargar Créditos</span></li>
            </a>
        <?php endif ?>
            
        
    </ul>
</div>
<?php endif ?>


<div class="col_single account">
<?

if(!isset($_GET['p'])){
    if($_SESSION['data']['rol'] != UserRole::Visitante)
	    include("my_items.php");
    else
        include("favorites.php");

}else{
	if($_GET['p']==1) include("account_data.php");
	if($_GET['p']==2) include("favorites.php");
	if($_GET['p']==3) include("my_messages.php");
	if($_GET['p']==4) include("my_tickets.php");
	if($_GET['p']==5) include("buy_credits.php");
	if($_GET['p']==6) include("pricing.php");
}

 loadBlock("payment_dialog");
 loadBlock("datajson");
?>

</div>