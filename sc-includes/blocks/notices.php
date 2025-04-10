<?php

if(isset($data['ID_user']))
{
    $notices = Notice::getNotices($data['ID_user'], true);
}else
{
    $notices = array();
}
?>
<div class="notices">
    <?php foreach ($notices as $notice) : ?>
        <div class="notice" data-id="<?=$notice['ID_notice']?>" style="display: none;">
            <div class="notice-icon">
                <img src="<?=Images::getImage('notification.svg', )?>" alt="link">
            </div>
            <div class="notice-content">
                <h6><?=$notice['title']?></h6>
                <p><?=$notice['text']?></p>
            </div>
            <div class="notice-acctions">
                
                <a data-id="<?=$notice['ID_notice']?>" href="<?=$notice['link'] !== null ? $notice['link'] : 'javascript:void(0)'?>" class="notice-btn notice-link">
                    <img src="<?=Images::getImage('click_here.svg', )?>" alt="link">
                </a>
                <button onclick="readNotice('<?=$notice['ID_notice']?>', this)" class="notice-btn notice-close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>notice.js"></script>