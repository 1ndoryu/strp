<div class="item-list-bolsa">
    <div class="item-list-bolsa-img">
        <img src="<?=Images::getBolsaImg()?>"/>
    </div>
    <div class="item-list-bolsa-text">
        <h3><?=$ad['ad']['title']?></h3>
        <p class="mb-3"><?=$ad['ad']['texto']?></p>
        <?php if($ad['ad']['hor_start'] != $ad['ad']['hor_end']): ?>
            <p><strong>Horario:</strong> <?=$ad['ad']['hor_start']?> - <?=$ad['ad']['hor_end']?></p>
          
        <?php endif ?>
        
        <p><strong>Lugar:</strong> <?=$ad['ad']['location']?>/<?=stripslashes($ad['region']['name']); ?></p>
        <p><strong>Contacto:</strong> <a href="tel:+34<?=$ad['ad']['phone']?>"><?=$ad['ad']['phone']?></a></p>
        <div class="d-flex justify-content-between mt-3">
            <p><?=parseDate($ad['ad']['date_ad'], 'd/m/Y')?></p>
            <p><strong>Ref:</strong> <?= $ad['ad']['ref'] ?></p>
        </div>
        
    </div>
</div>