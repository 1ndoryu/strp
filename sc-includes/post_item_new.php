<?php
	include_once 'mod/post_item.mod.php';
?>

<script src='https://www.google.com/recaptcha/api.js?render=<?php echo SITE_KEY; ?>'></script>	


<script src="<?=getConfParam('SITE_URL')?>src/js/filter.js"></script>
<div class="post-item-container">
	<div class="post-item-form">
		<div class="form-col-left">
			<img src="<?=Images::getImage("post-item.jpg")?>" class="item-form-img" alt="">
		</div>
		<div class="form-col-right">
			<form action="" method="post" id="new_item_post" class="form-horizontal">
				<div id="item_post_part1" style="display: none;">

					<div class="form-item-tittle">
						<span>01</span>
						<h2>Descripción del anuncio</h2>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="title" class="control-label">Soy un <b>*</b></label>
								<select id="sellerType" name="seller_type" size="1">
									<option value="0" >Selecciona un tipo de vendedor</option>
	
									<option value="1"><?=$language['post.seller_type_option_1']?></option>
									<option value="2"><?=$language['post.seller_type_option_2']?></option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="ad_type" class="control-label">Tipo de anuncio <b>*</b></label>
								<select name="ad_type" size="1" id="ad_type">
									<option value="0">Selecciona un tipo de anuncio</option>
									<option value="1"><?=$language['post.ad_type_option_1']?></option>
									<option value="2"><?=$language['post.ad_type_option_2']?></option>
								</select>
								<div class="error_msg" id="error_ad_type"><?=$language['post.error_ad_type']?></div>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="title" class="control-label">Categoría <b>*</b></label>
						<select name="category" id="category">
							<option value="0">Selecciona una categoría</option>
								<?
								$parent=selectSQL("sc_category",$where=array('parent_cat' => -1),"ord ASC");
								for($j=0;$j<count($parent);$j++){
									?>
									<optgroup label="<?=mb_strtoupper($parent[$j]['name'], 'UTF-8')?>">
									<?
										$child=selectSQL("sc_category",$where=array('parent_cat' => $parent[$j]['ID_cat']),"name ASC");
										for($i=0;$i<count($child);$i++){
											if ((strpos($child[$i]['name'], 'Otros') !== false) || (strpos($child[$i]['name'], 'Otras') !== false)) { 
												$otros_html='<option value="'.$child[$i]['ID_cat'].'">&nbsp;&nbsp;'.$child[$i]['name'].'</option>';
											}else {
												?>
												<option value="<?=$child[$i]['ID_cat'];?>">&nbsp;&nbsp;<?=$child[$i]['name'];?></option>
												<? 
											}
										}echo $otros_html;$otros_html='';
									?>
									</optgroup>
									<? 
								} 
								?>
						</select>
						<div class="error_msg" id="error_category"><?=$language['post.error_category']?></div>
					</div>
					<div class="form-group">
						<label for="title" class="control-label">Provincia <b>*</b></label>
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
					<div class="form-group">
						<label for="title" class="control-label">Localidad <b>*</b></label>
						<input name="city" type="text" id="city" maxlength="250" />
						<div class="error_msg" id="error_city"><?=$language['post.error_city']?></div>
					</div>
					<div class="form-group">
						<label for="title" class="control-label">Titulo <b>*</b></label>
						<input name="tit" type="text" id="tit" size="50" />
						<div class="input_count">Caracteres <span id="nro-car-tit">0</span> (min 10/máx 50)</div>
						<div class="error_msg" id="error_tit"><?=$language['post.error_title']?></div>
						<div class="error_msg" id="error_tit1">El titulo contiene palabras prohibidas</div>
					</div>
	
					<div class="form-group">
						<label for="text" class="control-label">Descripción <b>*</b></label>
						<div contenteditable="true" id="text_editable" class="editable"></div>
						<textarea style="display: none;" name="text" rows="5" id="text"></textarea>
						<div class="input_count">Caracteres <span id="nro-car-text">0</span> (min 10/máx 500)</div>
						<div class="error_msg" id="error_text"><?=$language['post.error_description']?></div>
						<div class="error_msg" id="error_text1">La descripcion contiene palabras prohibidas</div>
					</div>
				</div>
				<div id="item_post_part2">

					<div class="form-item-tittle">
						<span>02</span>
						<h2>Detalles</h2>
					</div>
					<div class="form-group">
						<label for="title" class="control-label">Mis fotos <b>*</b></label>
						
						<div class="photos_list sortable">
							<? for($i=1;$i<=getConfParam('MAX_PHOTOS_AD');$i++){?>
							<div id="photo_container-<?=$i?>" class="photo_list free">
								<input name="userImage[]" id="photo-<?=$i?>" type="file" class="photoFile" />
							</div>
							<? } ?>
							<div class="error_msg" id="error_photos"></div>
						</div>
						<div class="error_msg" id="error_photo">Sube al menos una foto para tu anuncio!</div>
						
					</div>
					
				</div>
							
			</form>
		</div>
	</div>
	<div class="post-item-footer">
		<div class="post-item-footer-box">
			<span>01</span>
			<p>Descripción del anuncio</p>
		</div>
		<div class="post-item-footer-box">
			<span>02</span>
			<p>Detalles</p>
		</div>
		<div class="post-item-footer-box">
			<span>03</span>
			<p>Finalizar</p>
		</div>
	</div>
</div>
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