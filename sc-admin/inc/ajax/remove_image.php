<?php
include("../../../settings.inc.php"); 

if(isset($_POST['id'])){
    $id = $_POST['id'];
    Images::deleteImage($id);
    
}