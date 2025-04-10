<?php
global $region_ID, $region_data, $cat_data, $title_list, $keyword_data, $name_location;

if(isset($name_location) || (isset($region_ID) && $region_ID != 0))
{
    $text = $cat_data[0]['text_city'];
    if(isset($name_location))
    {
        $text = str_replace("%ciudad%", $name_location, $text);
    }else
    $text = str_replace("%ciudad%", $region_data[0]['name'], $text);
}
else
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