<h3 class="title_main">Filtrar anuncios</h3>
<span class="separator"><i></i></span>
<div class="filter">
<div class="open_filter"><i class="fa fa-filter" aria-hidden="true"></i> Filtrar anuncios</div>
<div class="filter_options">
<select name="category" id="category" class="sel">
    <option value="">Categoría</option>
    	<?
		$parent=selectSQL("sc_category",$where=array('parent_cat' => -1));
		for($j=0;$j<count($parent);$j++){
		?>
		<option value="<?=$parent[$j]['name_seo'];?>/" style="font-weight:600; background:#e8e8e8;" <? if($cat_ID==$parent[$j]['ID_cat']) echo 'selected';?>><?=mb_strtoupper($parent[$j]['name'], 'UTF-8');?></option>
        <?
			$child=selectSQL("sc_category",$where=array('parent_cat' => $parent[$j]['ID_cat']));
			for($i=0;$i<count($child);$i++){
		?>
		<option value="<?=$parent[$j]['name_seo'];?>/<?=$child[$i]['name_seo'];?>/" <? if($cat_ID==$child[$i]['ID_cat']) echo 'selected';?>>&nbsp;&nbsp;<?=$child[$i]['name'];?></option>
        <? 
			}
		} ?>
</select>
<select name="region" id="region_">
   <option value="">Provincia</option>
   <? $provin = selectSQL("sc_region");
      for($i=0;$i<count($provin);$i++){ ?>
   <option value="<?php echo $provin[$i]['name_seo']; ?>" <? if($region_ID==$provin[$i]['ID_region']){?>selected="selected"<? }?>><? echo $provin[$i]['name']; ?></option>
   <? } ?>
</select>
<select name="city_" id="city_" class="sel">
   <option value="">Localidad</option>
   <? $cities = selectSQL("sc_city",$w=array('ID_region'=>$region_ID), 'name ASC');
      for($i=0;$i<count($cities);$i++){ ?>
   <option value="<?php echo $cities[$i]['name_seo']; ?>" <? if($city_data[0]['name_seo']==$cities[$i]['name_seo']){?>selected="selected"<? }?>><? echo $cities[$i]['name']; ?></option>
   <? } ?>
</select>
<select id="price_0" class="min_filter">
	<option value="">Precio Mín.</option>
    <? for($i=1; $i<=10 ; $i++){?>
    <option value="<?=$i*10;?>"<? if($_GET['min_pr']==$i*10){?> selected="selected"<? }?>><?=formatPrice($i*10)?></option>	
    <? }?>
    <? for($i=1; $i<=18 ; $i++){?>
    <option value="<?=100+$i*50;?>"<? if($_GET['min_pr']==100+$i*50){?> selected="selected"<? }?>><?=formatPrice(100+$i)?></option>	
    <? }?>
    <? for($i=1; $i<=10 ; $i++){?>
    <option value="<?=1000+$i*100;?>"<? if($_GET['min_pr']==1000+$i*100){?> selected="selected"<? }?>><?=formatPrice(1000+$i*100)?></option>	
    <? }?>
    <? for($i=3; $i<=10 ; $i++){?>
    <option value="<?=1000*$i;?>"<? if($_GET['min_pr']==1000*$i){?> selected="selected"<? }?>><?=formatPrice(1000*$i)?></option>	
    <? }?>
    <? for($i=1; $i<=18 ; $i++){?>
    <option value="<?=5000*$i+10000;?>"<? if($_GET['min_pr']==5000*$i+10000){?> selected="selected"<? }?>><?=formatPrice(5000*$i+10000)?></option>	
    <? }?>
</select>
<select id="price_1" class="min_filter">
	<option value="">Precio Máx.</option>
     <? for($i=1; $i<=10 ; $i++){?>
    <option value="<?=$i*10;?>"<? if($_GET['max_pr']==$i*10){?> selected="selected"<? }?>><?=formatPrice($i*10)?></option>	
    <? }?>
    <? for($i=1; $i<=18 ; $i++){?>
    <option value="<?=100+$i*50;?>"<? if($_GET['max_pr']==100+$i*50){?> selected="selected"<? }?>><?=formatPrice(100+$i)?></option>	
    <? }?>
    <? for($i=1; $i<=10 ; $i++){?>
    <option value="<?=1000+$i*100;?>"<? if($_GET['max_pr']==1000+$i*100){?> selected="selected"<? }?>><?=formatPrice(1000+$i*100)?></option>	
    <? }?>
    <? for($i=3; $i<=10 ; $i++){?>
    <option value="<?=1000*$i;?>"<? if($_GET['max_pr']==1000*$i){?> selected="selected"<? }?>><?=formatPrice(1000*$i)?></option>	
    <? }?>
    <? for($i=1; $i<=18 ; $i++){?>
    <option value="<?=5000*$i+10000;?>"<? if($_GET['max_pr']==5000*$i+10000){?> selected="selected"<? }?>><?=formatPrice(5000*$i+10000)?></option>	
    <? }?>
</select>
<select name="order" id="order_list">
	<option value="0">Ordenar anuncios</option>
	<option value="<?=getPagOrd("pd");?>"<? if($_GET['ord'] == "pd"){?> selected="selected" <? }?>><?=$language['list.price_min_max']?></option>
	<option value="<?=getPagOrd("pa");?>"<? if($_GET['ord'] == "pa"){?> selected="selected" <? }?>><?=$language['list.price_max_min']?></option>
	<option value="<?=getPagOrd("da");?>"<? if($_GET['ord'] == "da"){?> selected="selected" <? }?>><?=$language['list.date_ad_min']?></option>
	<option value="<?=getPagOrd("dd");?>"<? if($_GET['ord'] == "dd"){?> selected="selected" <? }?>><?=$language['list.date_ad_max']?></option>
</select>
<?
// CASAS
if($casas_detail){
?>
<select id="room_0" class="min_filter2">
	<option value="">Habitaciones Desde</option>
    <? for($i=1; $i<10; $i++){?>
    <option value="<?=$i;?>"<? if($_GET['min_room']==$i){?> selected="selected"<? }?>><?=$i?></option>	
    <? }?>
</select>
<select id="room_1" class="min_filter2">
	<option value="">Habitaciones Hasta</option>
    <? for($i=1; $i<10; $i++){?>
    <option value="<?=$i;?>"<? if($_GET['max_room']==$i){?> selected="selected"<? }?>><?=$i?></option>	
    <? }?>    
</select>
<select id="broom_0" class="min_filter2">
	<option value="">Baños Desde</option>
    <? for($i=1; $i<10; $i++){?>
    <option value="<?=$i;?>"<? if($_GET['min_broom']==$i){?> selected="selected"<? }?>><?=$i?></option>	
    <? }?>
</select>
<select id="broom_1" class="min_filter2">
	<option value="">Baños Hasta</option>
    <? for($i=1; $i<10; $i++){?>
    <option value="<?=$i;?>"<? if($_GET['max_broom']==$i){?> selected="selected"<? }?>><?=$i?></option>	
    <? }?></select>
<? } ?>
<?
// SUPERFICIE
if($casas){
?>
<select id="area_0" class="min_filter2">
	<option value="">Superficie Desde</option>
       <? for($i=1; $i<10; $i++){?>
    <option value="<?=$i*10;?>"<? if($_GET['min_area']==$i*10){?> selected="selected"<? }?>><?=number_format($i*10,0,',','.')?></option>	
    <? }?>
    <? for($i=1; $i<=100; $i++){?>
    <option value="<?=$i*10;?>"<? if($_GET['min_area']==$i*100){?> selected="selected"<? }?>><?=number_format($i*100,0,',','.')?></option>	
    <? }?>
</select>
<select id="area_1" class="min_filter2">
	<option value="">Superficie Hasta</option>
    <? for($i=1; $i<10; $i++){?>
    <option value="<?=$i*10;?>"<? if($_GET['max_area']==$i*10){?> selected="selected"<? }?>><?=number_format($i*10,0,',','.')?></option>	
    <? }?>
    <? for($i=1; $i<=10; $i++){?>
    <option value="<?=$i*100;?>"<? if($_GET['max_area']==$i*100){?> selected="selected"<? }?>><?=number_format($i*100,0,',','.')?></option>	
    <? }?></select>
<? } ?>
<?
// COCHES O MOTOS
if($coches_motos){
?>
<select id="fuel_car">
        <option value="">Combustible</option>
        <? $type_fuel=selectSQL("sc_type_fuel",$w=array(),'ID_fuel ASC');
		for($i=0;$i<count($type_fuel);$i++){?>
        <option value="<?=$type_fuel[$i]['ID_fuel']?>" <? if($_GET['fuel_car']==$type_fuel[$i]['ID_fuel']) echo 'selected';?>><?=$type_fuel[$i]['name']?></option>
       	<? } ?>
</select>
<select id="date_car_0" class="min_filter">
	<option value="">Año Desde</option>
    <? for($i=1950; $i<=date("Y",time()); $i++){?>
    <option value="<?=$i;?>"<? if($_GET['min_date_car']==$i){?> selected="selected"<? }?>><?=$i?></option>	
    <? }?>
</select>
<select id="date_car_1" class="min_filter">
	<option value="">Año Hasta</option>
    <? for($i=date("Y",time()); $i>1950; $i--){?>
    <option value="<?=$i;?>"<? if($_GET['max_date_car']==$i){?> selected="selected"<? }?>><?=$i?></option>	
    <? }?>
</select>
<select id="km_car_0" class="min_filter">
	<option value="">Km Mín.</option>
       <? for($i=1; $i<10; $i++){?>
    <option value="<?=$i*1000;?>"<? if($_GET['min_km_car']==$i*1000){?> selected="selected"<? }?>><?=number_format($i*1000,0,',','.')?></option>	
    <? }?>
    <? for($i=1; $i<=10; $i++){?>
    <option value="<?=$i*10000;?>"<? if($_GET['min_km_car']==$i*10000){?> selected="selected"<? }?>><?=number_format($i*10000,0,',','.')?></option>	
    <? }?>
</select>
<select id="km_car_1" class="min_filter">
	<option value="">Km Máx.</option>
    <? for($i=1; $i<10; $i++){?>
    <option value="<?=$i*1000;?>"<? if($_GET['max_km_car']==$i*1000){?> selected="selected"<? }?>><?=number_format($i*1000,0,',','.')?></option>	
    <? }?>
    <? for($i=1; $i<=10; $i++){?>
    <option value="<?=$i*10000;?>"<? if($_GET['max_km_car']==$i*10000){?> selected="selected"<? }?>><?=number_format($i*10000,0,',','.')?></option>	
    <? }?>
</select>
<? } ?>
<input type="button" name="search_filter" id="search_filter" value="<?=$language['filter.button_search']?>">
<input type="hidden" id="url_now" value="<?=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);?>">
</div>
</div>