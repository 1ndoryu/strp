<?php 
    
function comprovarToken($token){
    $stoken=selectSQL("sc_config",$w=array('name_param'=>'ADMIN_TOKEN'));
    if(count($stoken) != 0){
        if($stoken[0]['value_param'] == $token)
            return true;
        else
            return false;
    }
}

if(isset($_POST['pass'])){

    $pass = md5($_POST['pass']);

    updateSQL("sc_config",$datos=array('value_param'=>$pass),$ww=array('name_param'=>'ADMIN_PASS'));
    updateSQL("sc_config",$datos=array('value_param'=>''),$ww=array('name_param'=>'ADMIN_TOKEN'));
    
    echo '<script type="text/javascript">


					location.href = "index.php" ;


			</script>';

}