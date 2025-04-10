<div class="col_single box_white pub_bottom">
    <? if(getBanner('97090')!=''){?>
    <?=getBanner('97090')?>
    <? } ?>
</div>
<?
$select = selectSQL("sc_ad",$where=array('premium1'=>1,'active'=>1,'review'=>0)," rand() LIMIT 4");
if(count($select)>0){
?>
<h3 class="title_main">TOP ANUNCIOS</h3>
<span class="separator"><i></i></span>
<div class="last_ads">
    <ul class="last_ads_list">
        <? for($i=0;$i<count($select);$i++){
			include("item_list_gallery.php");
		} ?>
    </ul>
</div>
<? } ?>


<div class="col_single box_white pub_bottom">
    <? if(getBanner('97090')!=''){?>
    <?=getBanner('97090')?>
    <? } ?>
</div>
