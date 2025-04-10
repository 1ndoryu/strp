<?
$exito_div=false;
if(isset($_GET['update_sitemap_region'])){
	generateSitemapRegion();
	$exito_div="Sitemaps de provincias actualizados";
}
if(isset($_GET['update_sitemap_ads'])){
	generateSitemapAds();
	$exito_div="Sitemap de anuncios actualizado";
}
if(isset($_GET['update_sitemap_index'])){
	generateSitemapIndex();
	$exito_div="Sitemap.xml actualizado";
}
if(isset($_GET['update_sitemap_search'])){
	generateSitemapSearch();
	$exito_div="Sitemap de búsquedas actualizado";
	
}
?>
<h2>Gestionar Sitemaps</h2>
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<ul class="list_categories">
	<li>
    	<span class="sitemap_name">Sitemap de Anuncios</span>
    	<span class="sitemap_url"><?=getConfParam('SITE_URL')?>sitemaps/sitemap_ads.xml</span>
    	<span class="sitemap_option"><a href="index.php?id=manage_sitemap&update_sitemap_ads">Actualizar</a></span>
    </li>
	<li>
    	<span class="sitemap_name">Sitemap de búsquedas</span>
    	<span class="sitemap_url"><?=getConfParam('SITE_URL')?>sitemaps/sitemap_search.xml</span>
    	<span class="sitemap_option"><a href="index.php?id=manage_sitemap&update_sitemap_search">Actualizar</a></span>
    </li>
	<li>
    	<span class="sitemap_name">Sitemap de provincias</span>
    	<span class="sitemap_url"><?=getConfParam('SITE_URL')?>sitemaps/sitemap_region.xml</span>
    	<span class="sitemap_option"><a href="index.php?id=manage_sitemap&update_sitemap_region">Actualizar</a></span>
    </li>
	<li>
    	<span class="sitemap_name">Sitemap.xml (Índice)</span>
    	<span class="sitemap_url"><?=getConfParam('SITE_URL')?>sitemap.xml</span>
    	<span class="sitemap_option"><a href="index.php?id=manage_sitemap&update_sitemap_index">Actualizar</a></span>
    </li>
</ul>