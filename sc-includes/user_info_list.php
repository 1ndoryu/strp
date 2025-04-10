<div class="user_info_list box_white">
    <div class="photo_banner">
    <?
	if($user_ad[0]['banner_img']!==""){
		$avatar=IMG_USER.$user_ad[0]['banner_img'];
	}else{
		$avatar=IMG_PATH."no-user-image.png";
	}
	?>
            <div class="user_image_avatar" style="background:url(<?=getConfParam('SITE_URL')?><?=$avatar?>"></span></div>
    </div>
    <div class="user_details">
    <h3><?=ucfirst($user_ad[0]['name']);?></h3>
    <p>Usuario desde <?=date('d/m/Y' , $user_ad[0]['date_reg'])?></p>
    <span class="tot_item"><?=$total_reg?> anuncios activos</span>
    </div>
    <div class="product_share">
    <a href="https://twitter.com/share" rel="nofollow" target="_blank"><i class="share_twitter"></i></a>
    <a href="http://www.facebook.com/sharer.php?u=<? echo urlencode(getConfParam('SITE_URL').$_SERVER['REQUEST_URI']); ?>" target="_blank"><i class="share_facebook"></i></a>
    <a href="https://plus.google.com/share?url=<? echo urlencode(getConfParam('SITE_URL').$_SERVER['REQUEST_URI']); ?>" target="_blank"><i class="share_google"></i></a>
    </div>
</div>