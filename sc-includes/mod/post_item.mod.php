<?php

if(getConfParam('POST_ITEM_REG')==1){
	check_login();
}
if(isset($_POST['g-recaptcha-response'])){

	$Return = getCaptcha($_POST['g-recaptcha-response']);

	if(DEBUG)
		$Return =  json_decode(json_encode(['success' => true, 'score' => 1]));

    if($Return->success == true && $Return->score > 0.5){
		$en_revision=false;
		if(isset($_POST['category'])){
			if(verifyFormToken('postAdToken',$_POST['token']) || DEBUG){

				$datos_ad=array();
				$datos_ad['ID_cat']=$_POST['category'];
				$category_ad = selectSQL('sc_category',$w=array('ID_cat'=>$datos_ad['ID_cat']));
				$datos_ad['parent_cat']=$category_ad[0]['parent_cat'];
				$datos_ad['ID_region']=$_POST['region'];
				/*$datos_ad['ID_city']=$_POST['city'];*/
				$datos_ad['location']=$_POST['city'];
				$datos_ad['ad_type']=$_POST['ad_type'];
				$datos_ad['ad_type']=$_POST['ad_type'];
				$datos_ad['title']=$_POST['tit'];
				$datos_ad['title_seo']=toAscii($_POST['tit']);
				$datos_ad['texto']=htmlspecialchars($_POST['text']);
				$datos_ad['price']=$_POST['precio']?$_POST['precio']:0;
				$datos_ad['mileage']=$_POST['km_car'];
				$datos_ad['fuel']=$_POST['fuel_car'];
				$datos_ad['date_car']=$_POST['date_car'];
				$datos_ad['area']=$_POST['area'];
				$datos_ad['room']=$_POST['room'];
				$datos_ad['broom']=$_POST['bathroom'];
				$datos_ad['address']=$_POST['city'];
				$datos_ad['name']=formatName($_POST['name']);
				$datos_ad['phone']=$_POST['phone'];
				$datos_ad['whatsapp']=isset($_POST['whatsapp']) ? 1 : 0;
				$datos_ad['phone1']=$_POST['phone1'];
				$datos_ad['whatsapp1']=isset($_POST['whatsapp1']) ? 1 : 0;
				$datos_ad['seller_type']=$_POST['seller_type'];
				$datos_ad['notifications']=$_POST['notifications']?$_POST['notifications']:0;
			

				if(!isset($_SESSION['data']['ID_user'])){
						$checkUser = selectSQL("sc_user",$a=array('mail'=>$_POST['email']));
						if(count($checkUser) == 0){
							$pass = randomString(6);
							$datos_u = array(
								'name' => formatName($_POST['name']),
								'mail' => $_POST['email'],
								'phone' => $_POST['phone'],
//								'whatsapp' => $_POST['whatsapp'],
//								'phone1' => $_POST['phone1'],
//								'whatsapp' => $_POST['whatsapp1'],
								'pass' =>$pass,
								'date_reg'=>time(),
								'active'=>1
							);
							$result = insertSQL("sc_user",$datos_u);
							if($result){ 
								$id_user=lastIdSQL();
								mailRegister(formatName($_POST['name']),$_POST['email'],$pass);
							}
						}else {
							$id_user = $checkUser[0]['ID_user'];						
						}
				} else {						
					$id_user = $_SESSION['data']['ID_user'];				
				}

				$datos_ad['ID_user']=$id_user;
				$datos_ad['date_ad']=time();
				if(getConfParam('REVIEW_ITEM')==1){
					$datos_ad['review']=1;
				}
			
			
				/// COMPROBACIÓN
				/*$datos_ad['ID_city']!=0*/
				if($datos_ad['ID_region']!=0 && $datos_ad['ad_type']!=0 && $datos_ad['seller_type']!=0 && $datos_ad['ID_cat']!=0 && ($datos_ad['price']=="" || is_numeric($datos_ad['price'])) && $datos_ad['ID_user']>0){

					$insert=insertSQL("sc_ad",$datos_ad);
					$last_ad = lastIdSQL();
					if(isset($_POST['photo_name'])){
						foreach($_POST['photo_name'] as $photo => $name){
							updateSQL("sc_images",$data=array('ID_ad'=>$last_ad, 'position'=>$photo),$wa=array('name_image'=>$name));
						}
					}

					if($insert){
						checkRepeat($last_ad);
						if (!$datos_ad['notifications']) {
							mailAdNotNotification($last_ad);
						}
						
						mailNewAd($last_ad);
						echo '<script type="text/javascript">
						location.href = "'.$urlfriendly['url.premium'].$last_ad."/1".'";
						</script>';
					}
				}else {
					$error_insert=true;				
				}
			}
		}
    }else{
        echo '<div class="error_msg" id="error_category" style="display: block;">Eres un robot</div><br>';
    }
}
