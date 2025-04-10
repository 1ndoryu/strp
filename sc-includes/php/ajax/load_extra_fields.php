<?

///*     ScriptClasificados v8.0                     *///

///*     www.scriptclasificados.com                  *///

///*     Created by cyrweb.com. All rights reserved. *///

///*     Copyright 2009-2017                         *///



include("../../../settings.inc.php");




if(isset($_POST['cat'])){

	$category=selectSQL("sc_category",$w=array('ID_cat'=>$_POST['cat']));

	if($category[0]['field_0']==1){ ?>

<div class="row">

<div class="col_lft"><label>Kilómetros *</label></div>

<div class="col_rgt">
        <input name="km_car" type="number" id="km_car"  step="0.01" min="0" size="12" maxlength="12" /><span class="decimal_price"><b>Km</b></span>
        <div class="error_msg" id="error_km_car">Inidica los kilómetros</div>
</div>

</div>

<div class="row">

<div class="col_lft"><label>Año *</label></div>

<div class="col_rgt">
        <select name="date_car" id="date_car">

        <option value=""></option>

        <? for($i=date("Y",time());$i>1970;$i--){?>

        <option value="<?=$i?>"><?=$i?></option>

        <? } ?>

        </select>
        <div class="error_msg" id="error_date_car">Inidica el año</div>

</div>

</div>

<div class="row">

<div class="col_lft"><label>Combustible *</label></div>

<div class="col_rgt">
        <select name="fuel_car" id="fuel_car">

        <option value=""></option>

		<? $type_fuel=selectSQL("sc_type_fuel",$w=array(),'ID_fuel ASC');

		for($i=0;$i<count($type_fuel);$i++){?>

        <option value="<?=$type_fuel[$i]['ID_fuel']?>"><?=$type_fuel[$i]['name']?></option>

       	<? } ?>

        </select>
        <div class="error_msg" id="error_fuel_car">Inidica el combustible</div>

</div>

</div>

<?

	}

	if($category[0]['field_3']==1){ ?>

<div class="row">

<div class="col_lft"><label>Habitaciones *</label></div>

<div class="col_rgt">
        <input type="text" id="room" name="room" class="number" maxlength="2">
        <div class="error_msg" id="error_room">Inidica las Habitaciones</div>
</div>

</div>

<div class="row">

<div class="col_lft"><label>Baños *</label></div>

<div class="col_rgt">
        <input type="text" id="bathroom" name="bathroom" class="number" maxlength="2">
        <div class="error_msg" id="error_bathroom">Inidica los Baños</div>
</div>

</div>

<?	}

	if($category[0]['field_2']==1){ ?>

<div class="row">

<div class="col_lft"><label>Superficie (m<sup>2</sup>) *</label></div>

<div class="col_rgt">
        <input type="number" id="area" name="area" maxlength="6" step="0.01"><span class="decimal_price"><b>m<sup>2</sup></b></span>
        <div class="error_msg" id="error_area">Inidica la superficie  m<sup>2</sup></div>
</div>

</div>

<?	}

}





?>