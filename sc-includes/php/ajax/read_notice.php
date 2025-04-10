<?php
    include("../../../settings.inc.php");

    if(isset($_POST['notice']))
    {
        Notice::readNotice($_POST['notice']);
        die(array('result' => 'ok'));
    }

    die(array('result' => 'error'));