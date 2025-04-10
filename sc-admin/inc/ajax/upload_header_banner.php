<?php
include("../../../settings.inc.php"); 

if(isset($_FILES['banner']))
{   
    $ext = Images::getExtension($_FILES['banner']['name']);
    if(isset($_POST['responsive']))
        $name = "banner_header_r" . $ext;
    else
        $name = "banner_header" . $ext;
    $filaname = Images::uploadImages(IMG_BANNERS, $name, $_FILES['banner']['tmp_name']);
    if($filaname != "")
    {
        if(isset($_POST['responsive']))
            setConfParam('HEADER_BANNER_R', $filaname);
        else
            setConfParam('HEADER_BANNER', $filaname);
        $url = getConfParam('SITE_URL') . IMG_BANNERS . $filaname;
        die (json_encode(array("status" => true, "url" => $url)));
    }else
    {
        die (json_encode(array("status" => false)));
    }
}