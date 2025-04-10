<div class="header-banner">
    <?php 
        if(isMobileDevice())
            $banner = getConfParam("HEADER_BANNER_R");
        else
            $banner = getConfParam("HEADER_BANNER") ;
        $time = time();
    ?>
    <?php if($banner != ""): ?>
        <img 
        <?php if(isMobileDevice()): ?>
            width="400" height="75"
        <?php endif ?>
        src="<?=getConfParam('SITE_URL') ?><?= IMG_BANNERS ?><?=$banner?>?t=<?=$time?>" alt="header-banner">
    <?php endif ?>

</div>