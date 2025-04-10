<?php
include("../../../settings.inc.php");

if (isset($_GET['cat'])) 
{
    $filter = Filter::getFilter($_GET['cat']);
    $json = json_encode($filter, JSON_UNESCAPED_UNICODE);
    die($json);
}