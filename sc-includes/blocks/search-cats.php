<?php
    $parent_cats = selectSQL("sc_category", $w = array('parent_cat'=> "-1"));
    $id = "";
    if(isset($data['id']))
        $id = 'id = "'.$data['id'].'"';
?>
<div class="search_cat_contaienr" <?=$id;?>>
    <div class="search_cat_text">
        Seleccionar categorias
    </div>
    <div class="search_cat_dialog" style="display: none;">
        <div class="search_cat_item">
            <label class="search_cat_name">
                <input data-id="0" type="checkbox" name="category[]" value="0">
                Todos
            </label>
        </div>
        <?php foreach($parent_cats as $cat){ ?>
            <div class="search_cat_item">
                <label class="search_cat_name">
                    <input data-id="<?=$cat['ID_cat'];?>" type="checkbox" name="category[]" value="<?=$cat['ID_cat'];?>">
                    <?=$cat['name'];?>
                </label>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    $(document).on('click','.search_cat_text', function(){
        if($(".search_cat_dialog").is(":visible"))
            $(".search_cat_dialog").hide();
        else
            $(".search_cat_dialog").show();
    });
</script>