<?
global $TITLE_, $DESCRIPTION_, $KEYWORDS_;

$site_name_seo=getConfParam('SITE_NAME');

$TITLE_= getConfParam('SEO_TITLE');
$DESCRIPTION_= getConfParam('SEO_DESC');
$KEYWORDS_= getConfParam('SEO_KEYWORD');
$H1_SEO = "Anuncios clasificados gratis, móviles, compra venta, deportes y ocio o productos del hogar y mucho más.";
$H2_SEO = "";
$zoneSEO = "";
$TYPE_SITE="website";
$IS_MOBILE = isMobileDevice();
$locationSEO = "";

//seo map
if(isset($_GET['s']))
{
	if($_GET['s'] == "masajistas-terapeuticos")
		$_GET['s'] = "masajes-terapeuticos";
}

if(isset($_GET['zone'])){
	if(count($zona = selectSQL("sc_region",$a=array('name_seo' => $_GET['zone']))) != 0)
	{
		$zoneSEO = " ".$zona[0]['name'];
		$locationSEO = $zona[0]['name'];

	}
	}elseif(isset($_GET['pob'])){
		if(count($pob = selectSQL("sc_city",$a=array('name_seo' => $_GET['pob']))) != 0)
		{
			$locationSEO = $pob[0]['name'];
			$zoneSEO = " ".$pob[0]['name'];
		}	
}
if(isset($_GET['location']))
{
	$locationSEO=Locations::getNameLocation($_GET['location']);
}
$id_page="";
if(isset($_GET['id'])) $id_page=$_GET['id'];

switch($id_page) {
	case "register":
		$TITLE_= $language['seo.title_register'].getConfParam('SITE_NAME');
		$H1_SEO = "Regístrate en ".getConfParam('SITE_NAME')." y vende todo lo que ya no utilizas!";
		$H2_SEO = "Anuncios clasificados gratis";
	break;
	case "favorites":
		$TITLE_= $language['seo.title_favorites'].getConfParam('SITE_NAME');
	break;
	case "contact":
		$TITLE_= $language['seo.title_contact'].getConfParam('SITE_NAME');
	break;
	case "terms":
		$TITLE_= $language['seo.title_terms'].getConfParam('SITE_NAME');
	break;
	case "account":
		$TITLE_= $language['seo.title_account'].getConfParam('SITE_NAME');
	break;
	case "edit":
		$TITLE_= $language['seo.title_edit'].getConfParam('SITE_NAME');
	break;
	case "premium":
		$TITLE_= $language['seo.title_premium'].getConfParam('SITE_NAME');
	break;
	case "post_item":
		$TITLE_=$language['seo.title_post'].getConfParam('SITE_NAME');
		$DESCRIPTION_=$language['seo.description_post'];
		$H1_SEO="Publicar anuncios gratis";
		$H2_SEO="Poner anuncios clasificados en España";
	break;
	case "list":
		$search_query=false;
		if(isset($_GET['q']) && $_GET['q']!=""){
			$search_query=true;
			$query_s=invertQuerySearch($_GET['q']);
			
		}
		include("search_param.php");
		if($search_query){
			$TITLE_= ucwords($query_s).$zoneSEO." - Anuncios Clasificados Gratis";
			$DESCRIPTION_=ucwords($query_s).$zoneSEO." clasificados. Anuncios de segunda mano".$zoneSEO.". Encuentra ".ucwords($query_s).". Publica tus anuncios clasificados gratis.";
			$H1_SEO=ucwords($query_s).$zoneSEO;
			$H2_SEO="Anuncios Clasificados Gratis".$zoneSEO;
		}else{
			if(isset($filter_ad['ID_cat'])){
				
				if($cat_data[0]['seo_title']!=""){
					$TITLE_= $cat_data[0]['seo_title'].$zoneSEO;
				}else{
					$TITLE_= "Anuncios clasificados de ".$cat_data[0]['name'].$zoneSEO." - ".getConfParam('SITE_NAME');
				}
				
				if($cat_data[0]['seo_desc']!=""){
					$DESCRIPTION_=$cat_data[0]['seo_desc'].$zoneSEO;
				}else{
					$DESCRIPTION_="Clasificados de ".$cat_data[0]['name'].$zoneSEO.". Anuncios gratis de ".$cat_data[0]['name'].$zoneSEO.". ".$cat_data[0]['name']." de segunda mano".$zoneSEO.". Publica tus anuncios clasificados gratis.";	
				}
				
		
				$KEYWORDS_=$cat_data[0]['seo_keys'].",".$zoneSEO;
				
				
				$H1_SEO= "Clasificados de ".$cat_data[0]['name'].$zoneSEO;
				$H2_SEO="Anuncios de ".$cat_data[0]['name'].$zoneSEO;
			}elseif(isset($filter_ad['parent_cat'])){
				
				if($cat_data[0]['seo_title']!=""){
					$TITLE_= $cat_data[0]['seo_title'];
					if($zoneSEO!="")
					{	
						$TITLE_= $cat_data[0]['seo_title_city'];
						$TITLE_= str_replace("%ciudad%", $locationSEO, $TITLE_);

					}
				}else{
					$TITLE_= "Anuncios clasificados de ".$cat_data[0]['name'].$zoneSEO." - ".getConfParam('SITE_NAME');
				}
				
				if($cat_data[0]['seo_desc']!=""){
					$DESCRIPTION_=$cat_data[0]['seo_desc'];
					if($zoneSEO!="")
					{	
						$DESCRIPTION_=$cat_data[0]['seo_desc_city'];
						$DESCRIPTION_= str_replace("%ciudad%", $locationSEO, $DESCRIPTION_);
					}
				}else{
					$DESCRIPTION_="Clasificados de ".$cat_data[0]['name'].$zoneSEO.". Anuncios gratis de ".$cat_data[0]['name'].$zoneSEO.". ".$cat_data[0]['name']." de segunda mano".$zoneSEO.". Publica tus anuncios clasificados gratis.";
				}
				
		
				$KEYWORDS_=$cat_data[0]['seo_keys'];
				if($zoneSEO!="")
				{	
					$KEYWORDS_=$cat_data[0]['seo_keys_city'];
					$KEYWORDS_= str_replace("%ciudad%", $locationSEO, $KEYWORDS_);
				}
				
				if(isset($keyword_data) && !empty($keyword_data))
				{
					$TITLE_= str_replace("%ciudad%", $locationSEO, $keyword_data['title']);
					$DESCRIPTION_= str_replace("%ciudad%", $locationSEO, $keyword_data['description']);
				}
								
				$H1_SEO= "Clasificados de ".$cat_data[0]['name'].$zoneSEO;
				$H2_SEO="Anuncios de ".$cat_data[0]['name'].$zoneSEO;
			}else{
			$TITLE_= $language['seo.title_list'].$zoneSEO." - ".getConfParam('SITE_NAME');
			$DESCRIPTION_="Anuncios clasificados gratis".$zoneSEO.". Anuncios de segunda mano".$zoneSEO.". Publica tus anuncios clasificados gratis.";
			$H1_SEO=$language['seo.title_list'].$zoneSEO;
			$H2_SEO="Anuncios de segunda mano".$zoneSEO;
			}
		}
	break;
	case "item":
		if((isset($_GET['i'])) && (!empty($_GET['i']))){
			$ad=getDataAd($_GET['i']);
			if(count($ad) != 0){
				if($ad['ad']['active']==1 || $ad['ad']['active']==2){
					$have_image=false;
					$visitas = $ad['ad']['visit']+1;
					updateSQL("sc_ad",$b=array('visit' => $visitas),$c=array('ID_ad' => $ad['ad']['ID_ad']));	
					$img=array();
					for($i=0;$i<count($ad['images']);$i++)	$img[] = $ad['images'][$i]['name_image'];
					if(count($img)>0)	$have_image = true;
					else	$img[] = IMG_AD_DEFAULT;
					
					$TITLE_= ucfirst($ad['ad']['title']).$language['seo.in'].$ad['city']['name']." - ".getConfParam('SITE_NAME');
					$DESCRIPTION_= str_replace("\n\r", "", ucfirst(substr(mb_strtolower($ad['ad']['texto'],"UTF-8"), 0, 110)))."... Anuncios clasificados de ".$ad['category']['name'].$language['seo.in'].$ad['city']['name'];
					$H1_SEO=ucfirst($ad['ad']['title'])." en ".$ad['city']['name'];
					$H2_SEO="Anuncios clasificados de ".$ad['category']['name'];
					$TYPE_SITE="product";
					
				}else{
					  echo '<script type="text/javascript">
							location.href = "index.php";
							</script>';
				}
			}else{
				  echo '<script type="text/javascript">
						location.href = "index.php";
						</script>';
			}
		}else{
			  echo '<script type="text/javascript">
					location.href = "index.php";
					</script>';
		}
	break;
}
?>