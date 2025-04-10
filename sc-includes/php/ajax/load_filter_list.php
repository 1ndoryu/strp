<?php 
    include("../../../settings.inc.php");

    if(isset($_GET['target'])){
        $target = $_GET['target'];
    }else if(isset($_GET['se'])){
        $target = 0;
        $cat_data=selectSQL("sc_category",$w=array('name_seo'=>$_GET['se'],'parent_cat'=>'0>'));
        if(count($cat_data)==0){
            "";
            exit;
        } 
        $cat_ID=$cat_data[0]['ID_cat'];
        $filter_ad['ID_cat']=$cat_ID;
        $category_parent=$cat_data[0]['parent_cat'];
        if($cat_data[0]['field_0']==1) $target = 'coches';
        if($cat_data[0]['field_2']==1) $target = 'casas';
        if($cat_data[0]['field_3']==1) $target = 'casas_details';
        
    }else if(isset($_GET['price'])){
        $target = 'price';
        $price = $_GET['price'];
    }

?>
<?php switch( $target ){
    case 'category':
     $cat_name = $_GET['name'];
     $cat_ID = selectSQL('sc_category',$w = array('parent_cat' => -1, 'name_seo' => $cat_name));
    if(count($cat_ID) == 0){
        print "";
        break;
    }
     
     $cat_ID = $cat_ID[0]['ID_cat'];

     ?>
         <option value="">Subcategoría</option>
            <?
            // categorias fercode: ord ASC
            $parent=selectSQL("sc_category",$where=array('parent_cat' => -1),"ord ASC");
            for($j=0;$j<count($parent);$j++){
                $otros_html='';
                $parentSelected = false;
                $child=selectSQL("sc_category",$where=array('parent_cat' => $parent[$j]['ID_cat']),"name ASC");
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
        
    
    <?php break; ?>

    <?php case 'casas_details' : ?>
        <div class="min_filter_container">
            <select id="room_0" class="min_filter">
                <option value="">Habitaciones Desde</option>
                <? for($i=1; $i<10; $i++){?>
                <option value="<?=$i;?>" ><?=$i?></option>
                <? }?>
            </select>
            <select id="room_1" class="min_filter">
                <option value="">Habitaciones Hasta</option>
                <? for($i=1; $i<10; $i++){?>
                <option value="<?=$i;?>"><?=$i?></option>
                <? }?>
            </select>
            <select id="broom_0" class="min_filter">
                <option value="">Baños Desde</option>
                <? for($i=1; $i<10; $i++){?>
                <option value="<?=$i;?>"><?=$i?></option>
                <? }?>
            </select>
            <select id="broom_1" class="min_filter">
                <option value="">Baños Hasta</option>
                <? for($i=1; $i<10; $i++){?>
                <option value="<?=$i;?>"><?=$i?></option>
                <? }?>
            </select>
        </div>
    <?php break; ?>

    <?php case 'casas' : ?>
        <div class="min_filter_container">
            <select id="area_0" class="min_filter">
                <option value="">Superficie Desde</option>
                <? for($i=1; $i<10; $i++){?>
                <option value="<?=$i*10;?>"><?=number_format($i*10,0,',','.')?></option>
                <? }?>
                <? for($i=1; $i<=100; $i++){?>
                <option value="<?=$i*10;?>" ><?=number_format($i*100,0,',','.')?></option>
                <? }?>
            </select>
            <select id="area_1" class="min_filter">
                <option value="">Superficie Hasta</option>
                <? for($i=1; $i<10; $i++){?>
                <option value="<?=$i*10;?>"><?=number_format($i*10,0,',','.')?></option>
                <? }?>
                <? for($i=1; $i<=10; $i++){?>
                <option value="<?=$i*100;?>"><?=number_format($i*100,0,',','.')?></option>
                <? }?>
            </select>     
        </div>
    <?php break; ?>

    <?php case 'coches' : ?>
        <div class="min_filter_container">
            <select id="date_car_0" class="min_filter">
                <option value="">Año Desde</option>
                <? for($i=1950; $i<=date("Y",time()); $i++){?>
                <option value="<?=$i;?>"><?=$i?></option>
                <? }?>
            </select>
            <select id="date_car_1" class="min_filter">
                <option value="">Año Hasta</option>
                <? for($i=date("Y",time()); $i>1950; $i--){?>
                <option value="<?=$i;?>"><?=$i?></option>
                <? }?>
            </select>
            <select id="km_car_0" class="min_filter">
                <option value="">Km Mín.</option>
                <? for($i=1; $i<10; $i++){?>
                <option value="<?=$i*1000;?>"><?=number_format($i*1000,0,',','.')?></option>
                <? }?>
                <? for($i=1; $i<=10; $i++){?>
                <option value="<?=$i*10000;?>"><?=number_format($i*10000,0,',','.')?></option>
                <? }?>
            </select>
            <select id="km_car_1" class="min_filter">
                <option value="">Km Máx.</option>
                <? for($i=1; $i<10; $i++){?>
                <option value="<?=$i*1000;?>"><?=number_format($i*1000,0,',','.')?></option>
                <? }?>
                <? for($i=1; $i<=10; $i++){?>
                <option value="<?=$i*10000;?>"><?=number_format($i*10000,0,',','.')?></option>
                <? }?>
            </select>
        </div>

        <select id="fuel_car">
                <option value="">Combustible</option>
                <? $type_fuel=selectSQL("sc_type_fuel",$w=array(),'ID_fuel ASC');
                for($i=0;$i<count($type_fuel);$i++){?>
                <option value="<?=$type_fuel[$i]['ID_fuel']?>"><?=$type_fuel[$i]['name']?></option>
                <? } ?>
        </select>
    <?php break; ?>

    <?php case 'price' : ?>
        <?php switch( $price ):
            case '1': ?>
                <select id="Mprice_0" class="min_filter">
                    <option value="">Precio Mín.</option>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=$i*500;?>" ><?=formatPrice($i*500)?></option>
                    <? }?>
                    <? for($i=1; $i<=18 ; $i++){?>
                    <option value="<?=5000+$i*500;?>"><?=formatPrice(5000+$i*500)?></option>
                    <? }?>
                    <? for($i=3; $i<=10 ; $i++){?>
                    <option value="<?=$i*5000;?>"><?=formatPrice($i*5000)?></option>
                    <? }?>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=50000 + (5000*$i);?>"><?=formatPrice(50000 + (5000*$i))?></option>
                    <? }?>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=10000*$i+100000;?>"><?=formatPrice(10000*$i+100000)?></option>
                    <? }?>
                </select>
                <select id="Mprice_1" class="min_filter">
                    <option value="">Precio Máx.</option>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=$i*500;?>" ><?=formatPrice($i*500)?></option>
                    <? }?>
                    <? for($i=1; $i<=18 ; $i++){?>
                    <option value="<?=5000+$i*500;?>"><?=formatPrice(5000+$i*500)?></option>
                    <? }?>
                    <? for($i=3; $i<=10 ; $i++){?>
                    <option value="<?=$i*5000;?>"><?=formatPrice($i*5000)?></option>
                    <? }?>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=50000 + (5000*$i);?>"><?=formatPrice(50000 + (5000*$i))?></option>
                    <? }?>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=10000*$i+100000;?>"><?=formatPrice(10000*$i+100000)?></option>
                    <? }?>
                </select>
            <?php break; ?>

            <?php case '2' : ?>
                <select id="Mprice_0" class="min_filter">
                    <option value="">Precio Mín.</option>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=$i*500;?>" ><?=formatPrice($i*500)?></option>
                    <? }?>
                    <? for($i=1; $i<=18 ; $i++){?>
                    <option value="<?=5000+$i*500;?>"><?=formatPrice(5000+$i*500)?></option>
                    <? }?>
                    <? for($i=3; $i<=10 ; $i++){?>
                    <option value="<?=$i*5000;?>"><?=formatPrice($i*5000)?></option>
                    <? }?>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=50000 + (5000*$i);?>"><?=formatPrice(50000 + (5000*$i))?></option>
                    <? }?>
                    <? for($i=1; $i<=18 ; $i++){?>
                    <option value="<?=50000*$i+100000;?>"><?=formatPrice(50000*$i+100000)?></option>
                    <? }?>
                    <? for($i=2; $i<=10 ; $i++){?>
                    <option value="<?=1000000*$i;?>"><?=formatPrice(1000000*$i)?></option>
                    <? }?>
                </select>
                <select id="Mprice_1" class="min_filter">
                    <option value="">Precio Máx.</option>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=$i*500;?>" ><?=formatPrice($i*500)?></option>
                    <? }?>
                    <? for($i=1; $i<=18 ; $i++){?>
                    <option value="<?=5000+$i*500;?>"><?=formatPrice(5000+$i*500)?></option>
                    <? }?>
                    <? for($i=3; $i<=10 ; $i++){?>
                    <option value="<?=$i*5000;?>"><?=formatPrice($i*5000)?></option>
                    <? }?>
                    <? for($i=1; $i<=10 ; $i++){?>
                    <option value="<?=50000 + (5000*$i);?>"><?=formatPrice(50000 + (5000*$i))?></option>
                    <? }?>
                    <? for($i=1; $i<=18 ; $i++){?>
                    <option value="<?=50000*$i+100000;?>"><?=formatPrice(50000*$i+100000)?></option>
                    <? }?>
                    <? for($i=2; $i<=10 ; $i++){?>
                    <option value="<?=1000000*$i;?>"><?=formatPrice(1000000*$i)?></option>
                    <? }?>
                </select>
            <?php break; ?>
            <?php case '3' : ?>
                
            <?php break; ?>
        
            <?php default : ?>
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
            <?php break; ?>

        <?php endswitch ?>
    <?php break; ?>

    <?php default : ?>
        <?= $_GET['target'];?>
    <?php   break; ?>
<?php }  ?>