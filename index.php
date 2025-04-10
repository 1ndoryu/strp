<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///


include("settings.inc.php");

Maintenance::init();
Statistic::initDays();

include(PATH ."sc-includes/html/header.php");

include(PATH ."sc-includes/html/content.php");

include(PATH ."sc-includes/html/footer.php");

include("cron.php");

?>
 
