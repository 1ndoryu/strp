<?php 

    $item_time_on = getConfParam('item_time_on');
    $item_time_notice = getConfParam('item_time_notice');
    $time = time() - ($item_time_on - $item_time_notice) * 3600 *24;
    $query = "SELECT a.ID_ad as ID, u.mail as mail, u.name as name FROM sc_ad as a, sc_user as u WHERE a.date_ad <= $time AND (a.motivo is NULL OR a.motivo = 0) AND u.ID_user = a.ID_user";
    $consulta = mysqli_query($Connection, $query);
	$anuncios=array();
	if($consulta){
		while($row = mysqli_fetch_array($consulta)){
			$anuncios[] = $row;
		}

	}
        
    foreach ($anuncios as $key => $anun) {
        CaducadoMail($anun['mail'], $anun['ID'], $anun['name']);
        updateSQL('sc_ad', array('motivo' => 7), array('ID_ad' => $anun['ID']));
    }

    $time -= 3600 * 24 * $item_time_notice;

    $query = "UPDATE sc_ad SET trash = 1, active = 0, date_trash = ".time().", motivo = 8, trash_comment = 'Anuncio caducado' WHERE date_ad <= $time AND motivo = 7";
    $consulta = mysqli_query($Connection, $query);





    
