<?php
echo 'hola soy un cron \n';

include('settings.inc.php');

echo 'settings included \n';

include('sc-includes/php/cronjobs/premium2_update.php');
include('sc-includes/php/cronjobs/premium_and_banner_update.php');
include('sc-includes/php/cronjobs/ads_time.php');
//include('sc-includes/php/cronjobs/credits_time.php');

echo 'everything good \n';
?>
