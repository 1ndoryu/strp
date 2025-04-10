<?
$error_div=false;
$exito_div=false;
// Borrar categoría padre
if(isset($_GET['delete'])){
	deleteSQL("sc_search", $da=array('ID_search'=>$_GET['delete']));
	$exito_div="Búsqueda eliminada";
}
// VARIABLES PARA PAGINACION //
$TAMANO_PAGINA = 40; 
$pagina = $_GET["pag"]; 
	if (!$pagina){ 
		$inicio = 0; 
		$pagina=1; 
	} else { 
		$inicio = ($pagina - 1) * $TAMANO_PAGINA; 
	}
// ------------------------- //
$orden_comun = " ID_search DESC limit " . $inicio . "," . $TAMANO_PAGINA . "";
$result = selectSQL("sc_search",$filter,$orden_comun);
$num_total_registros = countSQL("sc_search",$filter,"");
$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
if($num_total_registros <$MAX_PAG){ 
if($num_total_registros == 0){$inn = 0;}else{$inn=1;}
$por_pagina = $num_total_registros;
}else{ 
$inn = 1+($MAX_PAG*($pagina-1));
if($pagina == $total_paginas){ $por_pagina=$num_total_registros; }else{$por_pagina = $MAX_PAG*($pagina);}
}
?>
<h2>Gestionar Búsquedas</h2>

<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<ul class="list_categories">
<? for($i=0;$i<count($result);$i++){?>
	<li>
    	<span class="col_left"><?=$result[$i]['query_search'];?></span>
    	<span class="col_right">
        <a href="index.php?id=manage_search&delete=<? echo $result[$i]['ID_search']; ?>"><?=$language_admin['manage_categories.delete']?></a>
        </span>
    </li>
<? }?>
</ul>