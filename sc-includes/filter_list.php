
<div class="w-10 text-right filter-container">
    <button class="btn btn-filtro f-1600 d-none d-md-inline " id="filterbutton">Filtrar <i class="fa fa-sliders-h"></i></button>
<div class="filter f-1200">
<div class="open_filter"><i class="fa fa-sliders-h" aria-hidden="true"></i> Filtrar anuncios</div>
<div class="filter_options">

<div class="filter-custom-select <?php if(isset($cat_ID)) print 'cat_filter' ?>">
    <select name="Maincategory" id="Maincategory" class="sel">
        <option value="" class="white">Categoría</option>
            <?
            // categorias fercode: ord ASC
            $parent=selectSQL("sc_category",$where=array('parent_cat' => -1),"ord ASC");
            for($j=0;$j<count($parent);$j++){
                $childSelected = false;
                $child=selectSQL("sc_category",$where=array('parent_cat' => $parent[$j]['ID_cat']),"name ASC");
                for($i=0;$i<count($child);$i++){
                    if($cat_ID==$child[$i]['ID_cat']){
                        $childSelected = true;
                        break;
                    }              
                }  
            ?>
            <option value="<?=$parent[$j]['name_seo'];?>/" <? if($cat_ID==$parent[$j]['ID_cat'] || $childSelected) echo 'selected';?>><?=mb_strtoupper($parent[$j]['name'], 'UTF-8');?></option><?
            } ?>
    </select>

</div>

<div class="filter-custom-select">
    <select name="category" id="category" class="sel">
    <option value="">Todas las subcategorías</option>
            <?
            // categorias fercode: ord ASC
            $parent=selectSQL("sc_category",$where=array('parent_cat' => -1),"ord ASC");
            for($j=0;$j<count($parent);$j++){
                $otros_html='';
                $parentSelected = false;
                $child=selectSQL("sc_category",$where=array('parent_cat' => $parent[$j]['ID_cat']),"name ASC");
                for($i=0;$i<count($child);$i++){
                    if($cat_ID==$child[$i]['ID_cat']){
                        $parentSelected = true;
                        break;
                    }              
                } 
            ?>
            <?
                if($cat_ID==$parent[$j]['ID_cat'] || $parentSelected){
                    $child=selectSQL("sc_category",$where=array('parent_cat' => $parent[$j]['ID_cat']),"name ASC");
                    for($i=0;$i<count($child);$i++){
                        // Categoria Otros al final fercode:
                        if ((strpos($child[$i]['name'], 'Otros')) !== false || (strpos($child[$i]['name'], 'Otras')) !== false) {
                            $otros_html='<option value="'.$parent[$j]['name_seo'].'/'.$child[$i]['name_seo'].'/" data-pub="'.$parent[$j]['name_seo'].'/">'.$child[$i]['name'].'</option>';
                        } else{ ?>
                        <option value="<?=$parent[$j]['name_seo'];?>/<?=$child[$i]['name_seo'];?>/" <? if($cat_ID==$child[$i]['ID_cat']) echo 'selected';?> data-pub="<?=$parent[$j]['name_seo'];?>/"><?=$child[$i]['name'];?></option>
                        <? }               
                    }
                    echo $otros_html;           
                }
            } ?>
    </select>

</div>


<select name="region" id="region_">
    <option value="">Toda España</option>
    <? $provin = selectSQL("sc_region", array(), "name ASC");
        for($i=0;$i<count($provin);$i++){ ?>
    <option value="<?php echo $provin[$i]['name_seo']; ?>" <? if($region_ID==$provin[$i]['ID_region']){?>selected="selected"<? }?>><? echo $provin[$i]['name']; ?></option>
    <? } ?>
</select>



<input type="text" name="city_" id="city_" placeholder="Localidad" value="<?= $city ? $city : ''?>" style="display: none;">


   <div <?php if( $cat_ID == 75 || $cat_ID == 157 || $cat_ID == 61 ) print 'style="display: none;"' ?>>
      <div class="d-none">
           <input type="hidden" id="price_0" value="<?php if(isset($_GET['min_pr']) && $_GET['min_pr'] != "") print $_GET['min_pr']; ?>" >
           <input type="hidden" id="price_1" value="<?php if(isset($_GET['max_pr']) && $_GET['max_pr'] != "") print $_GET['max_pr']; ?>" >
           
           <p class="p-0"> 
               <label for="amount" class="pl-2 mb-0">Precio</label>
               <input type="text" id="amount" readonly="">
           </p>
           
           <div id="slider-range" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
               <!--<div class="ui-slider-range ui-corner-all ui-widget-header" style="left: 23.8%; width: 35.4%;"></div>-->
               <span tabindex="0" class=" ui-slider-handle ui-corner-all ui-state-default" style="left: 23.8%;"></span>
               <span tabindex="0" class=" ui-slider-handle ui-corner-all ui-state-default" style="left: 59.2%;"></span>
           </div>
           <p class="d-flex justify-content-between align-items-center">
               <span>Min</span>
               <span>Max</span>
           </p>
       </div>
       
       <div class="d-block min_filter_container" id="price_container">
           <?php if( isset($_GET['s']) ): ?>
                <?php if( $_GET['s'] == 'motor' ): ?>
                    <select id="Mprice_0" class="min_filter">
                        <option value="">Precio Mín.</option>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=$i*500;?>" <? if($_GET['min_pr']==$i*500){?> selected="selected"<? }?> ><?=formatPrice($i*500)?></option>
                        <? }?>
                        <? for($i=1; $i<=18 ; $i++){?>
                        <option value="<?=5000+$i*500;?>"<? if($_GET['min_pr']== 5000+$i*500){?> selected="selected"<? }?> ><?=formatPrice(5000+$i*500)?></option>
                        <? }?>
                        <? for($i=3; $i<=10 ; $i++){?>
                        <option value="<?=$i*5000;?>"<? if($_GET['min_pr']== $i*5000){?> selected="selected"<? }?> ><?=formatPrice($i*5000)?></option>
                        <? }?>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=50000 + (5000*$i);?>"<? if($_GET['min_pr']== 50000+($i*5000)){?> selected="selected"<? }?>><?=formatPrice(50000 + (5000*$i))?></option>
                        <? }?>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=10000*$i+100000;?>"<? if($_GET['min_pr']== 10000*$i+100000){?> selected="selected"<? }?>><?=formatPrice(10000*$i+100000)?></option>
                        <? }?>
                    </select>
                    <select id="Mprice_1" class="min_filter">
                        <option value="">Precio Máx.</option>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=$i*500;?>" <? if($_GET['max_pr']==$i*500){?> selected="selected"<? }?> ><?=formatPrice($i*500)?></option>
                        <? }?>
                        <? for($i=1; $i<=18 ; $i++){?>
                        <option value="<?=5000+$i*500;?>"<? if($_GET['max_pr']== 5000+$i*500){?> selected="selected"<? }?> ><?=formatPrice(5000+$i*500)?></option>
                        <? }?>
                        <? for($i=3; $i<=10 ; $i++){?>
                        <option value="<?=$i*5000;?>"<? if($_GET['max_pr']== $i*5000){?> selected="selected"<? }?> ><?=formatPrice($i*5000)?></option>
                        <? }?>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=50000 + (5000*$i);?>"<? if($_GET['max_pr']== 50000+($i*5000)){?> selected="selected"<? }?>><?=formatPrice(50000 + (5000*$i))?></option>
                        <? }?>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=10000*$i+100000;?>"<? if($_GET['max_pr']== 10000*$i+100000){?> selected="selected"<? }?>><?=formatPrice(10000*$i+100000)?></option>
                        <? }?>
                    </select>
                <?php elseif( $_GET['s'] =='inmobiliaria' ): ?>
                    <select id="Mprice_0" class="min_filter">
                        <option value="">Precio Mín.</option>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=$i*500;?>"<? if($_GET['min_pr']==$i*500){?> selected="selected"<? }?> ><?=formatPrice($i*500)?></option>
                        <? }?>
                        <? for($i=1; $i<=18 ; $i++){?>
                        <option value="<?=5000+$i*500;?>"<? if($_GET['min_pr']==5000+$i*500){?> selected="selected"<? }?>><?=formatPrice(5000+$i*500)?></option>
                        <? }?>
                        <? for($i=3; $i<=10 ; $i++){?>
                        <option value="<?=$i*5000;?>"<? if($_GET['min_pr']==$i*5000){?> selected="selected"<? }?>><?=formatPrice($i*5000)?></option>
                        <? }?>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=50000 + (5000*$i);?>"<? if($_GET['min_pr']== 50000 + ($i*5000)){?> selected="selected"<? }?> ><?=formatPrice(50000 + (5000*$i))?></option>
                        <? }?>
                        <? for($i=1; $i<=18 ; $i++){?>
                        <option value="<?=50000*$i+100000;?>" <? if($_GET['min_pr']== 50000*$i+100000){?> selected="selected"<? }?> ><?=formatPrice(50000*$i+100000)?></option>
                        <? }?>
                        <? for($i=2; $i<=10 ; $i++){?>
                        <option value="<?=1000000*$i;?>" <? if($_GET['min_pr']== 1000000*$i){?> selected="selected"<? }?> ><?=formatPrice(1000000*$i)?></option>
                        <? }?>
                    </select>
                    <select id="Mprice_1" class="min_filter">
                        <option value="">Precio Máx.</option>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=$i*500;?>"<? if($_GET['max_pr']==$i*500){?> selected="selected"<? }?> ><?=formatPrice($i*500)?></option>
                        <? }?>
                        <? for($i=1; $i<=18 ; $i++){?>
                        <option value="<?=5000+$i*500;?>"<? if($_GET['max_pr']==5000+$i*500){?> selected="selected"<? }?>><?=formatPrice(5000+$i*500)?></option>
                        <? }?>
                        <? for($i=3; $i<=10 ; $i++){?>
                        <option value="<?=$i*5000;?>"<? if($_GET['max_pr']==$i*5000){?> selected="selected"<? }?>><?=formatPrice($i*5000)?></option>
                        <? }?>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=50000 + (5000*$i);?>"<? if($_GET['max_pr']== 50000 + ($i*5000)){?> selected="selected"<? }?> ><?=formatPrice(50000 + (5000*$i))?></option>
                        <? }?>
                        <? for($i=1; $i<=18 ; $i++){?>
                        <option value="<?=50000*$i+100000;?>" <? if($_GET['max_pr']== 50000*$i+100000){?> selected="selected"<? }?> ><?=formatPrice(50000*$i+100000)?></option>
                        <? }?>
                        <? for($i=2; $i<=10 ; $i++){?>
                        <option value="<?=1000000*$i;?>" <? if($_GET['max_pr']== 1000000*$i){?> selected="selected"<? }?> ><?=formatPrice(1000000*$i)?></option>
                        <? }?>
                    </select>
                <?php else: ?> 
                    <select id="Mprice_0" class="min_filter">
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
                    <select id="Mprice_1" class="min_filter">
                        <option value="">Precio Máx.</option>
                        <? for($i=1; $i<=10 ; $i++){?>
                        <option value="<?=$i*10;?>"<? if($_GET['max_pr']==$i*10){?> selected="selected"<? }?>><?=formatPrice($i*10)?></option>
                        <? }?>
                        <? for($i=1; $i<=18 ; $i++){?>
                        <option value="<?=100+$i*50;?>"<? if($_GET['max_pr']==100+$i*50){?> selected="selected"<? }?>><?=formatPrice(100+$i * 50)?></option>
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
                <?php endif ?>
            <?php else: ?> 
                <select id="Mprice_0" class="min_filter">
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
                <select id="Mprice_1" class="min_filter">
                    <option value="">Precio Máx.</option>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=$i*10;?>"<? if($_GET['max_pr']==$i*10){?> selected="selected"<? }?>><?=formatPrice($i*10)?></option>
                    <? }?>
                    <? for($i=1; $i<=18 ; $i++){?>
                    <option value="<?=100+$i*50;?>"<? if($_GET['max_pr']==100+$i*50){?> selected="selected"<? }?>><?=formatPrice(100+$i * 50)?></option>
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
           <?php endif ?>
       </div>
   </div> 






<div id="filter_details">
    
    <?
    // CASAS
    if($casas_detail){
    ?>
    <div class="min_filter_container">
        <select id="room_0" class="min_filter">
            <option value="">Habitaciones Desde</option>
            <? for($i=1; $i<10; $i++){?>
            <option value="<?=$i;?>"<? if($_GET['min_room']==$i){?> selected="selected"<? }?>><?=$i?></option>
            <? }?>
        </select>
        <select id="room_1" class="min_filter">
            <option value="">Habitaciones Hasta</option>
            <? for($i=1; $i<10; $i++){?>
            <option value="<?=$i;?>"<? if($_GET['max_room']==$i){?> selected="selected"<? }?>><?=$i?></option>
            <? }?>
        </select>
        <select id="broom_0" class="min_filter">
            <option value="">Baños Desde</option>
            <? for($i=1; $i<10; $i++){?>
            <option value="<?=$i;?>"<? if($_GET['min_broom']==$i){?> selected="selected"<? }?>><?=$i?></option>
            <? }?>
        </select>
        <select id="broom_1" class="min_filter">
            <option value="">Baños Hasta</option>
            <? for($i=1; $i<10; $i++){?>
            <option value="<?=$i;?>"<? if($_GET['max_broom']==$i){?> selected="selected"<? }?>><?=$i?></option>
            <? }?>
        </select>
    </div>
    <? } ?>
    <?
    // SUPERFICIE
    if($casas){
    ?>
    <div class="min_filter_container">
        <select id="area_0" class="min_filter">
            <option value="">Superficie Desde</option>
               <? for($i=1; $i<10; $i++){?>
            <option value="<?=$i*10;?>"<? if($_GET['min_area']==$i*10){?> selected="selected"<? }?>><?=number_format($i*10,0,',','.')?></option>
            <? }?>
            <? for($i=1; $i<=100; $i++){?>
            <option value="<?=$i*10;?>"<? if($_GET['min_area']==$i*100){?> selected="selected"<? }?>><?=number_format($i*100,0,',','.')?></option>
            <? }?>
        </select>
        <select id="area_1" class="min_filter">
            <option value="">Superficie Hasta</option>
            <? for($i=1; $i<10; $i++){?>
            <option value="<?=$i*10;?>"<? if($_GET['max_area']==$i*10){?> selected="selected"<? }?>><?=number_format($i*10,0,',','.')?></option>
            <? }?>
            <? for($i=1; $i<=10; $i++){?>
            <option value="<?=$i*100;?>"<? if($_GET['max_area']==$i*100){?> selected="selected"<? }?>><?=number_format($i*100,0,',','.')?></option>
            <? }?>
        </select>
    </div>
    <? } ?>
    <?
    // COCHES O MOTOS
    if($coches_motos){
    ?>
    <div class="min_filter_container">
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
    </div>
    <select id="fuel_car">
            <option value="">Combustible</option>
            <? $type_fuel=selectSQL("sc_type_fuel",$w=array(),'ID_fuel ASC');
                for($i=0;$i<count($type_fuel);$i++){?>
                <option value="<?=$type_fuel[$i]['ID_fuel']?>" <? if($_GET['fuel_car']==$type_fuel[$i]['ID_fuel']) echo 'selected';?>><?=$type_fuel[$i]['name']?></option>
                <? } ?>
    </select>
    <? } ?>
    
</div>

<select name="order" id="order_list">
	<option value="0">Ordenar anuncios</option>
    <option value="all" <? if($_GET['ord'] == "all"){?> selected="selected" <? }?>><?=$language['list.all']?></option>
    <option value="supply" <? if($_GET['ord'] == "supply"){?> selected="selected" <? }?>><?=$language['list.supply']?></option>
    <option value="demand"<? if($_GET['ord'] == "demand"){?> selected="selected" <? }?>><?=$language['list.demand']?></option>
</select>
<select name="seller" id="seller_list">
    <option value="all" <? if($_GET['sell'] == "all"){?> selected="selected" <? }?> >Tipo de vendedor</option>
    <option value="private" <? if($_GET['sell'] == "private"){?> selected="selected" <? }?>>Particular</option>
    <option value="professional" <? if($_GET['sell'] == "professional"){?> selected="selected" <? }?>>Profesional</option>
</select>

<input type="button" name="search_filter" id="search_filter" value="<?=$language['filter.button_search']?>">
<input type="hidden" id="url_now" value="<?=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);?>">
</div>
</div>
</div>

<script src="src/js/filter-select.js"></script>