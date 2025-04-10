<?php
    include("../../../settings.inc.php");
    $response = Orders::getOrderAjax();
    echo json_encode($response);
    die();