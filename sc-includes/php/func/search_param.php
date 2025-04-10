<?
$casas=false;
$casas_detail=false;
$coches_motos=false;
$user_query=false;
$filter_ad=array('trash' => 0);
$filter_search=array();
$filter_ad['review']="1!=";
$filter_ad['active']="0!=";
$filter_ad['ID_order']="0";
$keyword_data;
$name_location;
$q="";
$city=0;
$region=0;
$cat=0;
$st=0;
$max_price=0;
$parent_cat=false;
$region_data = null;
$MAX_PAG = getConfParam('ITEM_PER_PAGE'); 
$pag = $_GET["pag"]; 
if (!$pag){ 
	$start = 0; 
	$pag=1; 
}else $start = ($pag - 1) * $MAX_PAG;
// Variables filtro

if(isset($_GET['u'])){
	$user_ad=selectSQL("sc_user",$w=array('ID_user'=>$_GET['u'],'active'=>1));
	if(count($user_ad)>0){
		$user_query=true;
		$filter_ad['ID_user']=$user_ad[0]['ID_user'];
	}else error404();
}
 if(isset($_GET['zone'])){
	$region=$_GET['zone'];
	if($region != 'espana'){
		$region_data=selectSQL("sc_region",$w=array('name_seo'=>$region));
		if(count($region_data)==0) 
			echo "<script>window.history.go(-1)</script>";
		$region_ID=$region_data[0]['ID_region'];
		$filter_ad['ID_region']=$region_ID;	

		if(isset($_GET['location']))
		{
			$name_location=Locations::getNameLocation($_GET['location']);
			$filter_search['title']= $name_location;
			$filter_search['location']= $name_location;
		}

		if(isset($_GET['fs']))
		{
			$keyword_data = Searchs::getKeyword($_GET['fs']);
			$query_keyword = $keyword_data['query'];
			$query_keyword = explode(",", $query_keyword);
			foreach ($query_keyword as $key => $value) {
				$value = trim($value);
				$filter_search['texto']= $value;
				$filter_search['title']= $value;
				$filter_search['location']= $value;
			}
		}
	}
}
if(isset($_GET['pob'])){
	$city=$_GET['pob'];
	$filter_ad['location']='%'.$city.'%';
	/*$city_data=selectSQL("sc_city",$w=array('name_seo'=>$city));
	if(count($city_data)==0) echo "<script>window.history.go(-1)</script>";
	$region_ID=$city_data[0]['ID_region'];
	$filter_ad['ID_region']=$region_ID;
	$filter_ad['ID_city']=$city_data[0]['ID_city'];*/
}
//Tipo de vendedor
if(isset($_GET['sell'])){
	
	if ($_GET['sell']=='private') {
		$filter_ad['seller_type']=1;
	}
	else if ($_GET['sell']=='professional') {
		$filter_ad['seller_type']=2;
	}
}
if(isset($_GET['se'])){
	$cat_data=selectSQL("sc_category",$w=array('name_seo'=>$_GET['se'],'parent_cat'=>'0>'));
	if(count($cat_data)==0) 
		echo "<script>window.history.go(-1)</script>";
	$cat_ID=$cat_data[0]['ID_cat'];
	$filter_ad['ID_cat']=$cat_ID;
	$category_parent=$cat_data[0]['parent_cat'];
	if($cat_data[0]['field_0']==1) $coches_motos=true;
	if($cat_data[0]['field_2']==1) $casas=true;
	if($cat_data[0]['field_3']==1) $casas_detail=true;
}	
if(isset($_GET['s']) && !isset($_GET['se'])){
	$cat_data=selectSQL("sc_category",$w=array('name_seo'=>$_GET['s'],));
	if(count($cat_data)==0) 
	{
		$cat_data = selectSQL("sc_category",$w=array('alias'=>$_GET['s'],));
		if(count($cat_data)==0)
			echo "<script>window.history.go(-1)</script>";
	}

	$cat_ID=$cat_data[0]['ID_cat'];
	$filter_ad['parent_cat']=$cat_ID;
	$category_parent=$cat_ID;
}
if($search_query){
	$q=$query_s;
	if($q)
	$filter_search['title']=$q;
	$filter_search['texto']=$q;
	if( intval($q) != 0 )
		$filter_search['ID_ad'] = $q;
	
}
//extra search 
if(isset($_GET['dis']) && $_GET['dis']>0){
	$filter_ad['dis']=$_GET['dis'];
}
if(isset($_GET['lang']) && !in_array("0", $_GET['lang'])){
	
	$filter_search['lang1']= $_GET['lang'];
	$filter_search['lang2']= $_GET['lang'];
}

if(isset($_GET['out']) && $_GET['out']>0){
	$filter_ad['out']=$_GET['out'];
}

if(isset($_GET['hor_start']) && $_GET['hor_start']!="" && isset($_GET['hor_end']) && $_GET['hor_end']!=""){
	$filter_ad['hor_start']= $_GET['hor_start'] . ">=" ;
	$filter_ad['hor_end']= $_GET['hor_end'] . "<=";
}


// Tipo de anuncio
if(isset($_GET['t']) && $_GET['t']>0){
	$filter_ad['type_ad']=$_GET['t'];
}
// Price
if(isset($_GET['min_pr']) && ($_GET['min_pr']!="")){
	if(isset($_GET['max_pr']) && ($_GET['max_pr']!="")){
		$filter_ad['price']=$_GET['min_pr'].",".$_GET['max_pr']."^";
	}else{
		$filter_ad['price']=$_GET['min_pr'].">=";
	}
}
if(isset($_GET['max_pr']) && ($_GET['max_pr']!="")){
	if(isset($_GET['min_pr']) && ($_GET['min_pr']!="")){
		$filter_ad['price']=$_GET['min_pr'].",".$_GET['max_pr']."^";
	}else{
		$filter_ad['price']=$_GET['max_pr']."<=";
	}
}
// FUEL
if(isset($_GET['fuel_car']) && $_GET['fuel_car']!==0){
	$filter_ad['fuel']=$_GET['fuel_car'];
}
// KM
if(isset($_GET['min_km_car']) && ($_GET['min_km_car']!="")){
	if(isset($_GET['max_km_car']) && ($_GET['max_km_car']!="")){
		$filter_ad['mileage']=$_GET['min_km_car'].",".$_GET['max_km_car']."^";
	}else{
		$filter_ad['mileage']=$_GET['min_km_car'].">=";
	}
}
if(isset($_GET['max_km_car']) && ($_GET['max_km_car']!="")){
	if(isset($_GET['min_km_car']) && ($_GET['min_km_car']!="")){
		$filter_ad['mileage']=$_GET['min_km_car'].",".$_GET['max_km_car']."^";
	}else{
		$filter_ad['mileage']=$_GET['max_km_car']."<=";
	}
}
// Date Car
if(isset($_GET['min_date_car']) && ($_GET['min_date_car']!="")){
	if(isset($_GET['max_date_car']) && ($_GET['max_date_car']!="")){
		$filter_ad['date_car']=$_GET['min_date_car'].",".$_GET['max_date_car']."^";
	}else{
		$filter_ad['date_car']=$_GET['min_date_car'].">=";
	}
}
if(isset($_GET['max_date_car']) && ($_GET['max_date_car']!="")){
	if(isset($_GET['min_date_car']) && ($_GET['min_date_car']!="")){
		$filter_ad['date_car']=$_GET['min_date_car'].",".$_GET['max_date_car']."^";
	}else{
		$filter_ad['date_car']=$_GET['max_date_car']."<=";
	}
}
// ROOM
if(isset($_GET['min_room']) && ($_GET['min_room']!="")){
	if(isset($_GET['max_room']) && ($_GET['max_room']!="")){
		$filter_ad['room']=$_GET['min_room'].",".$_GET['max_room']."^";
	}else{
		$filter_ad['room']=$_GET['min_room'].">=";
	}
}
if(isset($_GET['max_room']) && ($_GET['max_room']!="")){
	if(isset($_GET['min_room']) && ($_GET['min_room']!="")){
		$filter_ad['room']=$_GET['min_room'].",".$_GET['max_room']."^";
	}else{
		$filter_ad['room']=$_GET['max_room']."<=";
	}
}
// BATHROOM
if(isset($_GET['min_broom']) && ($_GET['min_broom']!="")){
	if(isset($_GET['max_broom']) && ($_GET['max_broom']!="")){
		$filter_ad['broom']=$_GET['min_broom'].",".$_GET['max_broom']."^";
	}else{
		$filter_ad['broom']=$_GET['min_broom'].">=";
	}
}
if(isset($_GET['max_broom']) && ($_GET['max_broom']!="")){
	if(isset($_GET['min_broom']) && ($_GET['min_broom']!="")){
		$filter_ad['broom']=$_GET['min_broom'].",".$_GET['max_broom']."^";
	}else{
		$filter_ad['broom']=$_GET['max_broom']."<=";
	}
}

// AREA
if(isset($_GET['min_area']) && ($_GET['min_area']!="")){
	if(isset($_GET['max_area']) && ($_GET['max_area']!="")){
		$filter_ad['area']=$_GET['min_area'].",".$_GET['max_area']."^";
	}else{
		$filter_ad['area']=$_GET['min_area'].">=";
	}
}
if(isset($_GET['max_area']) && ($_GET['max_area']!="")){
	if(isset($_GET['min_area']) && ($_GET['min_area']!="")){
		$filter_ad['area']=$_GET['min_area'].",".$_GET['max_area']."^";
	}else{
		$filter_ad['area']=$_GET['max_area']."<=";
	}
}
$show_list=false;
$show_gallery=false;
if(isset($_GET['view_style'])){
	switch($_GET['view_style']){
		case 0: $show_list=true; break;
		case 1: $show_gallery=true; break;
	}
}else $show_list=true;

// $or="date_premium2 DESC";
if(isset($_GET['ord'])){
	if ($_GET['ord']=='supply') {
		$filter_ad['ad_type']=1;
	}
	else if ($_GET['ord']=='demand') {
		$filter_ad['ad_type']=2;
	}
	/*switch($_GET['ord']){
		case "pd": $or.=", price ASC"; break;
		case "pa": $or.=", price DESC"; break;
		case "da": $or.=", date_ad DESC"; break;
		case "dd": $or.=", date_ad ASC"; break;
		default: $or.=", date_ad DESC";
	}*/
}

$or = "date_ad DESC";

//select premium 3 
if(isset($pag) && $pag > 1)
{
	$select_premium3=selectSQL("sc_ad",$filter_ad,"RAND() LIMIT 0,5", array("premium3"=>"1"));
	if(count($select_premium3)>0){
		$_max = $MAX_PAG - count($select_premium3);
		if($pag > 2)
			$_start = $start - count($select_premium3);
		else
			$_start = $start;
		$select=selectSQL("sc_ad",$filter_ad,$or." LIMIT ".$_start.",".$_max."",$filter_search);
		$select = array_merge($select, $select_premium3);
	}else
	{
		// $or.=", date_ad DESC";
		$select=selectSQL("sc_ad",$filter_ad,$or." LIMIT ".$start.",".$MAX_PAG."",$filter_search);
	}
}else
{
	$select=selectSQL("sc_ad",$filter_ad,$or." LIMIT ".$start.",".$MAX_PAG."",$filter_search);
}
// PAGINACION
$total_reg=countSQL("sc_ad",$filter_ad,"date_ad DESC",$filter_search);

$tot_pag = ceil($total_reg / $MAX_PAG);
if($total_reg < $MAX_PAG){ 
	if($total_reg == 0) $inn = 0; else $inn=1;
	$por_pagina = $total_reg;
}else{ 
	$inn = 1+($MAX_PAG*($pag-1));
	if($pag == $tot_pag)
		$por_pagina=$total_reg;
	else
		$por_pagina = $MAX_PAG*($pag);
}
?>