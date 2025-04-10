
<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///
?>
<div id="breadcrumbs">
<ul class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
<?=generateBreadItem(getConfParam('SITE_URL'),$language['bread.home'],1);?>
<?
switch($_GET['id']){
	case "register":
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.contact'],$language['bread.register'],2);
	break;
	case "post_item":
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.post_item'],$language['bread.post'],2);
	break;
	case "favorites":
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.favorites'],$language['bread.favorites'],2);
	break;
	case "contact":
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.contact'],$language['bread.contact'],2);
	break;
	case "terms":
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.terms'],$language['bread.terms'],2);
	break;
	case "aviso-legal":
		echo generateBreadItem(getConfParam('SITE_URL').'aviso-legal/','Aviso Legal',2);
	break;
	case "cookies_policy":
		echo generateBreadItem(getConfParam('SITE_URL').'politica-de-cookies/','Politica de Cookies',2);
	break;
	case "proteccion_de_datos":
		echo generateBreadItem(getConfParam('SITE_URL').'proteccion-de-datos/','Proteccion de Datos',2);
	break;
	case "share-mail":
		echo generateBreadItem(getConfParam('SITE_URL').'compartir-email/','Compartir Email',2);
	break;
	
	case "account":
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.my_account'],$language['bread.my_account'],2);
	break;
	case "premium":
		$item_bread=getDataAd($_GET['i']);
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.premium'].$_GET['i'],$language['bread.premium'],2);
		echo generateBreadItem(urlAd($_GET['i']),$item_bread['ad']['title'],3);
	break;
	case "premium_2":
		$item_bread=getDataAd($_GET['i']);
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.premium_2'].$_GET['i'],$language['bread.premium_2'],2);
		echo generateBreadItem(urlAd($_GET['i']),$item_bread['ad']['title'],3);
	break;
	case "edit":
		$item_bread=getDataAd($_GET['i']);
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.my_account'],$language['bread.my_account'],2);
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.my_items'].$urlfriendly['url.my_items.edit'].$_GET['i'], $language['bread.edit'],3);
		echo generateBreadItem(urlAd($_GET['i']),$item_bread['ad']['title'],4);
	break;
	case "item":
		$item_bread=getDataAd($_GET['i']);		
		//echo generateBreadItem(getConfParam('SITE_URL').$item_bread['parent_cat']['name_seo']."/",$item_bread['parent_cat']['name'],4);
		echo generateBreadItem(getConfParam('SITE_URL').$item_bread['parent_cat']['name_seo']."-en-".$item_bread['region']['name_seo']."/", $item_bread['parent_cat']['anchor_text'] ." en ".$item_bread['region']['name'],5, "af-none");

		//echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.classifieds.in'].$item_bread['city']['name_seo']."/",$item_bread['city']['name'],3);
/*		
		if($item_bread['ad']['location']!=""){
			echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.classifieds.in'].strtolower($item_bread['ad']['location'])."/",$item_bread['ad']['location'],3);	
		}
*/
		//echo generateBreadItem(urlAd($_GET['i']),$item_bread['ad']['title'],6); //titulo
				//print_r($item_bread);
				//echo $item_bread['ad']['location'];
				//echo json_encode($item_bread);
	break;
	case "list":
		if(isset($_GET['s'])){
			$cat_bread_data=selectSQL("sc_category",$w=array('name_seo'=>$_GET['s'],'parent_cat'=>'0<='));
			echo generateBreadItem(getConfParam('SITE_URL').$cat_bread_data[0]['name_seo'],$cat_bread_data[0]['name']);
		}
		if(isset($_GET['se'])){
			$cat_bread_data=selectSQL("sc_category",$w=array('name_seo'=>$_GET['se'],'parent_cat'=>'0>'));
			$parent_bread_data=selectSQL("sc_category",$w=array('ID_cat'=>$cat_bread_data[0]['parent_cat']));
			//echo generateBreadItem(getConfParam('SITE_URL').$parent_bread_data[0]['name_seo']."/",$parent_bread_data[0]['name']);
			echo generateBreadItem(getConfParam('SITE_URL').$parent_bread_data[0]['name_seo']."/".$cat_bread_data[0]['name_seo'],$cat_bread_data[0]['name']);
		}
		if(isset($_GET['zone'])){
			$region_bread=$_GET['zone'];
			if($region_bread != 'espana'){
				$region_bread_data=selectSQL("sc_region",$w=array('name_seo'=>$region_bread));
				echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.classifieds.region'].$region_bread_data[0]['name_seo']."/",$region_bread_data[0]['name'],3);
			}else{
				echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.classifieds.region']."espana/","españa",3);
			}
		}else
		if(isset($_GET['pob'])){
			$city_bread=$_GET['pob'];
			$city_bread_data=selectSQL("sc_city",$w=array('name_seo'=>$city_bread));
			$region_data=selectSQL("sc_region",$w=array('ID_region'=>$city_bread_data[0]['ID_region']));
			echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.classifieds.region'].$region_data[0]['name_seo']."/",$region_data[0]['name'],3);
			echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.classifieds.in'].$city_bread_data[0]['name_seo']."/",$city_bread_data[0]['name'],4);
		}


		if($user_query){
			echo generateBreadItem("/usuario/".$user_ad[0]['ID_user'],formatName($user_ad[0]['name']),2);
		}
	break;
}
if(isset($_GET['amp'])){
	$item_bread=getDataAd($_GET['i']);
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.classifieds.region'].$item_bread['region']['name_seo']."/",$item_bread['region']['name'],2);
		echo generateBreadItem(getConfParam('SITE_URL').$urlfriendly['url.classifieds.in'].$item_bread['city']['name_seo']."/",$item_bread['city']['name'],3);
		echo generateBreadItem(getConfParam('SITE_URL').$item_bread['parent_cat']['name_seo']."/",$item_bread['parent_cat']['name'],4);
		echo generateBreadItem(getConfParam('SITE_URL').$item_bread['parent_cat']['name_seo']."/".$item_bread['category']['name_seo']."/",$item_bread['category']['name'],5);
		echo generateBreadItem(urlAd($_GET['i']),$item_bread['ad']['title'],6);
}
?>
</ul>
<?php if($_GET['id'] == "item"): ?>
	<a class="goback-button" onclick="window.close()" href="javascript:void(0);">
		<img src="<?=Images::getImage('volver.png')?>" width="50" height="50" alt="volver al listado">
	</a>
<?php endif ?>
</div>
