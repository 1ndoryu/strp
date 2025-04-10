<?php
    include("../../../settings.inc.php");
    if(isset($_GET['id']))
    {
        if(Orders::deletePending($_GET['id']))
            echo json_encode(array("status" => 1));
        else
            echo json_encode(array("status" => 0));
    }