<?php
    include("../../../settings.inc.php");
    $res = Payment::buyExtraAds();
    die(json_encode($res));