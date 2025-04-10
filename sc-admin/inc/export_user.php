<?php
		header("Content-Description: File Transfer"); 
	    header("Content-Type: application/force-download"); 
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Disposition: attachment; filename=\"user_".date('d-m-Y').".txt\";" );
        header("Content-Transfer-Encoding: binary");
?>
<? require_once("../../settings.inc.php");?>
<?
// LIST USERS
$listado="";
$usuarios=selectSQL("sc_user",$w=array('active'=>1),"ID_user DESC");
for($i=0;$i<count($usuarios);$i++){
	$listado.="".$usuarios[$i]['mail']."	".$usuarios[$i]['name']."\r\n";
}
print($listado);
?>
