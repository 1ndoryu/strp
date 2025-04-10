<?

$ads = selectSQL('sc_ad', $a=array('premium2' => 1));



foreach ($ads as $adi){
    $current_time = time();
    //echo date('h:i:s', $current_time);
    $ad=getDataAd($adi['ID_ad']);
    
    // updateSQL("sc_ad",$s=array('price'=>7777),$w=array('ID_ad'=>$ad['ad']['ID_ad']));
    
    $time_tag = Service::getOption('LISTING', 'TIME_TAG');

    if ($ad['ad']['premium2_frecuency'] <= 0){
        if ($ad['ad']['date_premium2'] + ($time_tag*60) <= $current_time){
            updateSQL("sc_ad",$s=array(
                'premium2'=>0,
                'date_premium2'=>0,
                'premium2_frecuency'=>0,
               'premium2_night'=>0,
            ),$w=array('ID_ad'=>$ad['ad']['ID_ad']));
        }
        continue;
    }
    
    $credit_type='credits';
    $name = $ad['parent_cat']['name'];
    $adult_cont = getConfParam('CREDITS_ADULT_COUNT');
    //if($name == 'Contactos') $credit_type='credits_adult';
    
    $update_time = $ad['ad']['date_premium2'] + $ad['ad']['premium2_frecuency'];
    
    $up_date = date('d-m-Y H:i:s', $update_time);
    $cur_date = date('d-m-Y H:i:s', $current_time);
    $current_hour = date('h:i:s', $current_time);
    
    if ($update_time > $current_time)
        continue;
        
    if ($ad['user'][$credit_type] > 0){
        
        if ($ad['ad']['premium2_night'] || !($current_hour > '23:00:00' || $current_hour < '05:00:00')){
            
            updateSQL("sc_ad",$s=array(
                'date_premium2'=>$current_time, 
                'date_ad'=>$current_time
            ),$w=array('ID_ad'=>$ad['ad']['ID_ad']));

            if($name == "Contactos")
                $new_credits = $ad['user'][$credit_type] - $adult_cont;
            else
                $new_credits = $ad['user'][$credit_type] - 1;


            updateSQL("sc_user",$s=array($credit_type=>$new_credits),$w=array('ID_user'=>$ad['user']['ID_user']));
        }
        
    } else {
        
        updateSQL("sc_ad",$s=array(
            'premium2'=>0,
            'date_premium2'=>0,
            'date_ad' => $current_time,
            'premium2_frecuency'=>0,
        ),$w=array('ID_ad'=>$ad['ad']['ID_ad']));
        
        Service::inactiveByAd($ad['ad']['ID_ad'], 'premium2');
    }
    
    //echo 'add' . '\n';
}

?>