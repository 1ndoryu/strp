<?php
    include("../../../settings.inc.php");
    $response = Orders::completeOrderAjax();
    echo json_encode($response);
    die();