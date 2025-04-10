<?php
    include("../../../settings.inc.php");

    $response = Orders::saveOrder();
    echo json_encode($response);
    die();