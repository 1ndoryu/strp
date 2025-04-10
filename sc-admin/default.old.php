<?
$time = time()-24*60*60;
$recoger = mysqli_query($Connection, "SELECT * FROM sc_ad WHERE date_ad > '$time'");
$tot = countSQL("sc_ad",$w=array('date_ad'=> $time.">", 'renovate' => 0));
$tot1 = countSQL("sc_ad",$w=array('premium1'=>1));
$tot2 = countSQL("sc_ad",$w=array());
$tot3 = countSQL("sc_ad",$w=array('premium2'=>1));
$tot4 = countSQL("sc_user",$w=array());
$tot5 = countSQL("sc_ad",$w=array('date_ad'=> $time.">", 'renovate' => 1));
?>
<div class="col_single">
<h2><?=$language_admin['default.stats_title']?></h2>
<div class="admin_stats">
<span class="info_stats"><?=$language_admin['default.last_day']?></span>
<b class="stats_total"><? echo $tot; ?></b>
<span class="info_stats">Renovados en las últimas 24 horas</span>
<b class="stats_total"><?= $tot5; ?></b>
<span class="info_stats"><?=$language_admin['default.ads_total']?></span>
<b class="stats_total"><? echo $tot2; ?></b>
<span class="info_stats"><?=$language_admin['default.ads_premium1']?></span>
<b class="stats_total"><?=$tot1;?></b>
<span class="info_stats"><?=$language_admin['default.ads_premium2']?></span>
<b class="stats_total"><?=$tot3;?></b>
<span class="info_stats"><?=$language_admin['default.user_total']?></span>
<b class="stats_total"><?=$tot4;?></b>
</div>
</div>
