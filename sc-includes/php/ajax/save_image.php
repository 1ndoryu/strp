<?php

    include("../../../settings.inc.php");

    if(isset($_POST['image']) && isset($_POST['name']))
    {
        //Images::copyOriginalImage($_POST['name']);
       
        if(Images::putDataImage(ABSPATH . IMG_ADS, $_POST['name'], $_POST['image']))
        { 
            //Images::printStampWebp(ABSPATH . $path);
            //Images::createWebp($_POST['name']);
            Images::imageEdited($_POST['name']);
        ?>
            <div class="removeImg"><i class="fa fa-times" aria-hidden="true"></i></div>
            <a href="javascript:void(0);" class="edit-photo-icon" onclick="editImage(2)">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h357l-80 80H200v560h560v-278l80-80v358q0 33-23.5 56.5T760-120H200Zm280-360ZM360-360v-170l367-367q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L530-360H360Zm481-424-56-56 56 56ZM440-440h56l232-232-28-28-29-28-231 231v57Zm260-260-29-28 29 28 28 28-28-28Z"></path></svg>
            </a>
            <span class="helper"></span>
            <img class="cropped" src="<?=Images::getImage($_POST['name'], IMG_ADS, true, time())?>"/>
            <input type="hidden" name="photo_name[]" value="<?=$_POST['name']?>">
        <?php }
    }