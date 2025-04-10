<?
echo 'start update banners and premium \n';

include('settings.inc.php');

echo 'settings included \n';

include('sc-includes/php/cronjobs/premium_and_banner_update.php');

include('sc-includes/php/cronjobs/premium2_update.php');

echo 'everything ok \n';
?>