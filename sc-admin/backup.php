<h2><?=$language_admin['backup.title_h1']?></h2>
<?php
$exito_backup=false;
$backups=get_files_root("backup/","zip");
if(count($backups) != 0){
	if(file_exists("backup/".$backups[0])){
		unlink("backup/".$backups[0]);
	}
}
if(isset($_POST['do_backup'])){
	createBackup();
	$backups=get_files_root("backup/","sql");
	if(count($backups) != 0){
		$zip = new ZipArchive();
		$sqlname = $backups[0];
		$name = str_replace('.sql','.zip', $sqlname);
		$zipname = "backup/" . str_replace('.sql','.zip', $sqlname);
		
		$zip->open($zipname, ZipArchive::CREATE);

		$res = $zip->addFile("backup/".$sqlname, $sqlname);

		$res = $res && $zip->close();

		if($res){
			unlink("backup/". $sqlname);
			$exito_backup=$language_admin['backup.created_backup'];
		}
	}

}
?>
<? if($exito_backup!==FALSE){?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_backup?></div>
<? } ?>
<form method="post" action="index.php?id=backup">
<?=$language_admin['backup.info_backup']?><input type="submit" name="do_backup" id="do_backup" value="<?=$language_admin['backup.button_generate']?>">
</form>
<hr />
<?php if( $exito_backup !== false ): ?>
	<h5>Copia De Seguridad Guenerada Descagar Aqui</h3>
	<a href="<?="descargar/".$name?>" target="_blank" download="<?=$name?>"><?=$language_admin['backup.download']?></a>	
<?php endif ?>

<!--
<h3><?=$language_admin['backup.title_h3']?></h3>
<ul class="list_categories">
	<? for($i=0;$i<count($backups);$i++){?>
    <li>
    	<span class="col_left"><?=$backups[$i];?></span>
    	<span class="col_right">
        <a href="download_file.php?f=<?=$backups[$i];?>" target="_blank"><?=$language_admin['backup.download']?></a>
        <a href="index.php?id=backup&f=<?=$backups[$i];?>"><?=$language_admin['backup.delete']?></a>
        </span>
    </li>
<? }?>
</ul>
	-->
