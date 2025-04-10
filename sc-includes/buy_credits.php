<?php

$DATAJSON = array();
$DATAJSON['renovation_price'] = getConfParam('RE_COST');

?>
<div class="info_valid" style="display: none;"></div>
<div class="payment pricing">
    <h1 class="mb-4">Comprar créditos</h1>
    <p class="hightlight">
        Elige el método de pago
    </p>

    
    <div class="cards justify-content-left">
        
        <span>
            <img src="<?=Images::getImage("card-logo-visa.svg")?>" alt="visa">
        </span>
        <span>
            <img src="<?=Images::getImage("card-logo-mastercard.svg")?>" alt="mastercard">
        </span>
        <span>
            <img src="<?=Images::getImage("paypal_logo.png")?>" alt="paypal">
        </span>
        <span>
            <img src="<?=Images::getImage("Bizum.png")?>" alt="bizum">
        </span>
        <span>
            <img src="<?=Images::getImage("bank.svg")?>" alt="bizum">
        </span>
    </div>
    <div class="row_payment">
        <div class="col_primary">
            <div class="metodos" id="payment_methods_credits">
            
                <div class="metodo" data-metodo="paypal" >
                    
                    <i class="circle"></i>

                    <div class="metodo-desc">
                        <img src="<?=Images::getImage("paypal_logo.png")?>" alt="paypal">
                        <img src="<?=Images::getImage("card-logo-visa.svg")?>" alt="visa">
                        <img src="<?=Images::getImage("card-logo-mastercard.svg")?>" alt="mastercard">
                    </div>

                </div>
       
                
                <div class="metodo" data-metodo="bizum">
                    
                    <i class="circle"></i>

                    <div class="metodo-desc">
                        <img src="<?=Images::getImage("Bizum.png")?>" alt="bizum">
                    </div>

                </div>
            </div>
            <div id="paypal_container_credits" style="display: none;"></div>
        </div>
        <div class="col_secondary">
            <div class="payment-details credits-details">
                    
                <p class="total"><span id="credits_total">0</span>€</p>
                <p class="mb-0"><strong>Créditos:</strong></p>
                <input type="number" id="credit_amount" value="0" min="0"  step="1" class="numeric">
                <p class="error-credits" id="error_credits" style="display: none;">Compra mínima de 5 créditos <i class="fa-exclamation-triangle fa"></i></p>
                <p class="info">
                    Este servicio no está habilitado para
                    empresas/autónomos de Ceuta y Melilla, ni tampoco fuera de España.
                </p>
            </div>
        </div>
    </div> 
            
</div>