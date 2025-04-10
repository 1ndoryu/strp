<?
User::updateFavorites();
$favoritos=array();
$result_=array();
$select=array();
if(isset($_COOKIE['fav']))
{
	foreach ($_COOKIE['fav'] as $name => $value) {
		$favoritos['ID_ad']=$value;
		$favoritos['active']=1;
		$favoritos['review']=0;
		$res=selectSQL('sc_ad',$favoritos);
		if($res != null && is_array($res) && count($res) > 0)
			$result_[]=$res;
	}
	for($i=0;$i<count($result_);$i++){
		$select[]=$result_[$i][0];
	}

}
// Variables Amigables //
$TAMANO_PAGINA = 20; 
$pagina = $_GET["pag"]; 
if (!$pagina){ 
 $inicio = 0; 
 $pagina=1; 
} else { 
 $inicio = ($pagina - 1) * $TAMANO_PAGINA;
}
$num_total_registros = count($select);
$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
////// CONSULTA BASE DATOS //////////
$FAVORITES_ADS = true;
if($num_total_registros == 0){
?>
<div class="no_item_founds">
<?=$language['favorites.no_favorites']?>
</div>
<?
}else{?>
<?php if(!isset($_GET['p'])): ?>
	<div class="favorite-title">
		<h1>FAVORITOS - VISITANTES</h1>
		<p>Si quieres guardar un anuncio en favoritos clica en el símbolo <i class="fa fa-heart" style="color: var(--rosa);"></i>.</p>
	</div>
<?php endif ?>

<?
if($total_paginas==$pagina)
	$limite=($num_total_registros-(($pagina-1)*$TAMANO_PAGINA))+$inicio;
else
	$limite=$TAMANO_PAGINA+$inicio;

for($i=$inicio;$i<$limite;$i++){ 
	$ad=getDataAd($select[$i]['ID_ad']);
	if($ad['ad'] == null)
		continue;
 ?>
<? include("item_list_gallery3.php"); ?>
<? 
} 
if($total_paginas != 0){ 
echo "<div id='paginas'>";
echo pag($num_total_registros, $TAMANO_PAGINA, $inicio);
echo "</div>";
}
?>
<? }?>
</div>
