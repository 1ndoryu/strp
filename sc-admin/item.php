<?php 
    // if(isset($_GET['edited']) && check_login_admin())
    // {
    //     $ad = parseChanges($ad);
    //     if(isset($ad['ad']['photo_name']))
    //         $img = $ad['ad']['photo_name'];
    // }

    require_once ABSPATH . 'sc-includes/php/func/hipervinculos.php';

?>
<div class="item-container">
    <div class="item-row">
        <div class="item-col-left item-lightbox">
            <div class="item-image-container">
                <?php if($ad['ad']['premium1'] == 1): ?>
                    <div class="item-top-label premium">
                        TOP
                        <svg
                            width="22"
                            height="22"
                            viewBox="0 0 6.3500001 6.3500001"
                            version="1.1"
                            id="svg1"
                            xmlns="http://www.w3.org/2000/svg"
                            xmlns:svg="http://www.w3.org/2000/svg">
                            <defs
                                id="defs1" />
                            <g
                                id="layer1">
                                <path
                                style="fill:#ffffff;stroke-width:0.264583"
                                id="path495"
                                d="M 3.6282233,0.0978832 0.89238569,1.721353 -1.8434519,3.3448231 -1.8814992,0.16378334 -1.9195467,-3.0172565 0.85433816,-1.4596866 Z"
                                transform="rotate(-179.19248,2.2480036,1.6438653)" />
                            </g>
                        </svg>
                    </div>
                  
                <?php endif ?>
                <?php $path = Images::getImage($img[0], IMG_ADS, false); 
                list($width, $height) = Images::getImageSize($img[0]); ?>
                <a href="<?=$path?>" data-pswp-width="<?=($width*0.5)?>" data-pswp-height="<?=($height*0.5)?>">
                    <div>
                        <img class="item-image" src="<?=$path?>" alt="<?=$ad['ad']['title'];?>">
                    </div>
                </a>
            </div>
            <div class="item-gallery">
                <?php foreach($img as $key => $i):
                 list($width, $height) = Images::getImageSize($i);
                 $path = Images::getImage($i, IMG_ADS, false);
                    if($key == 0)
                        continue;
                ?>
                <a href="<?=$path?>" data-pswp-width="<?=$width?>" data-pswp-height="<?=$height?>">
                    <div class="item-gallery-box">
                        <div>
                            <img src="<?=$path?>" alt="<?=$ad['ad']['title'];?>">
                        </div>
                    </div>
                </a>
                <?php endforeach;?>
            </div>
        </div>
        <div class="item-col-mid">
            <div>
                <div class="item-info">
                    <h1><?=$ad['ad']['title'];?></h1>
                    <p>
                        <?php if($ad['ad']['location'] != ""): ?>
                           <?=stripslashes($ad['ad']['location']); ?>/<?=$ad['region']['name'];?>
                           <?else: ?>
                            <?=$ad['region']['name'];?>
                        <?php endif ?>
                    </p>
                    <i id="fav-<?=$ad['ad']['ID_ad']?>" class="fav far fa-heart"></i>
                </div>
                <div class="item-text">
                    <?php $text = $ad['ad']['texto']; ?>
                    <?php if(strlen($text) > 150): ?>
                        <p id="text_short">
                            <?=Hipervinculos::crear_hipervinculos(substr($text, 0, 150), $ad['ad']['parent_cat'], $ad['region']['ID_region'])?>...
                        </p>
                        <p id="text_long" style="display: none;">
                            <?=Hipervinculos::crear_hipervinculos($text, $ad['ad']['parent_cat'], $ad['region']['ID_region'])?>
                        </p>
                        <a href="javascript:void(0)" class="text_more" id="text_more">
                            mostrar más
                        </a>
                    <?php else: ?>
                        <p><?=Hipervinculos::crear_hipervinculos($text, $ad['ad']['parent_cat'], $ad['region']['ID_region'])?></p>
                    <?php endif ?>

                </div>
                <div class="item-ref">
                    <span>Ref:</span> <?=$ad['ad']['ref']?>
                </div>
                <div class="item-textfooter">
                    <p>Cuando me llames dime que me has visto en</p>
                    <h6>solomasajistas.com</h6>
                </div>
            </div>

            <div class="item-social">
                <span>Compartir</span>
                <?php 
                    $link = urlAd($ad['ad']['ID_ad']);
                ?>
                
                <a target="_blank" href="<?=getConfParam('SITE_URL').'compartir-email/'.$ad['ad']['ID_ad']?>" class="email">
                    <i class="fa fa-envelope"></i>
                </a>

                <a href="https://api.whatsapp.com/send?text=<?=$link?>" class="whatsapp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>
        <div class="item-col-right">
            <div class="card-name">
                <img height="45" width="45" src="<?=Images::getImage("icon-item.svg");?>" alt="<?=$ad['user']['name'];?>">
                <h3><?=$ad['user']['name']?></h3>
            </div>
            <div class="item-card">

                <div class="item-card-item card-item-main ">
                    <div class="card-col-left ">
                        <a href="tel:+34<?=$ad['ad']['phone']?>">
                            <img src="<?=Images::getImage("icon-phone.svg");?>" alt="Teléfono">
                        </a>
                    </div>
                    <div class="card-col-mid">
                        <a href="tel:+34<?=$ad['ad']['phone']?>">
                            <? $phonee= str_replace(',','',str_replace('.','',$ad['ad']['phone'])); echo wordwrap( $phonee, 3 , '.' , true );?>
                        </a>
                    </div>
                    <div class="card-col-right"></div>
                </div>
                <?php if($ad['ad']['whatsapp'] == 1){ ?>
                    <div class="item-card-item ">
                        <div class="card-col-left ">
                            <?php if(isMobileDevice()): ?>
                                <a href="https://wa.me/34<?=$ad['ad']['phone']?>?text=<?=urlencode('Hola! he visto tu anuncio en Solomasajistas y estoy interesado en tus servicios.')?>" target="_blank">
                                    <img src="<?=Images::getImage("WhatsApp_icon.webp");?>" alt="Whatsapp">
                                </a>
                            <?php else: ?>
                                <a href="https://web.whatsapp.com/send?phone=34<?=$ad['ad']['phone']?>&text=<?=urlencode('Hola! he visto tu anuncio en Solomasajistas y estoy interesado en tus servicios.')?>" target="_blank">
                                    <img src="<?=Images::getImage("WhatsApp_icon.webp");?>" alt="Whatsapp">
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-col-mid">
                            Escribir Whatsapp
                        </div>
                        <div class="card-col-right">
                            
                        </div>
                    </div>
                <?php } ?>
                <div class="item-card-item">
                    <div class="card-col-left">
                        <div class="btn-triangle">
                            <i class="fa fa-envelope"></i>
                        </div>
                    </div>
                    <div class="card-col-mid">
                        <a onclick="$('#contact-modal').modal('show')" href="javascript:void(0)">Enviar mensaje</a>
                    </div>
                    <div class="card-col-right"></div>
                </div>
                <div class="item-card-item">
                    <div class="card-col-left">
                        <img src="<?=Images::getImage("icon-calendar.svg");?>" alt="Calendario">
                    </div>
                    <div class="card-col-mid">
                        <?php 
                            switch($ad['ad']['dis'])
                            {  
                                case 1:
                                    echo "Todos los días";
                                    break;
                                case 2:
                                    echo "Lunes a Viernes";
                                    break;
                                case 3:
                                    echo "Lunes a Sábado";
                                    break;
                                case 4:
                                    echo "Sábados y Domingos";
                                    break;
                                case 0:
                                default:
                                    echo "A consultar";
                                    break;
                            }
                        ?>
                    </div>
                    <div class="card-col-right"></div>
                </div>
                <div class="item-card-item">
                    <div class="card-col-left">
                        <img src="<?=Images::getImage("icon-clock.svg");?>" alt="Calendario">
                    </div>
                    <div class="card-col-mid">
                        <?php if($ad['ad']['hor_start'] != "" && $ad['ad']['hor_end'] != "" && $ad['ad']['hor_start'] != $ad['ad']['hor_end']): ?>
                            <?= $ad['ad']['hor_start'] ?> a <?= $ad['ad']['hor_end'] ?>hs
                        <?php else: ?>
                            A consultar
                        <?php endif ?>
                    </div>
                    <div class="card-col-right"></div>
                </div>

               
                <div class="item-card-item">
                    <div class="card-col-left">
                        <img src="<?=Images::getImage("lang.svg");?>" alt="Calendario">
                    </div>
                    <div class="card-col-mid">
                        <?php if($ad['ad']['lang1'] != 0): ?>
                            <span class="lang">
                                <?=Language::NAME($ad['ad']['lang1'])?>
                            </span>
                        <?php endif ?>
                        <?php if($ad['ad']['lang2'] != 0): ?>
                            <br>
                            <span class="lang">
                                <?=Language::NAME($ad['ad']['lang2'])?>
                            </span>

                        <?php endif ?>
                        <?php if($ad['ad']['lang1'] == 0 && $ad['ad']['lang2'] == 0): ?>
                            A consultar
                        <?php endif ?>
                    </div>
                    <div class="card-col-right"></div>
                </div>
  
            </div>
        </div>
    </div>
    <hr class="item-separator">
    <div class="contact-wrapper">
        <a href="tel:+34<?=$ad['ad']['phone']?>" class="contact-btn contact-phone">
            <img src="<?=Images::getImage("icon-phone.svg");?>" alt="Teléfono">
            <? $phonee= str_replace(',','',str_replace('.','',$ad['ad']['phone'])); echo wordwrap( $phonee, 3 , '.' , true );?>
        </a>
        <?php if($ad['ad']['whatsapp'] == 1): ?>
          
            <a href="https://wa.me/34<?=$ad['ad']['phone']?>?text=<?=urlencode('Hola! he visto tu anuncio en Solomasajistas y estoy interesado en tus servicios.')?>" target="_blank" class="contact-btn contact-whatsapp">
                <img src="<?=Images::getImage("WhatsApp_icon.webp");?>" alt="Whatsapp">
                Whatsapp
            </a>
         <?php else: ?>
            <a href="javascript:void(0)" onclick="$('#contact-modal').modal('show')" class="contact-btn contact-whatsapp">
                <i class="fa fa-envelope mr-2"></i>
                Mensajes
            </a>
        <?php endif ?>
            
    </div>
</div>
<?php
$premium_ads = selectSQL("sc_ad",
$where=array(
    'premium1'=>1,
    'active'=>1,
    'review'=>0,
    'parent_cat'=>$ad['ad']['parent_cat']
)," rand() LIMIT 10");

if(count($premium_ads)>0){
?>

<?php if( count($premium_ads) >= 3 ): ?>
 
    <div class="premiun_container item_premium">
        <div class="splide carousel-list">
            <div class="splide__track" data-glide-el="track">
                <ul class="splide__list">
                <!--<li class="glide__slide">
                    
                </li>-->
                <? 
                        
                        shuffle($premium_ads);
                    //   console_log($premium_ads);
                    //   console_log(count($premium_ads));
                        
                    
                        foreach ($premium_ads as $pma){
                            $ad=getDataAd($pma['ID_ad']);
                            include("item_list_gallery.php");
                        }
                        
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <script>
        var premium_count = <?=count($premium_ads);?>;
    </script>
    <script type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>carousel.js"></script>

<?php else: ?>
    <div class="last_ads premium_ads item_premium">
        <ul class="last_ads_list">
            <? 
                
                shuffle($premium_ads);
            //   console_log($premium_ads);
            //   console_log(count($premium_ads));
                
                $j = 0;
                foreach ($premium_ads as $pma){
                    $ad=getDataAd($pma['ID_ad']);
                    include("item_list_gallery.php");
                    $j++;
                    if($j == 4)
                        break;
                }
                
            ?>
        </ul>
    </div>

<?php endif ?>


<? } ?>



<div class="modal contact-modal" id="contact-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Contactar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="contactEmail"></div>
                <form action="" class="contact-product-form" method="post">
                    <!-- nombre -->
                     <div class="form-group">
                         <input type="text" name="c_name" id="c_name" placeholder="Nombre" <? if(isset($_SESSION[ 'data'][ 'ID_user'])){?>value="<?=formatName($_SESSION['data']['name'])?>"
                            <? } ?>>
                        <div class="error_msg" id="error_c_name">Indica tu nombre</div>
                    </div>
                     <!-- email -->
                    <div class="form-group">
                        
                        <input class="form-control" type="text" name="c_mail" id="c_mail" placeholder="Email" <? if(isset($_SESSION[ 'data'][ 'ID_user'])){?>value="<?=$_SESSION['data']['mail']?>" <? } ?>>
                        <div class="error_msg" id="error_c_mail">Indica tu email</div>
                    </div>
                    <!-- Phone -->
                    <!-- <div class="form-group">
                      
                        <input class="form-control" type="tel" name="c_phone" id="c_phone" placeholder="Telefono" <? if(isset($_SESSION[ 'data'][ 'ID_user'])){?>value="<?=$_SESSION['data']['phone']?>"<? } ?>>

                    </div> -->
                    <!-- Contenido -->
                    <div class="form-group">
                       
                        <textarea class="form-control" name="msg_c" id="msg_c">¡Hola! Me gustaría recibir más información sobre tu anuncio "<?=$ad['ad']['title'];?>"...</textarea>
                        <div class="error_msg" id="error_msg_c">
                            <?=$language['item.error_message']?>
                        </div> 
                    </div>
                    <div class="form-group mt-4">
                        <?=$language['item.info_terms']?>

                    </div>
                    <input name="contact_item_btn" class="btn_large" type="button" id="contact_item_btn" value="<?=$language['item.button_send']?>">
                    <input type="hidden" value="<?=$ad['ad']['ID_ad'];?>" id="id_ad_contact" name="id_ad_contact">
                </form>
            </div>
        </div>

</div>

<script type="module" src="src/js/item.js"></script>