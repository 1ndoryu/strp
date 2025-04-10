<?
if($search_query && $total_reg>0){
	$searches=selectSQL("sc_search",$w=array('query_search'=>'!='),'RAND() LIMIT 40',$b=array('query_search'=>$q));
	$title_search="Búsquedas relacionadas";
}else{
	$searches=selectSQL("sc_search",$w=array('query_search'=>'!='),'RAND() LIMIT 40');
	$title_search="Búsquedas recientes";
}
if(count($searches)>0){
?>
<div class="box_white search_related">
<span class="title_search"><?=$title_search?></span>
<ul>
<? for($i=0;$i<count($searches);$i++){ ?>
	<li><a href="<?=$searches[$i]['query_url'];?>"><?=$searches[$i]['query_search'];?></a></li>
<? } ?>
</ul>
</div>
<? } ?>