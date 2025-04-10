<?php 
global $language;
?>
<div class="search d-none d-md-block" >

  <div class="center_content">
        <div class="row justify-content-center">
          <div class="col-1 text-right">
            <a href="<?=getConfParam('SITE_URL') ?>" class="home-link"><?=$language['content.txt_home']?></a>
          </div>
          <div class="col-3 px-1 ml-2">
            <div class="input_search w-100">
              <input itemprop="query-input" type="text" name="keyword_search" id="keyword_search2" required placeholder="<?=$language['content.keyword_search']?>" value="<?php if(isset($query_s)) echo $query_s;?>">
            </div>
          </div>
          <div class="col-3 px-1 d-none d-md-block">
              <select id="search_cat2" class="select2_custom w-100">
              <option value=""><?=$language['content.select_category']?></option>
              <?php
            // categorias fercode: ord ASC
              $parent = selectSQL("sc_category",$w=array('parent_cat'=>-1),"ord ASC");
              for($i=0;$i<count($parent);$i++){ 
                $child = selectSQL("sc_category",$ma=array("parent_cat"=>$parent[$i]['ID_cat']),"name ASC");
                  if(count($child)>1){
                ?>

                  <optgroup label="<?=$parent[$i]['name']; ?>">
                    
                  <?php
                    for($j=0;$j<count($child);$j++){
                    // Categoria Otros al final fercode:
                    if ((strpos($child[$j]['name'], 'Otros') !== false) || (strpos($child[$j]['name'], 'Otras') !== false)) {
                      $otros_html='<option value="'.$parent[$i]['name_seo'].'/'.$child[$j]['name_seo'].'">'.$child[$j]['name'].'</option>';
                    } else{
    
                  ?>
                      <option value="<?=$parent[$i]['name_seo']; ?>/<?=$child[$j]['name_seo']; ?>" <?php if(isset($_GET['se']) && $_GET['se']==$child[$j]['name_seo']) echo 'selected';?>><?=$child[$j]['name']; ?></option>
                      <?php 		}
                    }
                echo $otros_html;
                $otros_html='';
                ?>
                </optgroup>
                <?php
              }else{ ?>
                  <option value="<?=$parent[$i]['name_seo']; ?>" <?php if(isset($_GET['s']) && $_GET['s']==$parent[$i]['name_seo']) echo 'selected';?>><?=$parent[$i]['name']; ?></option>
              <?php }
              
              }
              ?>
            </select>
          </div>
          <div class="col-2 px-1 d-none d-md-block">
              <select id="region_search2" class="select2_custom w-100 ">
                <option value="" <?php if(isset($_GET['zone']) && $_GET['zone']=='espana') echo 'selected';?>>Toda España</option>
                <?php
                $region = selectSQL("sc_region", array() ,"name ASC");
                for($i=0;$i<count($region);$i++){ ?>
                <option value="<?=$region[$i]['name_seo']; ?>" <?php if(isset($_GET['zone']) && $_GET['zone']==$region[$i]['name_seo']) echo 'selected';?>><?=$region[$i]['name']; ?></option>
                <?php } ?>
              </select>
          </div>
          <div class="col-2 px-1 ">
            <span class="button_search transition" id="but_search_main2"><i class="fa fa-search"></i> <span class="d-none d-lg-inline" >Buscar anuncios</span> </span>
          </div>

        </div>
  </div>
</div>