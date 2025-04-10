<?php
global $region_ID, $region_bread_data, $cat_data, $region, $anchor_text, $request_uri, $name_location, $total_reg, $zona;
$columns = 4;

$anchor_text = $cat_data[0]['anchor_text'] != '' ? $cat_data[0]['anchor_text'] : $cat_data[0]['name'];

$request_uri = getConfParam('SITE_URL') . $_GET['s'];

if(isset($region_ID))
{
    $zona = $zona[0];
    $request_uri .= "-en-". $_GET['zone'] . "/";
    $distritos = Locations::getLocations($region_ID, LocationType::District);
    $ciudades = Locations::getLocations($region_ID, LocationType::City);
    $searchs = Searchs::getSearchs($cat_data[0]['ID_cat'], $zona);
}else
{
    if(is_array($region))
    {
        $searchs = array_map(function($re){
            global $anchor_text, $request_uri;
            $search = array();
            $search['query_search'] = $anchor_text . " en " . $re['name'];
            $search['query_url'] = $request_uri . "-en-" . $re['name_seo'] . "/";
            return $search;
        }, $region);
    }else if(isset($total_reg) && $total_reg > 0)
    {
        $searchs = selectSQL("sc_region", array(), "name ASC");

        $searchs = array_map(function($re){
            global $anchor_text, $request_uri;
            $search = array();
            $search['query_search'] = $anchor_text . " " . $re['name'];
            $search['query_url'] = $request_uri . "-en-" . $re['name_seo'] . "/";
            return $search;
        }, $searchs);
    }
}

if(isset($distritos) && count($distritos) > 0 && !isset($name_location))
{
    $c_distritos = Locations::makeRows($distritos, 4);

?>
<div class="links">
    <h3><?=$zona['name']?> Ciudad</h3>
    <hr>
    <div class="links-container">
        <?php foreach ($c_distritos as $key => $distritos): ?>
            <div class="links-column">
                <?php foreach ($distritos as $key => $distrito): ?>
                    <a href="<?=$request_uri?>localidad-<?=$distrito['name_seo']?>">
                        <?=$anchor_text ?> <?=$distrito['name'];?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<?php } ?>

<?php if(isset($ciudades) && count($ciudades) > 0 && !isset($name_location)):
    
    $c_ciudades = Locations::makeRows($ciudades, $columns);
?>
<div class="links">
    <h3>Provincia de <?=$zona['name']?></h3>
    <hr>
    <div class="links-container">
        <?php foreach ($c_ciudades as $key => $ciudades): ?>
            <div class="links-column">
                <?php foreach ($ciudades as $key => $ciudad): ?>
                    <a href="<?=$request_uri?>localidad-<?=$ciudad['name_seo']?>">
                    <?=$anchor_text ?> <?=$ciudad['name'];?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>


<?php 
    if(isset($searchs) && count($searchs) > 0 && !isset($_GET['busq']))
    {
    if(isset($region_ID))
        $c_searchs = Locations::makeColumns($searchs, $columns);
    else
        $c_searchs = Locations::makeRows($searchs, $columns);
?>
<div class="links">
    <h3>Los más buscados</h3>
    <hr>
    <div class="links-container">
        <?php foreach ($c_searchs as $key => $searchs): ?>
            <div class="links-column">
                <?php foreach ($searchs as $key => $search): ?>
                    <a href="<?=$search['query_url']?>">
                        <?=$search['query_search']?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php }?>