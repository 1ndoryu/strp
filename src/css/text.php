<?php
global $region_ID, $region_data, $cat_data, $title_list, $keyword_data;

$text = $cat_data[0]['text'];

if($keyword_data)
{
    $text = $keyword_data['text'];
    $text = str_replace("%ciudad%", $region_data[0]['name'], $text);
}

if($text != "")
{
?>

<div class="list-bottom-text">
    <h2><?=$title_list?></h2>
    <?php echo $text; ?>
</div>

<?php }?>