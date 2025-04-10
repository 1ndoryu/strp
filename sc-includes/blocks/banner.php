<?php
    global $premium_ads;
    $position = $data['position'];
    $size = $data['size'];
    $parent_cat = $data['parent_cat'];

    $banners = getBanners($size, $position, $parent_cat);
?>

<div style='clear:both;'></div>
<?php if(count($banners) > 1): ?>

    <div class="glide banner-slider" id="banner_<?=$position == BannerPosition::Top ? 'top' : 'bottom'?>">
        <div class="glide__track" data-glide-el="track">
            <ul class="glide__slides">
                <?php foreach ($banners as $key => $value): ?>
                    <li class="glide__slide">
                        <a href="<?=$value['url'] == '' ? 'javascript:void(0);' : '' ?>"  <?= $value['url'] != '' ? 'target="_blank"' : '' ?> >
                            <div class="banner_list" style="background: url('<?=$value['code']?>') no-repeat center center;"></div>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="glide__arrows" data-glide-el="controls">
            <button class="glide__arrow glide__arrow--prev" aria-label="anterior" data-glide-dir="<">
                <i class="fa fa-chevron-left"></i>
            </button>
            <button class="glide__arrow glide__arrow--next" aria-label="siguiente" data-glide-dir=">">
                <i class="fa fa-chevron-right"></i>
            </button> 
        </div>
    </div>

    <?php if($position == BannerPosition::Top): ?>
        <script>
            var top_slider = new Glide('#banner_top', {
                type: 'carousel',
                autoplay: 3000,
                perView: 1,
                gap: 0,
                controls: false,
                breakpoints: {
                    1000: {
                        perView: 1,
                        controls: true
                    }
                }
            });
            top_slider.mount();
        </script>
    <?php elseif($position == BannerPosition::Bottom): ?>
        <script>
            var bottom_slider = new Glide('#banner_bottom', {
                type: 'carousel',
                autoplay: 3000,
                perView: 1,
                gap: 0,
                controls: false,
                breakpoints: {
                    1000: {
                        perView: 1,
                        controls: true
                    }
                }
            });
            bottom_slider.mount();
        </script>
    <?php endif ?>
       


<?php elseif(count($banners) == 1): 
    
?>
     
    <a href="<?=$banners[0]['url'] == '' ? 'javascript:void(0);' : '' ?>"  <?= $banners[0]['url'] != '' ? 'target="_blank"' : '' ?> >
        <div class="banner_list" style="background: url('<?=$banners[0]['code']?>') no-repeat center center;"></div>
    </a>

<?php else: ?>
    <?php if(!isset($premium_ads) || count($premium_ads)==0): ?>
        <div class='banner-no-image'></div>
    <?php endif ?>
<?php endif ?>