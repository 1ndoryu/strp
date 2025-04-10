<?php
include("settings.inc.php");
header("Content-type: text/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>
 <rss xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://search.yahoo.com/mrss/" version="2.0">
	<channel>
		<title><![CDATA['.$TITLE_.']]></title>
		<description><![CDATA['.$DESCRIPTION_.']]></description>
		<link>'.getConfParam('SITE_URL').'</link>
		<lastBuildDate>'.date('D, j M Y H:i:s O',time()).'</lastBuildDate>
		<atom:link href="'.getConfParam('SITE_URL').'feed/" rel="self" type="application/rss+xml" />';
		
$ad=selectSQL("sc_ad",$wm=array(),'date_ad DESC LIMIT 40');
for($i=0;$i<count($ad);$i++){
	$img = selectSQL("sc_images",$a=array('ID_ad' => $ad[$i]['ID_ad']));
	echo '<item> 
		<title><![CDATA['.$ad[$i]['title'].']]></title>
		<link><![CDATA['.urlAd($ad[$i]['ID_ad']).']]></link>
		<description><![CDATA['.substr(strip_tags($ad[$i]['texto']),0,600).'...]]></description>';
		if(count($img)!=0) echo '
		<media:content>
		<media:thumbnail url="'.getConfParam('SITE_URL').IMG_ADS.$img[0]['name_image'].'" />
		</media:content>';
	echo '<pubDate>'.date('D, j M Y H:i:s O',$ad[$i]['date_ad']).'</pubDate>
		<guid>'.urlAd($ad[$i]['ID_ad']).'</guid>
		</item>';
}

echo '</channel>
</rss>';
?>