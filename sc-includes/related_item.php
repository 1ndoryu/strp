<?
$select=selectSQL("sc_ad",$w=array('ID_ad'=>$ad['ad']['ID_ad']."!=",'ID_cat'=>$ad['ad']['ID_cat'],'parent_cat'=>$ad['ad']['parent_cat'],'active'=>1,'review'=>0)," date_ad DESC LIMIT 4");
if(count($select)>0){
?>
<div class="col_single related">
<h3 class="title_main"><?=$language['related.title_section']?></h3>
<span class="separator"><i></i></span>
</div>
<ul class="related_ads_list">
<?
for($i=0;$i<count($select);$i++){
	include("item_list_gallery.php");
}
?>
</ul>
<?
}
?>
