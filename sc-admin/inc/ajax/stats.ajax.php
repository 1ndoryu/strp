<?php
	include("../../../settings.inc.php");

    $stats = Statistic::getCompleteStats();
    echo json_encode($stats);
?>