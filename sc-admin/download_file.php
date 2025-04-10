<?
include("../settings.inc.php");
if(check_login_admin()){
	if(isset($_GET['f'])){
		download_file("backup/".$_GET['f'], $_GET['f']);
	}
}
?>