<?php
user::updateSesion();
$limite = check_item_limit();
$ANUNCIO_NUEVO_PREMIUM = false;
$user_registered = false;
$DATAJSON = array();
$DATAJSON['max_photos'] = getConfParam('MAX_PHOTOS_AD');
$DATAJSON['edit'] = 0;
if(getConfParam('POST_ITEM_REG')==1){
	check_login();
}
if(isset($_POST['g-recaptcha-response'])){
    
	$Return = getCaptcha($_POST['g-recaptcha-response']);

    if($Return->success == true && $Return->score > 0.5){
		$en_revision=false;
		if(isset($_POST['category'])){
			if(verifyFormToken('postAdToken',$_POST['token']) || DEBUG){

				$datos_ad=array();
				$datos_ad['ID_cat']=$_POST['category'];
				$category_ad = selectSQL('sc_category',$w=array('ID_cat'=>$datos_ad['ID_cat']));
				$datos_ad['parent_cat']=$category_ad[0]['parent_cat'];
				$datos_ad['ID_region']=$_POST['region'];
				/*$datos_ad['ID_city']=$_POST['city'];*/
				$datos_ad['location']=$_POST['city'];
				$datos_ad['ad_type']=$_POST['ad_type'];
				$datos_ad['ad_type']=$_POST['ad_type'];
				$datos_ad['title']=$_POST['tit'];
				$datos_ad['title_seo']=toAscii($_POST['tit']);
				$datos_ad['texto']=htmlspecialchars($_POST['text']);
				$datos_ad['price']=$_POST['precio']?$_POST['precio']:0;
				$datos_ad['mileage']=$_POST['km_car'];
				$datos_ad['fuel']=$_POST['fuel_car'];
				$datos_ad['date_car']=$_POST['date_car'];
				$datos_ad['area']=$_POST['area'];
				$datos_ad['room']=$_POST['room'];
				$datos_ad['broom']=$_POST['bathroom'];
				$datos_ad['address']=$_POST['city'];
				$datos_ad['name']=formatName($_POST['name']);
				$datos_ad['phone']=$_POST['phone'];
				$datos_ad['whatsapp']=isset($_POST['whatsapp']) ? 1 : 0;
				$datos_ad['phone1']=$_POST['phone1'];
				$datos_ad['whatsapp1']=isset($_POST['whatsapp1']) ? 1 : 0;
				$datos_ad['seller_type']=$_POST['seller_type'];
				$datos_ad['notifications']=$_POST['notifications']?$_POST['notifications']:0;
				$datos_ad['dis']=$_POST['dis'];
				$datos_ad['lang1']=$_POST['lang-1'];
				$datos_ad['lang2']=$_POST['lang-2'];
				$datos_ad['out']=$_POST['out'];
				$datos_ad['hor_start']=$_POST['horario-inicio'];
				$datos_ad['hor_end']=$_POST['horario-final'];
				$datos_ad['ID_order']= $_POST['order'];
				$datos_ad['payment']= isset($_POST['pago']) ? json_encode($_POST['pago']) : "[]";



				if(!isset($_SESSION['data']['ID_user'])){
						$checkUser = selectSQL("sc_user",$a=array('mail'=>$_POST['email']));
						$ip = get_client_ip();
						
						if(count($checkUser) == 0){
							if(!User::check_registered($ip, $_POST['phone']))
							{
								if(isset($_POST['photo_name']))
									$banner_img = Images::copyImage($_POST['photo_name'][0], IMG_USER, IMG_ADS, true);
								else
									$banner_img = "";
								$pass = randomString(6);
								$limit = user::getLimitsByRol($_POST['seller_type']);
								$datos_u = array(
									'name' => formatName($_POST['name']),
									'mail' => $_POST['email'],
									'phone' => $_POST['phone'],
	//								'whatsapp' => $_POST['whatsapp'],
	//								'phone1' => $_POST['phone1'],
	//								'whatsapp' => $_POST['whatsapp1'],
									'banner_img'=> $banner_img,
									'pass' =>$pass,
									'date_reg'=>time(),
									'active'=>1,
									'rol'=>$_POST['seller_type'],
									'date_credits'=> "0",
									'credits'=> "0",
									'IP_user'=>$ip,
									'anun_limit'=>$limit
								);
								$result = insertSQL("sc_user",$datos_u);
								if($result){ 
									$id_user=lastIdSQL();
									//mailWelcome(formatName($_POST['name']),$_POST['email'],$pass);
								}
							}else
							{
								$user_registered = true;
								$id_user = 0;
							}
						}else {
							$id_user = $checkUser[0]['ID_user'];						
						}
				} else {						
					$id_user = $_SESSION['data']['ID_user'];				
				}

				$limite = check_item_limit($id_user);
				if($limite == 0 || $_POST['order'] != 0)
				{
					list($extras, $extra_limit) = User::updateExtras($id_user);
					if($extras)
					{
						$datos_ad['renovable'] = renovationType::Diario;
						$datos_ad['renovable_limit'] = $extra_limit;
					}
			
						
					//rotar iamgenes
					foreach($_POST['photo_name'] as $photo => $name){
						Images::rotateImage($name, $_POST['optImgage'][$photo]['rotation']);
					}

					$datos_ad['ID_user']=$id_user;
					$datos_ad['date_ad']=time();
					if(getConfParam('REVIEW_ITEM')==1){
						$datos_ad['review']=1;
					}
					/// COMPROBACIÓN
					/*$datos_ad['ID_city']!=0*/
					if($datos_ad['ID_region']!=0 && $datos_ad['ad_type']!=0 && $datos_ad['seller_type']!=0 && $datos_ad['ID_cat']!=0 && ($datos_ad['price']=="" || is_numeric($datos_ad['price'])) && $datos_ad['ID_user']>0){
						if($_POST['order'] != 0)
						{
							$order = Orders::getOrderByID($_POST['order']);
							if($order['ID_ad'] == 0)
							{
								$insert=insertSQL("sc_ad",$datos_ad);
								$last_ad = lastIdSQL();
								updateSQL("sc_orders", array("ID_ad" => $last_ad), array("ID_order" => $_POST['order']));
								Statistic::addAnuncioNuevoPremium();
							}else
							{
								$error_insert=true;
								$insert=false;
								$last_ad = $order['ID_ad'];
							}
						}else
						{
							$insert=insertSQL("sc_ad",$datos_ad);
							$last_ad = lastIdSQL();
							Statistic::addAnuncioNuevo();
						}

						if(isset($_POST['photo_name'])){
							foreach($_POST['photo_name'] as $photo => $name){
								updateSQL("sc_images",$data=array('ID_ad'=>$last_ad, 'position'=>$photo, "status"=> 1),$wa=array('name_image'=>$name));
							}
						}

						if($insert){
							checkRepeat($last_ad);
							if (!$datos_ad['notifications']) {
								mailAdNotNotification($last_ad);
							}
							
							//mailNewAd($last_ad);
							if($_POST['order'] != 0)
							header('Location: /publicado?payad=' . $_POST['order']);
							// echo '<script type="text/javascript">
							// 	location.href = "/publicado?payad=' . $_POST['order'] . '";
							// 	</script>';
							else
							echo '<script type="text/javascript">
							location.href = "/publicado";
							</script>';
						}
					}else {
						$error_insert=true;				
					}
					
			}else
			{
				$error_insert=true;
			}
			
			}
		}
    }else{
        echo '<div class="error_msg" id="error_category" style="display: block;">Eres un robot</div><br>';
    }
}

?>

<script src='https://www.google.com/recaptcha/api.js?render=<?php echo SITE_KEY; ?>'></script>	

<?php if(!user::checkLogin() && check_ip()): ?>
	<dialog class="dialog" open >
		<div class="dialog-modal">
			<a style="color: black;" href="/" ><i class="fa-times-circle fa"></i></a>
			<p class="text-underline">Usuario ya registrado</p>
			<p class="mb-3" >Para publicar un anuncio accede a tu cuenta.</p>
			<!-- <p>Destaca tu anuncio con uno de nuestro planes de pago</p> -->
			<button onclick="gotToLogin()" class="payment-btn" >
				Acceder a mi cuenta
			</button>
		</div>
	</dialog>
<?php endif ?>
<ul class="post-help">
	<li class="post-help-item">
		<a target="_blank" href="/ayuda/" >
			Normas de <b>publicación</b>
		</a>
	</li>
	<li class="post-help-item" >
		<a target="_blank" href="/ayuda/" >
			Ayuda
		</a>
	</li>
	<li class="post-help-item" >
		<a target="_blank" href="https://www.solomasajistas.com/blog/" >
			Visita nuestro blog
		</a>
	</li>


</ul>
<h2 class="title">Publica tu anuncio ¡Gratis! <!-- load block post_info --></h2>
<div class="col_single post_item_col">

<? if(isset($en_revision) && $en_revision){?>
<div class="info_post">
<span class="title_one"><?=formatName($datos_ad['name'])?><?=$language['post.subtitle_review']?></span>
<?=$language['post.info_review']?>
</div>
<? }else{?>
<form id="new_item_post" class="fm" method="post" action="<? $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" value="">
<!-- <h2 class="mt-5">Datos del anunciante</h2> -->
<fieldset>

<?php if(isset($_GET['buyExtras'])): ?>
	<dialog class="dialog" open id="extras_msg" >
        <div class="dialog-modal">
            <a href="/mis-anuncios"  style="color: black;">
                <i class="fa-times-circle fa"></i>
            </a>
            <p><strong>¡Tu pago ha sido realizado con éxito!</strong></p>
			<p>Ya puedes publicar o activar tus anuncios.</p>

        </div>
    </dialog>
<?php endif ?>

<?php if(!checkSession()): ?>
	<div class="row mb-3 mb-md-0">
		<div class="col-12 mb-md-4">
			<div class="group-checkbox justify-content-center">
				<label class="d-none d-sm-block">Soy: </label>
				<label>
					<input type="radio" name="seller_type" checked  value="<?=UserRole::Particular?>">
					<?=UserRole::NAME(UserRole::Particular)?>
				</label>
				<label>
					<input type="radio" name="seller_type"  value="<?=UserRole::Centro?>">
					<?=UserRole::NAME(UserRole::Centro)?>
				</label>
				<label>
					<input type="radio" name="seller_type"  value="<?=UserRole::Publicista?>">
					<?=UserRole::NAME(UserRole::Publicista)?>
				</label>
			</div>
			<div class="error_msg" id="error_idioma">Indica un lenguaje para tu anuncio</div>
		</div>
	  
	</div>
	<?php else: ?>
		<input type="hidden" name="seller_type" value="<?=$_SESSION['data']['rol']?>">
<?php endif ?>

<div class="row <?= !isset($_SESSION['data']['mail']) ? "pb-2" : ""?>">
<div class="col_lft"><label class="<?= !isset($_SESSION['data']['mail']) ? "pt-md-4 pt-0" : ""?>" ><?=$language['post.label_mail']?> *</label></div>
<div class="col_rgt"><? if(!isset($_SESSION['data']['mail'])){ ?>
<div class="mail-desc">Asegúrate que el email esté bien escrito</div>
<input name="email" type="text" id="email" size="50" autocomplete="off" maxlength="50"/>
<? }else{ ?>
<input name="email" type="text" id="email" readonly value="<? echo $_SESSION['data']['mail']; ?>" placeholder="Asegúrate que el email este bien escrito"/>
<? }?><div class="error_msg" id="error_email"><?=$language['post.error_mail']?></div>
</div>
</div>
</fieldset>


<fieldset class="selects">
<div class="row">
<div class="col_lft"><label><?=$language['post.label_category']?> *</label></div>
<div class="col_rgt">
<select name="category" id="category">
    <option value="0">Selecciona una categoría</option>
    	<?
		$parent=selectSQL("sc_category",$where=array('parent_cat' => -1),"ord ASC");
		for($j=0;$j<count($parent);$j++){
				$child=selectSQL("sc_category",$where=array('parent_cat' => $parent[$j]['ID_cat']),"name ASC");
				if(count($child) > 1){
			?>
				<optgroup label="<?=mb_strtoupper($parent[$j]['name'], 'UTF-8')?>">
	        <? }
				
				for($i=0;$i<count($child);$i++){
					if ((strpos($child[$i]['name'], 'Otros') !== false) || (strpos($child[$i]['name'], 'Otras') !== false)) { 
						$otros_html='<option value="'.$child[$i]['ID_cat'].'">&nbsp;&nbsp;'.$child[$i]['name'].'</option>';
					}else {
						?>
						<option value="<?=$child[$i]['ID_cat'];?>">&nbsp;&nbsp;<?=$child[$i]['name'];?></option>
				        <? 
					}
				}echo $otros_html;$otros_html='';
				if(count($child) > 1){
			?>

			</optgroup>
			<? }
		} 
		?>
</select>
<div class="error_msg" id="error_category"><?=$language['post.error_category']?></div>
</div>
</div>
<div class="row">
<div class="col_lft">
  <label><?=$language['post.label_region']?> *</label></div>
<div class="col_rgt" id="region_container">
	<select name="region" size="1" id="region">
		<option value="0">Selecciona una provincia</option>
		<?php $cat = selectSQL("sc_region",$arr=array(),"name ASC");
		for($i=0;$i<count($cat);$i++){ ?>
		<option value="<?php echo $cat[$i]['ID_region']; ?>">
		<?php echo $cat[$i]['name']; ?>
		</option>
		<? } ?>
	</select>
<div class="error_msg" id="error_region"><?=$language['post.error_region']?></div>
</div>
</div>
<div class="row">
<div class="col_lft">
  <label><?=$language['post.label_city']?></label></div>
	<div class="col_rgt">
		<?php /*
		<select name="city" size="1" id="city">
	      <option value="0"><?=$language['post.select_city']?></option>
		</select>
		*/?>
		<input name="city" type="text" id="city" maxlength="250" />
		<div class="error_msg" id="error_city"><?=$language['post.error_city']?></div>
	</div>
</div>


<div class="row">
	<div class="col_lft">
	  <label><?=$language['post.label_ad_type']?> *</label>
	</div>
	<div class="col_rgt">		
		<select name="ad_type" size="1" id="ad_type">
	      <option value="0">Selecciona un tipo de anuncio</option>
	      <option value="1"><?=$language['post.ad_type_option_1']?></option>
	      <option value="2"><?=$language['post.ad_type_option_2']?></option>
		</select>
		<div class="error_msg" id="error_ad_type"><?=$language['post.error_ad_type']?></div>
	</div>
</div>

</fieldset>
<fieldset>
	<div class="row">
		<div class="col_lft"><label><?=$language['post.label_title']?> *</label></div>
		<div class="col_rgt">
			<input name="tit" type="text" id="tit" size="50" />
			<div class="input_count">Caracteres <span id="nro-car-tit">0</span> (min 10/máx 50)</div>
			<div class="error_msg" id="error_tit"><?=$language['post.error_title']?></div>
			<div class="error_msg" id="error_tit1">El titulo contiene palabras no permitidas</div>
		</div>
	</div>
	<div class="row">
		<div class="col_lft"><label><?=$language['post.label_description']?> *</label></div>
		<div class="col_rgt">
			<div contenteditable="true" id="text_editable" class="editable"></div>
			<textarea style="display: none;" name="text" rows="5" id="text"></textarea>
			<div class="input_count">Caracteres <span id="nro-car-text">0</span> (min 30/máx 500)</div>
			<div class="error_msg" id="error_text"><?=$language['post.error_description']?></div>
			<div class="error_msg" id="error_text1">La descripción contiene palabras no permitidas</div>
		</div>
	</div>
<div id="esp_fields">
	<div class="row" style="display: none;">
		<div class="col_lft"><label><?=$language['post.label_price']?></label></div>
		<div class="col_rgt"><input name="precio" type="number" id="precio" size="8" maxlength="9" placeholder="3.23" step="0.01"/>
			<span class="decimal_price"> <b><?=getConfParam('ITEM_CURRENCY_CODE');?></b></span>
			<div class="error_msg" id="error_price">Indica un precio para tu anuncio</div>
		</div>
	</div>
	<div class="row">
		<div class="col_lft"><label>Disponibilidad *</label></div>
		<div class="col_rgt">
			<div class="form-row">
				<div class="form-col col-sm-5" id="dis-container">
					<select name="dis" size="1" id="dis">
						<option value="0">Disponibilidad</option>
						<option value="1">Todos los días</option>
						<option value="2">Lunes a Viernes</option>
						<option value="3">Lunes a Sábado</option>
						<option value="4">Sábados y Domingos</option>
	
					</select>
				</div>
				<div class="form-col col-sm-5">
					<div class="form-row justify-content-between">
	
						<div class="form-col col-sm-4">
							<label class="text-left">Salidas*</label>
						</div>
						<div class="col-sm-7" id="out-container">
	
							<select name="out" size="1" id="out">
								<option value="0">No</option>
								<option value="1">Sí</option>
		
							</select>
						</div>
					</div>
				</div>
				
				
			</div>
			<div class="error_msg" id="error_dis">Indica tu disponibilidad</div>
		</div>
	</div>
	<div class="row">
		<div class="col_lft"><label>Horario *</label></div>
		<div class="col_rgt">
			<div class="form-row" id="horario-container">
				<div class="col-sm-5 form-col">
					<select name="horario-inicio" size="1" id="horario-inicio">
						<?php 
							for($i = 0; $i < 24; $i++){ 
								$h = $i >= 10 ? $i : "0".$i;
								print "<option value='$h:00'>$h:00</option>";
								if($i !== 24)
								{
									print "<option value='$h:30' >$h:30</option>";
								}
							}
						?>
					</select>
				</div>
				<div class="form-col col-sm-5">
					<select name="horario-final" size="1" id="horario-final">
						<?php 
							for($i = 0; $i < 24; $i++){ 
								$h = $i >= 10 ? $i : "0".$i;
								print "<option value='$h:00'>$h:00</option>";
								if($i !== 24)
								{
									print "<option value='$h:30' >$h:30</option>";
								}
							}
						?>
					</select>
				</div>
			</div>
			<div class="error_msg" id="error_horario">Indica un horario para tu anuncio</div>
		</div>
	</div>

	
	<div class="row">
		<div class="col_lft"><label>Idioma</label></div>
		<div class="col_rgt">
			<div class="form-row ">
				<div class="form-col col-sm-5">
					<select name="lang-1" size="1" >
						<option value="0">Selecciona un idioma</option>
						<?php for($i=1; $i <= Language::COUNT; $i++){ ?>
							<option value="<?=$i?>" ><?=Language::NAME($i)?></option>
						<?php  } ?>
					</select>
				</div>
				<div class="form-col col-sm-5">
					<select name="lang-2" size="1" >
						<option value="0">Selecciona un idioma</option>
						<?php for($i=1; $i <= Language::COUNT; $i++){ ?>
							<option value="<?=$i?>" ><?=Language::NAME($i)?></option>
						<?php  } ?>
					</select>
				</div>
			</div>
			<div class="error_msg" id="error_idioma">Indica un lenguaje para tu anuncio</div>
		</div>
	</div>
	<div class="row" style="display: none;">
		<div class="col_lft"><label>Forma de pago</label></div>
		<div class="col_rgt">
			<div class="group-checkbox">
				<label>
					<input type="checkbox" name="pago[]" id="pago-1" value="Efectivo">
					Efectivo
				</label>
				<label>
					<input type="checkbox" name="pago[]" id="pago-2" value="Tarjeta">
					Tarjeta
				</label>
				<label>
					<input type="checkbox" name="pago[]" id="pago-3" value="Bizum">
					Bizum
				</label>
			</div>
			<div class="error_msg" id="error_idioma">Indica un lenguaje para tu anuncio</div>
		</div>
	</div>
</div>

</fieldset>
<fieldset id="fieldset_photos">
<div class="row">
	<div class="col_lft"></div>
	<div class="col_rgt">
		<div class="photos-container mt-md-5">
			<div class="title_photos_list" id="title_photos_list">¡Sube fotos a tu anuncio!</div>
			<div class="subtitle_photos_list">Puedes subir hasta <?=getConfParam('MAX_PHOTOS_AD')?> fotos a tu anuncio</div>
			<div class="subtitle_photos_list">Tamaño max. 2 Mb. Sólo imágenes .jpg o .png</div>
			<div class="photos-button">
				<div class="photos-button-text">Haz click o arrastra para subir fotos</div>
				<input type="file" name="" data-arrow="false" id="post_photo" >
			</div>
		</div>
	</div>
</div>


<div class="error_msg" id="error_photo">Sube al menos una foto para tu anuncio!</div>
<div class="photos_list sortable">
	
</div>
<div class="error_msg" id="error_photos"></div>

</fieldset>
<fieldset class="mt-3">
	<!-- <h2>Datos de contacto</h2> -->
<div class="row">
<div class="col_lft"><label><?=$language['post.label_name']?> *</label></div>
<div class="col_rgt"><input name="name" type="text" id="name" size="30" maxlength="25" value="<? isset($_SESSION['data']['name']) ? $_SESSION['data']['name'] : '' ?>"/><div class="error_msg" id="error_name"><?=$language['post.error_name']?></div>
</div>
</div>

<div class="row mb-md-4">
	<div class="col_lft"><label><?=$language['post.label_phone']?> *</label></div>
	<div class="col_rgt">
		<div class="phone_container">
			<input name="phone" class="phone_number" type="tel" id="phone" size="20" maxlength="11" value="<? echo isset($_SESSION['data']['phone']) ? $_SESSION['data']['phone'] : ''; ?>"/>
			<label ><input type="checkbox" value="1" name="whatsapp" <?= isset($_SESSION['data']['whatsapp']) && $_SESSION['data']['whatsapp'] == 1 ? 'checked' : '' ?>  > Whatsapp</label>
		</div>
		<div class="phone_container" style="display: none;">
			<input name="phone1" class="phone_number" type="tel" id="phone1" size="20" maxlength="20" value="<? echo isset($_SESSION['data']['phone1']) ? $_SESSION['data']['phone1'] : ''; ?>"/>
			<label >Whatsapp <input type="checkbox" value="1" name="whatsapp1" <?= isset($_SESSION['data']['whatsapp1']) && $_SESSION['data']['whatsapp1'] == 1 ? 'checked' : '' ?> ></label>
		</div>
		
		<div class="error_msg" id="error_phone"><?=$language['post.error_phone']?></div>
	</div>
</div>

</fieldset>

<div class="row">
<label class="radio">
    <input name="terminos" type="checkbox" id="terminos" value="1"/>
    <?=$language['post.label_terms']?></label><div class="error_msg" id="error_terminos"><?=$language['post.error_terms']?></div>
</div>


<div class="row">
	<label class="radio">
    	<input name="notifications" type="checkbox" id="notifications" value="1"/>
    		<?=$language['post.label_notifications']?>
    </label>
</div>


<div class="row">
<input type="button" class="button mb-5" id="butPub" value="<?=$language['post.button_post']?>"/>
<? $token_q = generateFormToken('postAdToken'); ?>
<input type="hidden" name="token" id="token" value="<?=$token_q;?>">
</div>
<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" />
<input type="hidden" id="new_order" name="order" value="0" />
</form>
<? } ?>
</div>

<?php
	loadBlock('post-warning');
	if($limite == 1)
		$ANUNCIO_NUEVO_PREMIUM = true;
	
	loadBlock('payment_dialog');	
	loadBlock('editor');
?>

<?php if(isset($_SESSION['data']['ID_user']) && $limite == 2): ?>
  
  <dialog class="dialog" open >
	  <div class="dialog-modal">
		  <a href="/index.php" style="color: black;">
			  <i class="fa-times-circle fa"></i>
		  </a>
		  <p>Haz alcanzado el límite de anuncios publicados</p>
	  </div>
  </dialog>
  
<?php endif ?>
<?php if(isset($_SESSION['data']['ID_user']) && $limite == 0 && checkLastDelete($_SESSION['data']['ID_user'])): ?>
  
  <dialog class="dialog" open >
	  <div class="dialog-modal">
		  <a href="/index.php" style="color: black;">
			  <i class="fa-times-circle fa"></i>
		  </a>
		  <p>Aún no puedes publicar anuncio <b>Gratis</b></p>
		  <p>Ver nuestro planos o Intente más tarde</p>
		  <button onclick="openPayment(); " class="payment-btn">
                Ver opciones de pago
          </button>
		  
	  </div>
  </dialog>
  
<?php endif ?>


<script src="<?=getConfParam('SITE_URL')?>src/js/filter.js"></script>
<script src="<?=getConfParam('SITE_URL')?>src/js/post.js"></script>
<script>
	function submitFormCaptcha (state) {
    	grecaptcha.ready(function() {
		    grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'formulario'})
		    .then(function(token) {
		        document.getElementById('g-recaptcha-response').value=token;
		        $("#new_item_post").submit();
		    });
	    });
	}

</script>
<script type="text/javascript">
	$(document).ready(function() {
		function sellerTypeTemplate (state) {
			if (!state.id) { return state.text; }
			if (state.element.value == 0) { return state.text; }
		  	var $state = $('<span class="select-icon"><img src="src/images/'+  state.element.text.toLowerCase() +'.png" class="select-icon" /> ' + state.text +'</span>');
		 	return $state;
		};
		function sellerTypeTemplateSelection (state) {
		  if (!state.id) {
		    return state.text;
		  }
		if (state.element.value == 0) { return state.text; }
		  var baseUrl = "/user/pages/images/flags";
		  var $state = $('<span class="select-icon"><img src="src/images/'+  state.text.toLowerCase() +'.png" class="select-icon" /> ' + state.text +'</span>');
		  return $state;
		};

		$('.sortable').sortable({
  			helper: "clone",
  			forcePlaceholderSize: true,
			forceHelperSize: true,
			grid: [ 10, 10 ]
		});
	    $('#content select:not(#sellerType)').select2({
		    minimumResultsForSearch: Infinity
		});
		$('#sellerType').select2({
		  	templateResult: sellerTypeTemplate,
		  	templateSelection: sellerTypeTemplateSelection,
		    minimumResultsForSearch: Infinity
		});
		$('#content select').on('select2:open', function (e) {
			setTimeout(function(){ 
				$('.select2-results__group').each(function() {
			    	if($(this).siblings().find('.select2-results__option--selected').length > 0){
			    		$(this).toggleClass('openedGroup');
			    	}
				});
			}, 100);
		});
	});
</script>
<?php loadBlock('datajson'); ?>