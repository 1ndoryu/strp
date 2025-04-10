<?php
// credits 

    $credits_time = getConfParam('CREDITS_TIME') * 3600*24;

    $query = "SELECT credits_history, ID_user, credits FROM sc_user";

    $consulta = mysqli_query($Connection, $query);

	$users=array();
	if($consulta){
        while($row = mysqli_fetch_array($consulta)){
			$users[] = $row;
        }
    }

    foreach ($users as $user) {
        if($user['credits_history'] != ''){

            $history = json_decode($user['credits_history'], true);
            if( ($history[0]['date'] + $credits_time) <= time()){
                $nCredits = $user['credits'] - $history[0]['count'];
                $history =  array_slice($history, 1, count($history) - 1);
                updateSQL('sc_user', array('credits_history' => json_encode($history), 'credits' => $nCredits), array('ID_user' => $user['ID_user']));
            }

        }
    }

