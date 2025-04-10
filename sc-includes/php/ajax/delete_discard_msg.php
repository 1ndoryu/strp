<?php
include("../../../settings.inc.php");

if(isset($_GET['id']))
{
    updateSQL('sc_ad', array('discard' => 0), array('ID_ad' => $_GET['id']));
}