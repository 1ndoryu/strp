<?php
global $language, $cat_ID, $city, $region_ID;
?>
<div class="w-10 text-right filter-container">
    <button class="btn btn-filtro f-1600 d-none d-md-inline" id="filterbutton">
        Filtrar <i class="fa fa-chevron-down"></i>
    </button>
<div class="filter f-1200">
<div class="open_filter btn-t">
    <div class="btn-t-dsing">
        <span><i class="fa fa-search" aria-hidden="true"></i></span>
        <svg viewBox="-1.5 0 13.5 24" xmlns="http://www.w3.org/2000/svg">
        <polygon points="0,0 10,12 0,24" stroke="currentColor" stroke-width="3" fill="currentColor"></polygon>
        </svg>

    </div>
     Filtrar
</div>
<div class="filter_options">

<div class="form-item">
    <input type="text" name="q" id="q" class="form-control" placeholder="Buscar">
</div>

<div class="filter-custom-select">
    <select name="Maincategory" id="Maincategory" class="sel">
        <!-- <option value="" class="white">Categoría</option> -->
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
            <option value="<?=$parent[$j]['name_seo'];?>" <? if($cat_ID==$parent[$j]['ID_cat'] || $childSelected) echo 'selected';?>><?=$parent[$j]['name'];?></option><?
            } ?>
    </select>

</div>

<div class="filter-custom-select">
    <select name="region" id="region_">
        <option value="">Toda España</option>
        <? $provin = selectSQL("sc_region", array(), "name ASC");
            for($i=0;$i<count($provin);$i++){ ?>
        <option value="<?php echo $provin[$i]['name_seo']; ?>" <? if($region_ID==$provin[$i]['ID_region']){?>selected="selected"<? }?>><? echo $provin[$i]['name']; ?></option>
        <? } ?>
    </select>
</div>
<p class="r-show hidden" onclick="$('#filter_extra').toggle()"><strong>+ Filtros</strong></p>
<div id="filter_extra" <?= isMobileDevice() ? 'style="display: none;"' : '' ?>>
    <div class="filter-custom-select">
        <select name="dis" id="dis">
            <option value="0">Disponibilidad</option>
            <option <?=isset($_GET['dis']) && $_GET['dis']==1 ? 'selected="selected"' : ''?> value="1">Todos los días</option>
            <option <?=isset($_GET['dis']) && $_GET['dis']==2 ? 'selected="selected"' : ''?> value="2">Lunes a Viernes</option>
            <option <?=isset($_GET['dis']) && $_GET['dis']==3 ? 'selected="selected"' : ''?> value="3">Lunes a Sábado</option>
            <option <?=isset($_GET['dis']) && $_GET['dis']==4 ? 'selected="selected"' : ''?> value="4">Sábado y Domingo</option>
        </select>

    </div>
    <div class="row">
        <div class="col-6 pr-1">
            <div class="select-dialog">
                <span onclick="$('.select-dialog-content').toggle();$(this).toggleClass('active')" class="select-dialog-title">Idioma</span>
                <div class="select-dialog-content" style="display: none;">
                    <div class="select-dialog-item-r">
                        <label class="select-dialog-item">
                            <input type="checkbox" name="lang" value="0" <? if( isset($_GET['lang']) && $_GET['lang'] ==$i){?> checked="checked"<? }?> >
                            Todos
                        </label>
                    </div>
                    <?php for($i=1; $i <= Language::COUNT; $i++){ ?>
                        <label class="select-dialog-item">
                            <input type="checkbox" name="lang" value="<?=$i?>" <? if( isset($_GET['lang']) && $_GET['lang'] ==$i){?> checked="checked"<? }?> >
                            <?=Language::NAME($i)?>
                        </label>
                    <?php  } ?>
                </div>
            </div>
            
        </div>
        <div class="col-6 pl-1">
            <div class="filter-custom-select">
                <select name="out" id="out">
                    <option value="0">Salidas</option>
                    <option <?= isset($_GET['out']) &&  $_GET['out'] == "0" ? 'selected="selected"' : ''?> value="0">No</option>
                    <option <?= isset($_GET['out']) &&  $_GET['out'] == "1" ? 'selected="selected"' : ''?> value="1">Sí</option>
                </select>
            </div>
        </div>
    </div>
    <p>Horario</p>
    <div class="row">
        <div class="col-6 pr-1">
            <div class="filter-custom-select">
                <select name="horario-inicio" id="horario_inicio">
                    <option value="">Inicio</option>
                    <? for($i = 0; $i < 24; $i++){ 
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
        <div class="col-6 pl-1">
            <div class="filter-custom-select">
                <select name="horario-final" id="horario_final">
                    <option value="">Fin</option>
                    <? for($i = 0; $i < 24; $i++){ 
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
    </div>


</div>

<input type="button" name="search_filter" id="search_filter" value="Buscar">
<input type="hidden" id="url_now" value="<?=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);?>">
</div>
</div>
</div>

<script src="src/js/filter-select.js"></script>