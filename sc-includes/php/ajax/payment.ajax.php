<?php
    include("../../../settings.inc.php");
    $res = Payment::processPayment();
    die(json_encode($res));