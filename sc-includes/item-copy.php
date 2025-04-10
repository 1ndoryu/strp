<?php 
    if(isset($_GET['edited']) && check_login_admin())
    {
        $ad = parseChanges($ad);
        if(isset($ad['ad']['photo_name']))
            $img = $ad['ad']['photo_name'];
    }

?>

<div class="product_content" itemscope="" itemtype="http://schema.org/Product"><span class="product_name" itemprop="name"><?=stripslashes(ucfirst($ad['ad']['title']))?></span>
    
         <?php
            $category = selectSQL('sc_category', $w = array('ID_cat' => $ad['category']['parent_cat']));
          ?>

    <span class="back-list"><a href="<?=getConfParam('SITE_URL'). $category[0]['name_seo']?>">Volver al listado</a></span>
    <div class="clear"></div>
    <div class="product_left">
    <div class="product_price favorite">
         <i id="fav-<?=$ad['ad']['ID_ad'];?>" class="fav fa-heart<? if(!isset($_COOKIE['fav'][$ad['ad']['ID_ad']])) echo " far"; else print ' fas on'?>" aria-hidden="true"></i>
     </div>
    <?php 
        $image=false;
        $imgvertical = false;
        if($img != 0){
            $imagezise = getimagesize(IMG_ADS . $ad['images'][0]['name_image']) ;
            if(($imagezise[0] + 50) < $imagezise[1])
                $imgvertical = true;
        }
    ?>
        <div class="item_pic_Container <?php if(count($img) == 1) print 'fondo'; if($imgvertical) print 'vertical'; ?>">
            <? if(count($img)>1){?>
                <div id="slides" class="slide-fondo">
                    <div>
                        <div id="main_img" class="fondo <?=getImgOrientation($img[0])?>">
                            <div class="img-fondo" style="background: url(<?=getConfParam('SITE_URL')?><?=IMG_ADS;?><?= $img[0] ?>);">
                            </div>
                            <img itemprop="image" src="<?=getConfParam('SITE_URL')?><?=IMG_ADS;?><? echo $img[0]; ?>" alt="<? echo stripslashes($ad['ad']['titulo']); ?>" title="<? echo stripslashes($ad['ad']['titulo']); ?>" />
                        </div>
                    </div>
                    <?  for($i = 1; $i < count($img); $i++){?>
                        <div>
                            <div class="fondo <?=getImgOrientation($img[$i])?>">
                                <div class="img-fondo" style="background: url(<?=getConfParam('SITE_URL')?><?=IMG_ADS;?><?= $img[$i] ?>);">
                                </div>
                                <img src="<?=getConfParam('SITE_URL')?><?=IMG_ADS;?><?=$img[$i];?>" alt="<?=$language['item.photo']?><? echo $i; ?>" />
                            </div>
                        </div>
                        <? } ?>
                            <a class="slidesjs-previous slidesjs-navigation" href="#" title="Anterior"><i class="fa fa-angle-left" aria-hidden="true"></i></a>
                            <a class="slidesjs-next slidesjs-navigation" href="#" title="Siguiente"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
                </div>
                <? }else{ ?>
                    <div class="img-fondo" style="background: url(<?=getConfParam('SITE_URL')?><?=IMG_ADS;?><?= $img[0] ?>);">
                    </div>
                        <img itemprop="image" src="<?=getConfParam('SITE_URL')?><?=IMG_ADS;?><? echo $img[0]; ?>" id="main_img" alt="<? echo stripslashes($ad['ad']['titulo']); ?>" title="<? echo stripslashes($ad['ad']['titulo']); ?>" />
                    <? } ?>
        </div>
        <div class="special_details <?php if(count($img) > 1) print 'm'?>">
        <? $type_fuel=selectSQL("sc_type_fuel",$w=array('ID_fuel'=>$ad['ad']['fuel']));?>
            <?php if( $ad['category']['field_0']==1 ): ?>
                <?php if( $ad['ad']['date_car'] != '' ): ?>
                    <span class="datail_1">año <?=$ad['ad']['date_car'];?></span>
                <?php endif ?>
                <?php if( $ad['ad']['mileage'] != '' ): ?>
                    <span class="datail_2"><?=number_format($ad['ad']['mileage'],0,',','.');?> km</span>  
                <?php endif ?>
                <?php if( $type_fuel[0]['name'] != '' ): ?>
                    <span class="datail_3"><?=$type_fuel[0]['name'];?></span>
                <?php endif ?>
            <?php elseif( $ad['category']['field_3']==1 ): ?>
                <?php if( $ad['ad']['room'] != '' ): ?>
                    <span class="datail_1">hab <?=$ad['ad']['room'];?></span>
                <?php endif ?>
                <?php if( $ad['ad']['broom'] != '' ): ?>
                    <span class="datail_2">baños <?=$ad['ad']['broom'];?></span>
                <?php endif ?>
                <?php if( $ad['ad']['area'] != '' ): ?>
                    <span class="datail_3"><?=number_format($ad['ad']['area'],0,',','.');?> m<sup>2</sup></span>
                <?php endif ?>
            <?php elseif( $ad['category']['field_2']==1 ): ?>
                <span class="datail_1"><?=number_format($ad['ad']['area'],0,',','.');?> m<sup>2</sup></span>
            <?php endif ?>

        </div>
        <div class="product_description">
            <h3 itemprop="description"><?=nl2br($ad['ad']['texto'])?></h3> 
        </div>
        <div class="description_item"><span>Más detalles</span>
            <ul class="info_item">
                <li>
                    <?=$language['item.category']?><b> <?=$ad['parent_cat']['name'];?></b>
                </li>
                <?php if ($ad['ad']['ad_type'] != 0): ?>
                    <li>
                        Tipo de anuncio 
                        <b>
                            <?php if ($ad['ad']['ad_type'] == 1) {
                                print "Oferta";
                            }elseif ($ad['ad']['ad_type'] == 2) {
                                print "Demanda";
                            } ?>
                        </b>
                    </li>
                <?php endif ?>

                <li>
                    <?=$language['item.subcategory']?><b> <?=$ad['category']['name'];?></b>
                </li>
                <?php if ($ad['ad']['seller_type'] != 0): ?>
                    <li>
                        Tipo de vendedor <b>
                        <?php if($ad['ad']['seller_type'] == 1)
                         print "Particular";
                         elseif ($ad['ad']['seller_type'] == 2) {
                            print "Profecional";
                        } ?></b>
                    </li>
                <?php endif ?>
                
                <li>
                    provincia <b><?=$ad['region']['name'];?></b>
                </li>
                <li>
                    <?=$language['item.price']?>
                        <b <?php if($ad['ad']['price'] == 0) print 'class="consultar"'?>>
                        <?php if( $ad['ad']['price'] != 0): ?>
                            <?=formatPrice($ad['ad']['price'])?>
                        <?php else: ?> 
                            A Consultar
                        <?php endif ?>
                        </b>
                </li>
 
                <li>
                    <?=$language['item.city']?><b> <?=$ad['ad']['location'];?></b>
                </li>
                <!--<li>
                    <?=$language['item.active_since']?><b><?=date('H:i:s d/m/Y',$ad['ad']['date_ad']);?></b>
                </li>-->
                <li>
                    Fecha de Publicacion <b><?= timeSince($ad['ad']['date_ad'], false) ?></b>
                </li>
                <li>
                    <?=$language['item.visits']?><b> <?=$ad['ad']['visit'];?></b>
                </li>
                <li>
                    REF Anuncio <b><?= $ad['ad']['ref'] ?></b>
                </li>
            </ul>
        </div>
        

        <? if(getBanner('72890', '0' , $ad['parent_cat']['ID_cat'] )!=''){?>
            <div class="box_white" style="overflow:hidden">
                <?=getBanner('72890', '0' , $ad['parent_cat']['ID_cat'] )?>
            </div>
            <? } ?>
        
    </div>
    <div class="product_right">
        <div class="contact_item"> <span class="title_contact contact">
            <?php if( $ad['user']['name'] != ''): ?>
                <i class="fa fa-user-alt pl-5 pr-2"></i>
                <?=formatName($ad['user']['name'])?>
                            
            <?php endif ?>
        </span>
            <? if(strlen($ad['ad']['phone'])>6){ ?>
                <span class="title_contact phone">
                    <div id="phone">
                        <span class="phone-icon">
                            <i class="fa fa-phone-alt" aria-hidden="true"></i>
                        </span>
                        <a href="tel:+34<?=$ad['ad']['phone']?>" class="phone-number">
                            <span class="phone-number">
                                <? $phonee= str_replace(',','',str_replace('.','',$ad['ad']['phone'])); echo wordwrap( $phonee, 3 , '.' , true );?>
                                <?php if( $ad['ad']['phone1'] != '' ): ?>
                                    <i class="fa fa-chevron-down"></i>
                                <?php endif ?>
                            </span>
                        </a>
                        <?php if( $ad['ad']['whatsapp'] == 1 ): ?>
                            <?php if(isMobileDevice()): ?>
                                    <a target="_blank" href="https://wa.me/34<?=$ad['ad']['phone']?>?text=Hola%21+he+visto+en+lowcost+tu+anuncio+y+estoy+interesado+en+tus+servicios.">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                <?php else: ?>
                                    
                                    <a target="_blank" href="https://web.whatsapp.com/send?phone=34<?=$ad['ad']['phone']?>&text=Hola%21+he+visto+en+lowcost+tu+anuncio+y+estoy+interesado+en+tus+servicios.">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                    <?php if( $ad['ad']['phone1'] != '' ): ?>
                        <div id="phone1" style="display: none;" >

                            <span class="phone-number">
                                <? $phonee= str_replace(',','',str_replace('.','',$ad['ad']['phone1'])); echo wordwrap( $phonee, 3 , '.' , true );?>
                            </span>
                            <?php if( $ad['ad']['whatsapp1'] == 1 ): ?>
                               <i class="fab fa-whatsapp"></i>
                            <?php endif ?>
                        </div>
                    <?php endif ?>
                </span>
                <span class="item-msg">NO TE OLVIDES DE DECIR QUE HAS VISTO MI ANUNCIO EN ANUNCICLAS</span>
            <? } ?>
                    <div id="contactEmail"></div>
                    <form class="contact-product-form"> 
                        <h5 class="my-2 text-center">Enviar Mensaje</h5> 
                        <label>Tu nombre</label>
                         <input type="text" name="c_name" id="c_name" placeholder="Tu nombre" <? if(isset($_SESSION[ 'data'][ 'ID_user'])){?>value="<?=formatName($_SESSION['data']['name'])?>"
                        
                            <? } ?>>
                                <div class="error_msg" id="error_c_name">Indica tu nombre</div>
                                 <label>Tu Email</label>
                                 <input type="text" name="c_mail" id="c_mail" placeholder="tuemail@email.com" <? if(isset($_SESSION[ 'data'][ 'ID_user'])){?>value="<?=$_SESSION['data']['mail']?>"
                                    <? } ?>>
                                        <div class="error_msg" id="error_c_mail">Indica tu email</div> <label>Tu Teléfono</label> <input type="tel" name="c_phone" id="c_phone" placeholder="666 000 999" <? if(isset($_SESSION[ 'data'][ 'ID_user'])){?>value="<?=$_SESSION['data']['phone']?>"
                                            <? } ?>> 
                                            <textarea name="msg_c" id="msg_c">¡Hola! Me gustaría recibir más información sobre tu anuncio "<?=$ad['ad']['title'];?>"...</textarea>
                                                <div class="error_msg" id="error_msg_c">
                                                    <?=$language['item.error_message']?>
                                                </div> 
                                                <input name="contact_item_btn" class="btn_large" type="button" id="contact_item_btn" value="<?=$language['item.button_send']?>">
                                                <input type="hidden" value="<?=$ad['ad']['ID_ad'];?>" id="id_ad_contact" name="id_ad_contact">
                                                <?=$language['item.info_terms']?>
                    </form>
        </div>
        <!--
        <div class="seller-info"> <span class="user_item_photo" style="background-image:url(<?=getPhotoUser($ad['ad']['ID_user'])?>"></span>
            <a href="usuario/<?=$ad['ad']['ID_user']?>" class="user-link">
                <?=formatName($ad['user']['name'])?>
            </a> <span class="user_item_tot"><?=countSQL("sc_ad",$w=array('ID_user'=>$ad['ad']['ID_user']));?> anuncios activos</span> 
        </div> -->
        <? if(getBanner('300250', '1' , $ad['parent_cat']['ID_cat'] )!=''){?>
            <div class="banner_300250">
                <?=getBanner('300250', '1' , $ad['parent_cat']['ID_cat'])?>
            </div>
            <? } ?>
                <div class="product_share">
                     <span>Comparte este anuncio</span>
                     <div class="text-left">
                        <!--<a href="whatsapp://send?text=<?=urlAd($ad['ad']['ID_ad'])?>" data-action="share/whatsapp/share" rel="nofollow" target="_blank">
                             <i class="share_whatsapp"></i>
                         </a>-->
                         <a class="whatsapp-icon" href="whatsapp://send?text=<?=urlAd($ad['ad']['ID_ad'])?>" data-action="share/whatsapp/share" rel="nofollow" target="_blank">
                             <i class="fab fa-2x fa-whatsapp-square"></i>
                         </a>
                         <a class="mail-icon" href="<?=getConfParam('SITE_URL')?>/compartir-email/<?=$ad['ad']['ID_ad']?>/" data-action="share/whatsapp/share" rel="nofollow" target="_blank">
                             <i class="fa fa-2x fa-envelope"></i>
                         </a>
                     </div>
                    <!--<a href="https://twitter.com/share" rel="nofollow" target="_blank"><i class="share_twitter"></i></a>-->
                    <!--<a href="http://www.facebook.com/sharer.php?u=<?=urlAd($ad['ad']['ID_ad'])?>" target="_blank"><i class="share_facebook"></i></a>-->
                    <!--<a href="https://plus.google.com/share?url=<?=urlAd($ad['ad']['ID_ad'])?>" target="_blank"><i class="share_google"></i></a>-->
                </div>
    </div>
</div>

<? $ad=getDataAd($_GET['i']); ?>
    <div class="option_item_container">
        <h3 class="title_main"><?=$language['item.options_item']?></h3>
        <span class="main-separator" <?=getColor('SEARCHBAR_COLOR')?> ></span>
        <div class="box_white">
            <ul class="options_item">
                <!--<li class="sp1">
                    <a href='<?=$urlfriendly[' url.my_items '].$urlfriendly['url.my_items.edit ']?><?=$ad['ad ']['ID_ad ']?>' title='<?=$language[' item.edit_item ']?>'>
                        <?=$language['item.edit_item']?>
                    </a>
                </li>
                <li class="sp2">
                    <a href='<?=$urlfriendly[' url.premium ']?><?=$ad['ad ']['ID_ad ']?>' title='<?=$language[' item.premium_item ']?>'>
                        <?=$language['item.premium_item']?>
                    </a>
                </li>-->
                <?php if( isset($_SESSION['data']) ): ?>
                    <li class="sp3">
                        <a href="javascript:void(0);" class="fav<? if(isset($_COOKIE['fav'][''.$ad['ad']['ID_ad'].''])) echo " on ";?>" id="fav-<?=$ad['ad']['ID_ad'];?>" title="<?=$language['item.fav_item']?>">
                            <?=$language['item.fav_item']?>
                        </a>
                    </li>    
                <?php else: ?> 
                    <li class="sp3">
                        <a href="javascript:void(0);" class="login" title="<?=$language['item.fav_item']?>">
                            <?=$language['item.fav_item']?>
                        </a>
                    </li>    
                <?php endif ?>
                <li class="sp4">
                    <a href="javascript:window.print()" title="<?=$language['item.print_item']?>">
                        <?=$language['item.print_item']?>
                    </a>
                </li>
                <li class="sp5">
                    <a href="<?=$urlfriendly['url.contact']?>?report=<?=$ad['ad']['ID_ad']?>" title="<?=$language['item.report_item']?>">
                        <?=$language['item.report_item']?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <script type="text/javascript">
        function recaptcha_callback() {
            document.getElementById('contact_item_btn').disabled = false
        }
    </script>