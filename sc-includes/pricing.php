<?php
    $planes = Payment::getDataPlans();
?>
<div class="payment pricing">
    <h2>Nuestras tarifas</h2>
    <h1 class="mt-5">¡DALE MÁS VISIBILIDAD A TU ANUNCIO!</h1>

    <p class="hightlight">
        Cada crédito cuesta 1 €
    </p>

    <p class="underlined">
        Todos los precios tienen el IVA incluido.
    </p>

    <p>Descuentos para compras a partir de los 100€</p>

    <div class="cards">
        <!-- <img src="<?=Images::getImage("credits.svg")?>" alt="creditos"> -->
        <img src="<?=Images::getImage("card-logo-visa.svg")?>" alt="visa">
        <img src="<?=Images::getImage("card-logo-mastercard.svg")?>" alt="mastercard">
        <img src="<?=Images::getImage("paypal_logo.png")?>" alt="paypal">
        <img src="<?=Images::getImage("Bizum.png")?>" alt="bizum">
        <img src="<?=Images::getImage("bank.svg")?>" alt="transferencia">

    </div>
    <div class="publicista_design">
        <img src="<?=Images::getImage("descuento.png")?>" alt="publicista_design">
    </div>
    <?php if(isMobileDevice()): ?>
        <div class="payment_cards">
                <?php foreach($planes as $plan => $data):
                    if(!isset($ANUNCIO_NUEVO_PREMIUM) && $data['posted'] == 1)
                        continue;
                    ?>
                    <div class="payment-card">
                        <div class="payment-card-title">
                            <strong class="payment-title">
                                <?= $data['name'] ?>
                            </strong>
                            <?php if($data['comment'] != ''): ?>
                                <span><?= $data['comment'] ?></span>
                            <?php endif; ?>
                        </div>
                    

                        <div class="payment_card_options">
                            
                            <?php foreach($data['days'] as $days => $price): ?>
                                <?php if($price != 0): ?>
                                    
                                    <label class="payment-option">
            
                                        <!-- <input type="radio" name="<?=$plan?>"  value="<?=$days?>"> -->
                                        <span><?=$days?> <?= $data['counter'] == '' ? 'días' : $data['counter'] ?></span>
                                        <b>
                                            <?= $price ?> €
                                        </b>
                                    </label>
                                    
                                <?php endif ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                <?php endforeach; ?>
        </div>
        <?php else: ?>
        <table class="table table-payment table-responsive-md" id="payment_table">
            <tbody>
                <?php foreach($planes as $plan => $data):
                        if(!isset($ANUNCIO_NUEVO_PREMIUM) && $data['posted'] == 1)
                            continue;
                    ?>
                    <tr>
                        <td>
                            <div class="payment-col-table">
                                <strong class="payment-title">
                                    <?= $data['name'] ?>
                                </strong>
                                <?php if($data['comment'] != ''): ?>
                                    <span><?= $data['comment'] ?></span>
                                <?php endif; ?>
                                
                            </div>
                        </td>
                        <?php foreach($data['days'] as $days => $price): ?>
                            <?php if($price != 0): ?>
                                <td>
                                    <label class="payment-option">
            
                                        <!-- <input type="radio" name="<?=$plan?>"  value="<?=$days?>"> -->
                                        <span><?=$days?> <?= $data['counter'] == '' ? 'días' : $data['counter'] ?></span>
                                        <b>
                                            <?= $price ?> €
                                        </b>
                                    </label>
                                </td>
                            <?php endif ?>
                        <?php endforeach; ?>
                        
                    </tr>
                <?php endforeach; ?>
                
                
                
                
            </tbody>
        </table>
    <?php endif ?>

</div>
