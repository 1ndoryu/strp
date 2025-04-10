<?

    $exito_div=false;
    $BOLSA_ID = getBolsaID();
    $paypal_configuration=selectSQL("sc_paypal",$w=array('ID_paypal'=>1));
    $paypal_currency_codes=selectSQL("sc_currency_code",$w=array('ID_currency'=>$paypal_configuration[0]['ID_currency_code']));
    $plan_times = selectSQL("sc_time_options", $a = array('owner' => 'PREMIUM'));
    loadModule('items');
    $parent_cats = selectSQL("sc_category", $a = array('parent_cat' => "-1"));

    $exito_div=Items::catch();

    if(isset($_POST['del']))
    {

        $ad_del = array('trash' => 1);
        $ad_del['motivo'] = $_POST['motivo'];
        if($_POST['motivo'] == Motivo::Cancelado)
            $ad_del['trash_comment'] = $_POST['comment_des'];
        else
            $ad_del['trash_comment'] = $_POST['comment'];
        $ad_del['date_trash'] = time();
        updateSQL('sc_ad', $ad_del, $w = array('ID_ad' => $_POST['del']));
        if($_POST['motivo'] == Motivo::Cancelado)
        {
            $exito_div=$language_admin['manage_item.ad_deleted'];   
            mailNoAproveAd($_POST['del']);
        }
        elseif($_POST['motivo'] == Motivo::INCUMPLIMIENTO || $_POST['motivo'] == Motivo::Repetido)
            mailNoAproveAd($_POST['del']);
        elseif($_POST['motivo'] != Motivo::SIN_AVISO)
        {
            mailDeleteAd($_POST['del']);
        }
        //deleteAdRoot($_GET['del']);
        $exito_div=$language_admin['manage_item.ad_deleted'];
    }
    if(isset($_GET['re'])){
    updateSQL("sc_ad",$da=array('date_ad'=>time(), 'renovate' => 1),$w=array('ID_ad'=>$_GET['re']));
    $exito_div=$language_admin['manage_item.ad_renoved'];
    }
    if(isset($_GET['valid'])){
        $ad_valid=getDataAd($_GET['valid']);
        if($ad_valid['ad']['review'] != 0)
        {
            if(!validateChanges($_GET['valid']))
            {
                $new_ref = getNewRef();
                $da=array('review'=>0, 'delay' => 0, "date_ad"=>time(), 'ref' => $new_ref);
                if($ad_valid['ad']['renovable'] == renovationType::Diario)
                {
                    $limit = $ad_valid['user']['renovate_limit'];
                    if($limit != 0)
                    {
                        $da['renovable_limit'] = $limit;
                    }else
                        $da['renovable_limit'] = time() + 3600 * 24 * 30;
                }
                updateSQL("sc_ad",$da,$w=array('ID_ad'=>$_GET['valid']));
                mailNewAd($_GET['valid']);
            }
            
            //Notice::addNotice($ad_valid['user']['ID_user'],  "El anuncio de ".'"'. $ad_valid['ad']['title'] .'"' . " ha sido validado", urlAd($ad_valid['ad']['ID_ad']), null);
            
            $exito_div=$language_admin['manage_item.ad_actived'];

        }


    }
    if(isset($_GET['dl']) && isset($_GET['frec'])){
    // @$time_des=time() + $paypal_configuration[0]['time_2']*24*3600;
        Service::premium2($_GET['dl'], $_GET['frec'], $_GET['night']);
   
        $exito_div=$language_admin['manage_item.ad_premium2'];
    }
    if(isset($_GET['dp']) && isset($_GET['value'])){
    
        if(Service::premium1($_GET['dp'], $_GET['value']))
            $exito_div=$language_admin['manage_item.ad_premium1'];
    }
    if(isset($_GET['nl'])){
        
    updateSQL("sc_ad",$da=array('premium2_frecuency'=>0),$w=array('ID_ad'=>$_GET['nl']));
    Service::inactiveByAd($_GET['nl'], 'premium2');
    $exito_div=$language_admin['manage_item.ad_not_premium2'];
    }
    if(isset($_GET['np'])){
    updateSQL("sc_ad",$da=array('premium1'=>0,'date_premium1'=>0),$w=array('ID_ad'=>$_GET['np']));
    Service::inactiveByAd($_GET['np'], 'premium1');
    $exito_div=$language_admin['manage_item.ad_not_premium1'];
    }
    
    if(isset($_POST['actions_group_submit'])){
        $tot=0;
        $action_to_do=$_POST['action_do'];
        if($action_to_do==1){
            foreach($_POST['item'] as $n => $value){
                deleteAdRoot($value);
                $tot++;
            }
            $exito_div=$tot.$language_admin['manage_item.ads_deleted'];
        }
        if($action_to_do==2){
            foreach($_POST['item'] as $n => $value){
                updateSQL("sc_ad",$da=array('date_ad'=>time()),$w=array('ID_ad'=>$value));
                $tot++;
            }
            $exito_div=$tot.$language_admin['manage_item.ads_renoved'];
        }
        if($action_to_do==3){
            $datetime = $_POST['datetime'];
            $data = array('ads' => array());
            foreach($_POST['item'] as $n => $value)
            {
                $ad_validate=getDataAd($value);
                if($ad_validate['ad']['review']!=0 && $ad_validate['ad']['delay']==0){
                    $data['ads'][] = $ad_validate['ad']['ID_ad'];
                    $tot++;
                    if($datetime != "")
                        updateSQL("sc_ad",$da=array('delay'=>1),$w=array('ID_ad'=>$value));
                    else
                    {
                        if(!validateChanges($value))
                        {
                            $new_ref = getNewRef();
                            updateSQL("sc_ad",$da=array(
                                'review'=>0,
                                'ref' => $new_ref
                            ),$w=array('ID_ad'=>$value));
                            mailNewAd($value);
                        }
                        
                    }
                    
                }
            }

            if($tot > 0 && $datetime != "")
                Events::addEvent('validar', $datetime, $data);

            $exito_div=$tot.$language_admin['manage_item.ads_actived'];
        }
    }
?>
<?
    $order = " date_ad DESC limit ";

    $filter=array('trash' => 0);
    if(isset($_GET['user'])){
        $filter['ID_user']=$_GET['user'];
    }
    if(isset($_GET['validate'])){
        $filter['review']= "0!=";
    }
    if(isset($_GET['premium'])){
        $filter['premium1']=1;
        $order = " date_premium1 DESC limit ";
    }
    if(isset($_GET['premium3'])){
        $filter['premium3']=1;
        $order = " date_premium1 DESC limit ";
    }

    if(isset($_GET['banners'])){
        $filter['ID_banner'] = "0!=";
    }

    if(isset($_GET['cat']) && $_GET['cat'] != 0){
        $filter['parent_cat'] = $_GET['cat'];
    }

    if(isset($_GET['q']) && isset($_GET['field'])){
        switch ($_GET['field']) {
            case 'ID_ad':
                $filter['ref'] = $_GET['q'];
                break;
            case 'mail':
                $w = array();
                $w[$_GET['field']]=trim($_GET['q'])."%";
                $usuarios=selectSQL("sc_user",$w);
                $fil_val = array();
                foreach ($usuarios as $key => $value) {
                    $fil_val[$key] = strval($value['ID_user']);
                }
                break;
            case 'title':
                $buscar = array('title' => trim($_GET['q']), 'texto' => trim($_GET['q']));
                break;
            default:
                # code...
                break;
        }
    }
    // VARIABLES PARA PAGINACION //
    $TAMANO_PAGINA = getConfParam('ITEM_PER_PAGE'); 
    $pagina = $_GET["pag"]; 
    if (!$pagina){ 
        $inicio = 0; 
        $pagina=1; 
    } else { 
        $inicio = ($pagina - 1) * $TAMANO_PAGINA; 
    }
    // ------------------------- //
    $orden_comun = $order . $inicio . "," . $TAMANO_PAGINA . "";

    if($_GET['field'] == 'mail')
        $result = selectSomeSQL('sc_ad',"ID_user", $fil_val, $orden_comun );
    else if ($_GET['field'] == 'title') 
        $result = selectSQL('sc_ad', $filter, $orden_comun, $buscar);
    else
        $result = selectSQL("sc_ad",$filter,$orden_comun);
 
    $num_total_registros = countSQL("sc_ad",$filter,"");
    $total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
    if($num_total_registros <$MAX_PAG){ 
        if($num_total_registros == 0){$inn = 0;}
        else{$inn=1;}
        $por_pagina = $num_total_registros;
    }else{ 
        $inn = 1+($MAX_PAG*($pagina-1));
        if($pagina == $total_paginas){ $por_pagina=$num_total_registros; }else{$por_pagina = $MAX_PAG*($pagina);}
    }

    // send mail
    if(isset($_POST['para']) && isset($_POST['asunto'])){
        $mail = $_POST['para'];
        $asunt = $_POST['asunto'];
        $titulo = $_POST['titulo'];
        $content = $_POST['contenido'];
        $id_ad = $_POST['id_Ad'];
        $content = toHtml($content);
        $content = str_replace('<p>','<p style="font-family:Arial;">', $content);
        if(SendMail($asunt, $content, $mail, $titulo, $id_ad))
            $exito_div = "Correo Enviado";
    
    }


?>


<h2><?=$language_admin['manage_item.title_h1']?></h2>
<form action="index.php" method="get" class="addCat">
    <label>Buscar Anuncio</label>
    <input name="q" type="text" value="<?php if(isset($_GET['q'])) print $_GET['q'];?>">
    <?php if( isset($_GET['field'] ) ): ?>
        <select name="field">
            <option value="ID_ad" <? if($_GET['field']=="ID_ad") echo "selected";?> >Ref Anuncio</option>
            <option value="mail" <? if($_GET['field']=="mail") echo "selected";?> ><?=$language_admin['manage_user.mail']?></option>
            <option value="title"<? if($_GET['field']=="title") echo "selected";?> >Por Palabras</option>
        </select>
    <?php else : ?>
        <select name="field">
            <option value="ID_ad" >Ref Anuncio</option>
            <option value="mail" ><?=$language_admin['manage_user.mail']?></option>
            <option value="title" selected >Por Palabras</option>
        </select>
    <?php endif ?>
    <select name="cat" >
        <option value="0">Todas las categorías</option>
        <?php foreach ($parent_cats as $key => $value): ?>
            <option value="<?=$value['ID_cat']?>" <? if($_GET['cat']==$value['ID_cat']) echo "selected";?> ><?=$value['name']?> (<?=countParentCategory($value['ID_cat'])?>)</option>
        <?php endforeach; ?>
    </select>
    <?if(isset($_GET['validate'])):?>
        <input name="validate" type="hidden" value="" >
    <?endif?>
    <?if(isset($_GET['premium'])):?>
        <input name="premium" type="hidden" value="" >
    <?endif?>
    <?if(isset($_GET['premium3'])):?>
        <input name="premium3" type="hidden" value="" >
    <?endif?>
        
    <input name="Buscar" type="submit" value="Buscar Anuncio" class="button_form">
    <input type="hidden" name="id" value="manage_items">
</form>
<!-- <span class="h2"><? echo $inn; ?>-<? echo $por_pagina; ?><?=$language_admin['manage_item.of']?><strong><? echo $num_total_registros; ?></strong> <?=$language_admin['manage_item.ads']?></span> -->
<span class="h2">Total de anuncios: <?=$num_total_registros?> </span>
<? if($exito_div!==FALSE){?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? }?>
<div class="filter_list_ad">
<a href="index.php?id=manage_items"<? if(!isset($_GET['validate']) && !isset($_GET['premium']) && !isset($_GET['banners']) ) echo ' class="sel"';?>><?=$language_admin['manage_item.all_ads']?></a>
<a href="index.php?id=manage_items&validate"<? if(isset($_GET['validate'])) echo ' class="sel"';?>><?=$language_admin['manage_item.ads_to_review']?></a>
<a href="index.php?id=manage_items&premium"<? if(isset($_GET['premium'])) echo ' class="sel"';?>>Destacados Premium</a>
<a href="index.php?id=manage_items&banners"<? if(isset($_GET['banners'])) echo ' class="sel"';?>>Destacados Banner</a>
<a href="index.php?id=manage_items&premium3"<? if(isset($_GET['premium3'])) echo ' class="sel"';?>>Anuncios destacados</a>

</div>
<div class="d-flex px-4" style="align-items: center;">
<input type="checkbox" class="items_check">
<label><?=$language_admin['manage_item.multiple_choices']?></label>
</div>
<!--<div class="my_items_info">
<span class="check"><input type="checkbox" class="items_check"></span>
<span class="photo"><?=$language_admin['manage_item.col_photo']?></span>
<span class="info"><?=$language_admin['manage_item.col_info']?></span>
<span class="options"><?=$language_admin['manage_item.col_opt']?></span>
</div>-->
<form id="actions_group" method="post" action="<? $_SERVER['PHP_SELF']; ?>">
<ul class="my_items_list">
<?
    for($i=0;$i<count($result);$i++){
    $image=false;
    $ad=getDataAd($result[$i]['ID_ad']);
    
    $ad = parseChanges($ad);
    if(count($ad['images'])!=0) $image=true;
    
    $adult = 0;
    $name = $ad['parent_cat']['name'];
    if ($name == 'Contactos') $adult = 1;
    $imgvertical =false;
	if($image){
		$imagezise = getimagesize('../'. IMG_ADS . $ad['images'][0]['name_image']) ;
		if(($imagezise[0] + 50) < $imagezise[1])
			$imgvertical = true;
	}
	// verifica si la imagen es vertical

?>
<li>

    <div class="mail_info ">
        <div>
            <span style="float: left;"><strong>Ref. <span class="r-hide">del anuncio</span> <?=$ad['ad']['ref'];?></strong></span>
            <div class="item-active-services">
				
                <?php if($ad['ad']['premium1'] == 1):
					
					?>
                    <div class="active-service" title="Exp: <?= date('d.m h:i', $ad['ad']['date_premium1'])?>" >
                        TOP
                        
                    </div>
				<?php endif ?>
				<?php if($ad['ad']['premium2'] == 1): ?>
					
				<?php endif ?>
				<?php if($ad['ad']['premium3'] == 1): ?>
					<div class="active-service" title="Exp: <?= date('d.m h:i', $ad['ad']['date_premium3'])?>">
						Destacado
						
					</div>
				<?php endif ?>
				<?php if($ad['ad']['renovable'] == renovationType::Autorenueva): ?>
					<div class="active-service" title="Exp: <?= date('d.m h:i', $ad['ad']['renovable_limit'])?>" >
					Autosubida
				
					</div>
				<?php endif ?>
				<?php if($ad['ad']['renovable'] == renovationType::Autodiario): ?>
					<div class="active-service" title="Exp: <?= date('d.m h:i', $ad['ad']['renovable_limit'])?>" >
						Diario Automatico
					
					</div>
				<?php endif ?>
				<?php if($ad['ad']['renovable'] == renovationType::Diario): ?>
					<div class="active-service" title="Exp: <?= date('d.m h:i', $ad['ad']['renovable_limit'])?>" >
                        Extra Diario 
					</div>
				<?php endif ?>
                <?php if($ad['ad']['review']==1 && $ad['ad']['repeat'] != 0): ?>
                
                    <a target="_blank" class="repeat" href="<?=urlAd($ad['ad']['repeat'])?>" target="_blank">
                        <i class="fa fa-exclamation-circle"></i>
                        <?php if($ad['ad']['trash_comment'] != ""): ?>
                            <?=$ad['ad']['trash_comment']?>
                        <?php else: ?>
                            Posible repetido
                        <?php endif ?>
                    </a>
                
                <?php endif ?>
                <?php if($ad['ad']['review'] == 0 && $ad['ad']['ID_order'] != 0): ?>
                    <div class="active-service pending" title="Pago pendiente">
						Pago pendiente
					</div>
                <?php endif ?>
			</div>
        </div>
        <span class="pr-3"><span class="r-hide">Teléfono:</span> <?= $ad['user']['phone'] ?></span>
        <span><?=$ad['user']['mail'];?></span>
        <a class="send_mail" onclick="SetMailer('<?=$ad['user']['mail']?>', '<?=stripslashes($ad['ad']['title'])?>', <?=$ad['ad']['ID_user']?>)" ><i class="fa fa-envelope"></i></a>
    </div>

    <div class="info_item_row">
    <div class="check_item"><input type="checkbox" name="item[]" class="item_check" value="<?=$ad['ad']['ID_ad']?>"></div>
    <div class="image_my_item">
        
        <div class="img-item <?= $imgvertical ? 'vertical' : 'horizontal' ?>">
            <?php if($ad['ad']['parent_cat'] == $BOLSA_ID): ?>
                    
                    <a  href="/<?=URL($ad)?>" target="_blank" title="<?=stripslashes($ad['ad']['title'])?>">
                        <img src='<?=Images::getBolsaImg()?>'/>
                    </a>
                <?php else: ?>
         
                    <a  href="/<?=URL($ad)?>" target="_blank" title="<?=stripslashes($ad['ad']['title'])?>">
                        <img src='<?=Images::getImage($ad['images'][0]['name_image'], IMG_ADS, true, $ad['images'][0]['edit']);?>'/>
                    </a>
            <?php endif ?>
        </div>

        <div class="min-img-item">
            <?php foreach( $ad['images'] as $key => $value ):
                 if($key == 0)
                     continue;
                list($width, $height) = Images::getImageSize($value['name_image']);
                $path = Images::getImage($value['name_image'], IMG_ADS, true, $value['edit']);
            ?>
                <div class="item-min">
                    <?php if(count($ad['images']) > 1): ?>
                        <i class="fa fa-times-circle" data-id="<?=$value['ID_image']?>"></i>
                    <?php endif ?>
                    <a href="<?=$path?>" data-pswp-width="400" data-pswp-height="509">
                        <img src="<?=$path?>" >
                    </a>
                </div>
            
             <?php endforeach ?>
        </div>
    
    </div>
    <div class="info_my_item panel">
        <span class="titleAd"><a target="_blank" href="<?=URL($ad)?>" title="<?=stripslashes($ad['ad']['title'])?>"><?=stripslashes($ad['ad']['title'])?></a></span>
        <span class="zoneAd"><?=$ad['category']['name'];?><?=$language_admin['manage_item.in']?><?=$ad['city']['name'];?> <?=$ad['region']['name'];?></span>
        <span class="text2Ad w-100">
            <span class="short">
                    <?
                        echo mb_strimwidth($ad['ad']['texto'], 0, 220, "...");
                        //echo $ad['ad']['texto'];
                    ?>
            </span>
            <span class="open hidden">
                    <?
                        //echo mb_strimwidth($ad['ad']['texto'], 0, 220, "...");
                        echo $ad['ad']['texto'];
                    ?>
            </span>
            <?php if(strlen($ad['ad']['texto']) > 220): ?>
                <a href="javascript:void(0);" class="moreAd" onclick="showText(this);">
                    ver más
                </a>
            <?php endif ?>
        </span>
        <span class="dateAd"><?=$language_admin['manage_item.post_since']?><?=timeSince($ad['ad']['date_ad'], false);?></span>
        <!-- <span class="priceAd"><? if($ad['ad']['price']>0) echo formatPrice($ad['ad']['price']); else echo $language_admin['manage_item.no_price'];?></span> -->
        
    </div>
    <div class="visits_my_item">
        <b class="visits">
            <i><?=$ad['ad']['visit']?></i>
            visitas
        </b>
    </div>
    <div class="options_my_item">
        <ul>
            <?php if($ad['ad']['review']!=0): ?>
                <li>
                    <?php 
                        $score = Images::getMaxScore($ad['images']); 
                        $color = "blue";
                        if($score > 40)
                            $color = "orange";
                        if($score > 50)
                            $color = "red";
                    ?>

                </li>
            <?php endif ?>


        
        <!--Campos de anuncio validado -->
        <? if($ad['ad']['review'] == 0){ ?>
            <? if($ad['ad']['premium1']!=1){ ?>
            <li><a onclick="show_menu_premium(<?=$ad['ad']['ID_ad']?>)">Top anuncio</a></li>
            <? }else{?>
            <li>
                <span onclick="extenderPlazo(<?=$ad['ad']['ID_ad']?>, 'Top')" class="exp_date extend">Exp: <?= date('d.m h:i', $ad['ad']['date_premium1'])?></span>
                <a class="disable_renove" href="<?=getPagParam('np',$ad['ad']['ID_ad']);?>">Desactivar Top</a>
            </li>
            <? } ?>
            <? if($ad['ad']['premium2']==1 && $ad['ad']['premium2_frecuency'] != 0){ ?>
                <li id="p2_<?=$ad['ad']['ID_ad']?>"><a href="<?=getPagParam('nl',$ad['ad']['ID_ad']);?>"><?=$language_admin['manage_item.opt_not_premium2']?></a></li>
            <? }else{?>
                <li><a href="javascript:void(0);" onclick="show_menu_listing(<?=$ad['ad']['ID_ad']?>)"><?=$language_admin['manage_item.opt_premium2']?></a></li>
            <? } ?>

            <? if($ad['ad']['premium3']==1){ ?>
                <span onclick="extenderPlazo(<?=$ad['ad']['ID_ad']?>, 'Destacado')" class="exp_date extend">Exp: <?= date('d.m h:i', $ad['ad']['date_premium3'])?></span>
                <li id="p2_<?=$ad['ad']['ID_ad']?>"><a class="disable_renove" href="<?=getPagParam('del_destacar',$ad['ad']['ID_ad']);?>">Eliminar Destacado</a></li>
            <? }else{?>
                <li><a href="javascript:void(0);" onclick="show_menu_destacado(<?=$ad['ad']['ID_ad']?>)">Destacar Anuncio</a></li>
            <? } ?>

            <?php if($ad['ad']['renovable'] == renovationType::Autorenueva): ?>
                <li>
                    <span onclick="extenderPlazo(<?=$ad['ad']['ID_ad']?>, 'Autorenueva')" class="exp_date extend">Exp: <?= date('d.m h:i', $ad['ad']['renovable_limit'])?></span>
                    <a class="disable_renove" href="<?=getPagParam('del_autorenueva',$ad['ad']['ID_ad']);?>" >Eliminar Autorenueva</a>
                </li>
            <?php else: ?>
                <li>
                    <a href="javascript:void(0);" onclick="show_menu_autosubida(<?=$ad['ad']['ID_ad']?>)">Autorenueva</a>
                </li>
            <?php endif ?>


            <li><a target="_blank" href="index.php?id=manage_items&veren=<?=$ad['ad']['ID_ad']?>">Ver en pagina</a></li>
            
        <? } ?>

        <!--<li id="d_<?=$ad['ad']['ID_ad']?>"><a href="<?=getPagParam('del',$ad['ad']['ID_ad']);?>"><?=$language_admin['manage_item.opt_delete']?></a></li>-->
        </ul>

    </div>
    <div class="options_row">
        <ul>
        <? if($ad['ad']['review']!=0){ ?>
            <?php if($ad['ad']['review']==1): ?>
                <?php if($ad['ad']['delay']==0){ ?>
                    <li><a href="<?=getPagParam('valid',$ad['ad']['ID_ad']);?>" style="background: var(--rosa);"><?=$language_admin['manage_item.opt_validate']?></a></li>
                  <?php } else { ?>
                    <li><a href="javascript:void(0)" style="background: var(--rosa);">Programado</a></li>
                <?php } ?>
            <?php else: ?>
                
                <?php if($ad['ad']['delay']==0){ ?>
                    <li>
                        <a href="<?=getPagParam('valid',$ad['ad']['ID_ad']);?>" style="background: var(--rosa);;">Validar editado</a>
                    </li>
                  <?php } else { ?>
                    <li><a href="javascript:void(0)" style="background: var(--rosa);">Programado</a></li>
                <?php } ?>
                
            <?php endif ?>
        <? }else{ ?>
            <li id="r_<?=$ad['ad']['ID_ad']?>"><a <? if (strpos(timeSince($ad['ad']['date_ad']), 'd') === false) {echo " class=disable_renove onclick=\"alert('Solo es posible renovar anuncio cada 24 horas.');return false;\" href=#>";} else{?> href="<?=getPagParam('re',$ad['ad']['ID_ad']);?>"><? } ?>Renovar</a></li>
            <li id="e_<?=$ad['ad']['ID_ad']?>"><a target="_blank" href="index.php?id=edit_item&a=<?=$ad['ad']['ID_ad']?>"><?=$language_admin['manage_item.opt_edit']?></a></li>
     
            <li >
                <a href="javascript:void(0);" onclick="setModalDesactivar(<?=$ad['ad']['ID_ad']?>)" >Desactivar</a>
            </li>
       <?php } ?>
            <?php if($ad['ad']['review'] != 0){ ?>
                <?php if($ad['ad']['review'] == 1): ?>
                    <li id="d_<?=$ad['ad']['ID_ad']?>">
                        <a href="javascript:void(0);" onclick="setModalDel(<?=$ad['ad']['ID_ad']?>, 1)" >Cancelar</a>
                    </li>
                    <li id="e_<?=$ad['ad']['ID_ad']?>"><a target="_blank" href="index.php?id=edit_item&a=<?=$ad['ad']['ID_ad']?>"><?=$language_admin['manage_item.opt_edit']?></a></li>
                <?php else: ?>
                    <li id="d_<?=$ad['ad']['ID_ad']?>">
                        <a onclick="setDiscard(<?=$ad['ad']['ID_ad']?>)" href="javascript:void(0);">Cancelar cambios</a>
                    </li>
                  
                <?php endif ?>
            <? } ?>
                

            <li id="d_<?=$ad['ad']['ID_ad']?>">
                <a href="javascript:void(0);" onclick="setModalDel(<?=$ad['ad']['ID_ad']?>, <?=$ad['ad']['review'] == 0 ? 0 : -1?>)" ><?=$language_admin['manage_item.opt_delete']?></a>
            </li>
            
        </ul>
    </div>
    </div>
</li>
<?
} // FOR
?>
</ul>
<div class="addCat">
    
    <select name="action_do" id="action_do">
    <option>Seleccionar</option>
    <option value="3"><?=$language_admin['manage_item.multiple_active']?></option>
    <option value="1"><?=$language_admin['manage_item.multiple_delete']?></option>
    <option value="2"><?=$language_admin['manage_item.multiple_renove']?></option>
    </select>

    <div class="hidden-action-filds hidden">
        <input type="datetime-local" name="datetime" id="">
    </div>
    <input type="submit" name="actions_group_submit" id="actions_group_submit" value="<?=$language_admin['manage_item.button_aply']?>" class="button_form">
</div>

</form>

<div class="modal" id="discard-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Descartar cambios</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form  method="post">
        <div class="modal-body">

			  <div class="form-group row" id="delad-comment">
                    <label  class=" col-form-label col-2">Comentario</label>
                    <div class="col-9">
						<textarea name="comment" id="delad-comment" class="form-control"></textarea>
                    </div>
			  </div>
              
			  <input type="hidden" name="discard" id="discard_id" >
		  
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Aplicar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		</div>
		</form>
      </div>
    </div>
</div>
<div class="modal" id="delad-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-trash-alt"></i> <span class="delete-text"></span> Anuncio</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="delad-form" method="post">
        <div class="modal-body">
			  <div class="form-group row">
                    <label class=" col-form-label col-2" for="#delad-motivo">Motivo</label>
                    <div class="col-9">
                        <select name="motivo" id="delad-motivo" required class="form-control">
					        <option value="0">Selecciona</option>
                            <option value="1" >1</option>
                            <option value="2" >2</option>
                            <option value="3" >3</option>
                            <option value="4" >4</option>
                            <option value="5" >5</option>
                            <option value="6" >6</option>
                            <option value="<?=Motivo::SIN_AVISO?>" >Sin aviso</option>
                        </select>
                        <div class="invalid-feedback f-1150">Selecione un motivo</div>
                    </div>
			  </div>
			  <div class="form-group row">
                    <label for="adminedit-mail" class=" col-form-label col-2">fecha</label>
                    <div class="col-9">
						<input type="date" readonly  value="<?= date('Y-m-d"', time()); ?>" id="delad-date" class=" form-control">
                    </div>
			  </div>
			  <div class="form-group row" id="delad-comment">
                    <label  class=" col-form-label col-2">Comentario</label>
                    <div class="col-9">
						<textarea name="comment" id="delad-comment" class="form-control"></textarea>
                    </div>
			  </div>
			  <div class="form-group row" id="desactivar-comment" style="display: none;">
                    <label class=" col-form-label col-2">Comentario</label>
                    <div class="col-9">
                        <select name="comment_des"  class="form-control">
                            <option value="">Selecciona</option>
                            <option value="Edita tu imagen y vuelve a subir tu anuncio." >Edita tu imagen y vuelve a subir tu anuncio.</option>
                            <option value="Edita tus servicios y vuelve a publicar tu anuncio." >Edita tus servicios y vuelve a publicar tu anuncio.</option>
                            <option value="Revisa los textos y vuelve a publicar tu anuncio" >Revisa los textos y vuelve a publicar tu anuncio</option>
                        </select>
                    </div>
			  </div>

              
			  <input type="hidden" name="del" id="delad-id" >
		  
        </div>
        <div class="modal-footer">
          <button type="submit" id="delad-btn" class="btn btn-danger">Aplicar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		</div>
		</form>
      </div>
    </div>
</div>

<div class="modal" id="premium-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-plus"></i> Destacar Anuncio</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="delad-form" method="get">
        <div class="modal-body">
			  <div class="form-group row">
                    <label class="col-form-label col-2" for="#delad-motivo">Duración</label>
                    <div class="col-9">
                        <select name="value" class="form-control" >
                            <option selected value="0">Selecciona</option>
                            <?
                                for ($j = 0; $j < count($plan_times); $j++) {?>
                                    <option value="<?= $plan_times[$j]['value'] ?>">
                                        <?= $plan_times[$j]['quantity'] ?> <?= $plan_times[$j]['time_unit'] ?>
                                        
                                    </option>
                            <? } ?>
                        </select>
                    </div>
			  </div>

              <input type="hidden" name="id" value="manage_items">
			  <input type="hidden" name="dp" id="premium_id">

        </div>
        <div class="modal-footer">
          <button type="submit" id="delad-btn" class="btn btn-success">Aceptar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
		</div>
		</form>
      </div>
    </div>
</div>

<div class="modal" id="listado-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-plus"></i> Subir al listado</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="delad-form" method="get">
        <div class="modal-body">
			  <div class="form-group row">
                    <label class="col-form-label col-2" for="#delad-motivo">Frecuencia</label>
                    <div class="col-9">
                        <select name="frec" class="form-control" >
                            <option selected value="0">Selecciona</option>
                            <?
                                $plan_times = selectSQL("sc_time_options", $a = array('owner' => 'LISTING'));
                                for ($j = 0; $j < count($plan_times); $j++) {?>
                                    <option value="<?= $plan_times[$j]['value'] ?>">
                                        Cada <?= $plan_times[$j]['quantity'] == 1 ? '' : $plan_times[$j]['quantity']; ?> <?= $plan_times[$j]['time_unit'] ?>
                                    </option>
                            <? } ?>
                        </select>
                    </div>
			  </div>

              <div class="form-group row">
                  <label for="" class="col-form-label col-3">Noche:</label>
                  <label for="" class="col-form-label col-2">Sí <input type="radio" name="night" value="1"></label>
                  <label for="" class="col-form-label col-2">No <input type="radio" name="night" value="0"></label>

              </div>

              <input type="hidden" name="id" value="manage_items">
			  <input type="hidden" name="dl" id="listado_id">

        </div>
        <div class="modal-footer">
          <button type="submit" id="delad-btn" class="btn btn-success">Aceptar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
		</div>
		</form>
      </div>
    </div>
</div>

<div class="modal" id="extend-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-plus"></i> Extender plazo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="post">
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-form-label col-2">Duración</label>
                    <div class="col-9">
                        <input type="text" name="time" id="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-2">Servicio</label>
                    <div class="col-9">
                        <input type="text" readonly name="service" id="service_extend">
                    </div>
                </div>

                <input type="hidden" name="idad-extend" id="extend_id">
            </div>
            <div class="modal-footer">
                <button type="submit" id="delad-btn" class="btn btn-success">Aplicar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
		</form>
      </div>
    </div>
</div>
<div class="modal" id="autosubida-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-plus"></i> Autosubida</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="get">
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-form-label col-2">Duración</label>
                    <div class="col-9">
                        <select name="tiempo" class="form-control" >
                            <option selected value="0">Selecciona</option>
                            <option value="15">15 días</option>
                            <option value="30">30 días</option>
                        </select>
                    </div>
                </div>


                <input type="hidden" name="id" value="manage_items">
                <input type="hidden" name="autorenueva_id" id="autorenueva_id">

            </div>
            <div class="modal-footer">
                <button type="submit" id="delad-btn" class="btn btn-success">Aceptar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
		</form>
      </div>
    </div>
</div>

<div class="modal" id="destacado-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-plus"></i> Destacar Anuncio</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="delad-form" method="get">
        <div class="modal-body">
			  <div class="form-group row">
                    <label class="col-form-label col-2" for="#delad-motivo">Duración</label>
                    <div class="col-9">
                        <select name="tiempo" class="form-control" >
                            <option selected value="0">Selecciona</option>
                            <?
                                $plan_times = selectSQL("sc_time_options", $a = array('owner' => 'PREMIUM3'));
                                for ($j = 0; $j < count($plan_times); $j++) {?>
                                    <option value="<?= $plan_times[$j]['value'] ?>">
                                        Cada <?= $plan_times[$j]['quantity'] == 1 ? '' : $plan_times[$j]['quantity']; ?> <?= $plan_times[$j]['time_unit'] ?>
                                    </option>
                            <? } ?>
                        </select>
                    </div>
			  </div>


              <input type="hidden" name="id" value="manage_items">
			  <input type="hidden" name="destacar_ad" id="destacado_id">

        </div>
        <div class="modal-footer">
          <button type="submit" id="delad-btn" class="btn btn-success">Aceptar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
		</div>
		</form>
      </div>
    </div>
</div>

<div class="modal" id="modal_desactivar" tabindex="-1" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Desactivar Anuncio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php?id=manage_items" method="post">
                <div class="modal-body">
                    <p>¿Está seguro de que desea desactivar este anuncio?</p>
                    <div class="form-group">
                        <label for="motivo">Motivo</label>
                        <input type="text" name="comment" class="form-control">
                        <input type="hidden" name="desactivar" id="desactivar_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit"  class="btn btn-danger">Desactivar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="modal-mailer" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-envelope mr-2"></i> Enviar Correo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
			<form action="" id="form-mailer" method="post">
				<div class="form-group row">
					<label for="form-mailer-para" class="col-form-label text-center col-2">Para</label>
					<div class="col-9">
						<input type="text" name="para" readonly id="form-mailer-para" class="form-control">
					</div>
				</div>
				<div class="form-group row">
					<label for="form-mailer-asunto" class="col-form-label col-2 text-center">Asunto</label>
					<div class="col-9">
						<input type="text" name="asunto" id="form-mailer-asunto" class="form-control">
						<div class="invalid-feedback">
							Escriba el Asunto
						</div>
					</div>
                </div>
				<div class="form-group">
					<label for="#form-mailer-title" >Titulo</label>
					
						<input type="text" name="titulo" id="form-mailer-title" class="form-control">
						<div class="invalid-feedback">
							Escriba Un Titulo
						</div>
					
				</div>
				<div class="form-group">
					<label for="form-mailer-content">Contenido</label>
					<textarea name="contenido" class="form-control" id="form-mailer-content"></textarea>
					<div class="invalid-feedback">
							Escriba Un Correo
					</div>
                </div>
                <input type="hidden" name="id_Ad" id="form-mailer-id">
			</form>
        </div>
        <div class="modal-footer">
          <button type="submit" id="modal-mailer-btn" class="btn btn-primary">Enviar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
		</div>
		</form>
      </div>
    </div>
</div>


<? 
if($total_paginas != "0"){  ?>
    <div class="pag_buttons">
       
        <?if ($pagina > 1): ?>
            <a href="/sc-admin/index.php?id=manage_items&pag=1" ><i class="fa fa-angle-double-left"></i></button>
            <a href="/sc-admin/index.php?id=manage_items&pag=<?=$pagina-1?>" ><i class="fa fa-angle-left"></i></button>
        <?endif ?>
        <a class="current"><?=$pagina?></button>
        <?if ($pagina < $total_paginas): ?>
            <a href="/sc-admin/index.php?id=manage_items&pag=<?=$pagina+1?>" ><i class="fa fa-angle-right"></i></button>
            <a href="/sc-admin/index.php?id=manage_items&pag=<?=$total_paginas?>"><i class="fa fa-angle-double-right"></i></button>
        <?endif ?>

    </div>
<? 
}
?>

<script src="res/items.js"></script>
<script type="module" src="res/item-image.js" ></script>
<?php if(isset($_GET['validate'])): ?>
  <script>
    checkEvents(0);
  </script>
<?php endif ?>
























