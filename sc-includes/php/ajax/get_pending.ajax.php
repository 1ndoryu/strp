<?php
    include("../../../settings.inc.php");

    $res = Orders::getPendingAjax();
    echo json_encode($res);
    die();
