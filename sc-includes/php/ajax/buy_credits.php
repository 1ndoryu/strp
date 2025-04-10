<?php
    include("../../../settings.inc.php");

    $response = Payment::buyCredits();
    die(json_encode($response));