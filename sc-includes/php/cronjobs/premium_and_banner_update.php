<?

$banners = selectSQL('sc_banners', $w = array('active_thru' => time().'<', 'type' => '0'));

foreach ($banners as $banner){
	if(file_exists(ABSPATH . $banner['code']))
		if(unlink(ABSPATH . $banner['code']))
			deleteSQL('sc_banners', array('ID_banner' => $banner['ID_banner']));
}
$time = time();
$premiums = rawQuerySQL("SELECT ID_ad,ID_user FROM sc_ad WHERE premium1 = 1 AND date_premium1 < $time");

foreach ($premiums as $prem){
	updateSQL("sc_ad",$s=array('premium1'=>0, 'date_premium1'=>0),$w=array('ID_ad'=>$prem['ID_ad']));
	desactivateAd($prem['ID_ad'], $prem['ID_user']);
}
//caucidad de creditos
$user_credits = rawQuerySQL("SELECT ID_user FROM sc_user WHERE credits != 0 AND date_credits < $time and date_credits != 0");

foreach ($user_credits as $user)
{
	updateSQL("sc_user",$s=array('credits'=>0, 'date_credits'=>0),$w=array('ID_user'=>$user['ID_user']));
}

//premium 3
$premiums = rawQuerySQL("SELECT ID_ad, ID_user FROM sc_ad WHERE premium3 = 1 AND date_premium3 < $time");

foreach ($premiums as $prem){
	updateSQL("sc_ad",$s=array('premium3'=>0, 'date_premium3'=>0),$w=array('ID_ad'=>$prem['ID_ad']));
	desactivateAd($prem['ID_ad'], $prem['ID_user']);
}

//auto renovation
$limit = $time - getConfParam('AUTORENUEVA_TIME') * 60;
$type = renovationType::Autorenueva;
rawQuerySQL("UPDATE sc_ad SET date_ad = $time WHERE renovable = $type AND date_ad < $limit");

$autorenueva = rawQuerySQL("SELECT ID_ad, ID_user FROM sc_ad WHERE renovable != 0 AND renovable_limit < $time");
foreach ($autorenueva as $ad){
	updateSQL("sc_ad",$s=array('renovable'=>0, 'renovable_limit'=>0),$w=array('ID_ad'=>$ad['ID_ad']));
	desactivateAd($ad['ID_ad'], $ad['ID_user']);
}