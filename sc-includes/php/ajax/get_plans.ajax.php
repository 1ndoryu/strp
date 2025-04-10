<?php
    include("../../../settings.inc.php");

    $planes = Payment::selectPlans();
    $data = array(
        'status' => 'success',
        'planes' => $planes[0],
        'monto' => $planes[1],
    );
    die(json_encode($data));