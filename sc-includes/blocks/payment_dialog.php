<?php
    $planes = Payment::getDataPlans();
    global $ANUNCIO_NUEVO_PREMIUM, $DATAJSON;

    if(isset($DATAJSON))
    {
        $DATAJSON['credit_price'] = getConfParam('CREDIT_PRICE');
    }
    
?>
<dialog class="payment">
    <div class="payment-modal" id="payment">
        <?php if(isset($ANUNCIO_NUEVO_PREMIUM)): ?>
            <a href="/" style="color: black;">
                <i class="fa-times-circle fa"></i>
            </a>
          <?php else: ?>
            <i onclick="closePayment()" class="fa-times-circle fa"></i>
        <?php endif ?>
        <section id="step-1" style="display: ;">
        <?php if(isset($ANUNCIO_NUEVO_PREMIUM) && false): ?>
                <div class="payment-steps">
                    <div class="payment-step">
                        <b>1</b>
                        <span>Elige un servicio</span>
                    </div>
                    <div class="payment-step">
                        <b>2</b>
                        <span>Publica tu anuncio</span>
                    </div>
                    <div class="payment-step">
                        <b>3</b>
                        <span>Espere que se apruebe</span>
                    </div>
                    <div class="payment-step">
                        <b>4</b>
                        <span>Realiza el pago</span>
                    </div>
                </div>
            <?php endif ?>

            <h1>¡DALE MÁS VISIBILIDAD A TU ANUNCIO!</h1>
            <p class="hightlight">
                HAZ QUE TE VEAN
            </p>
            
            <p class="underlined">
                Todos los precios tienen el IVA incluido.
            </p>
            <?php if(isset($_SESSION['data']['rol']) && $_SESSION['data']['rol'] == UserRole::Publicista): ?>
                <p>Descuentos para compras a partir de los 100€</p>
            <?php endif ?>

            <div class="cards">
                <span>
                    <img src="<?=Images::getImage("credits.svg")?>" alt="creditos">
                </span>
                <span>
                    <img src="<?=Images::getImage("card-logo-visa.svg")?>" alt="visa">
                </span>
                <span>
                    <img src="<?=Images::getImage("card-logo-mastercard.png")?>" alt="mastercard">
                </span>
                <span>
                    <img src="<?=Images::getImage("paypal_logo.png")?>" alt="paypal">
                </span>
                <span>
                    <img src="<?=Images::getImage("Bizum.png")?>" alt="bizum">
                </span>
                
            </div>
            <!-- <div class="arrow-container">
                <div class="arrow" onclick="scrollPaymentTable()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div> -->
            <?php if(isset($_SESSION['data']['ID_user']) && $_SESSION['data']['rol'] != UserRole::Particular): ?>
                <div class="publicista_design">
                    <img onclick="openDiscount()" src="<?=Images::getImage("descuento.png")?>" alt="publicista_design">
                </div>
            <?php endif ?>
            
            <form action="" id="payment_form">
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
                        
                                                    <input type="radio" name="<?=$plan?>"  value="<?=$days?>">
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
                        
                                                    <input type="radio" name="<?=$plan?>"  value="<?=$days?>">
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
            </form>
            <?php if(!isset($ANUNCIO_NUEVO_PREMIUM)): ?>
                <input type="hidden" name="idad" id="payment_idad">
                <div class="payment-footer">
                    <div class="col-payment-footer text-left">
                        
                    </div>
                    <div class="col-payment-footer text-right">
                  
                    </div>
                </div>
                
                <div class="payment-footer">
                    <div class="col-payment-footer col-big">
                        
                    </div>
                    <div class="col-payment-footer col-small">
                        <button class="payment-btn" onclick="createPayment()">
                            Continuar
                        </button>
                    </div>
                </div>
                <?php else: ?>
                    <div class="payment-footer">
                    <div class="col-payment-footer col-big">
                    </div>
                    <div class="col-payment-footer col-small">
                        <?php if(isset($ANUNCIO_NUEVO_PREMIUM)): ?>
                            <button class="payment-btn" onclick="createPayment(true)">
                                Continuar
                            </button>
                        <?php else: ?>
                            <button class="payment-btn" onclick="createPayment()">
                                Continuar
                            </button>
                          
                        <?php endif ?>
                       
                    </div>
                </div>
            <?php endif ?>

        </section>
        <section id="step-2" style="display: none;">
            <p class="hightlight black">
                Elige el método de pago
            </p>

            <div class="row_payment">
                <div class="col_primary">
                    <div class="metodos" id="payment_methods">
                        <div class="metodo" data-metodo="creditos">
                            
                            <i class="circle"></i>
                            <div class="metodo-desc">
                                <img src="<?=Images::getImage("credits.svg")?>" alt="creditos">
                                <span>Mis créditos</span>
                            </div>
                        </div>
                        <div class="metodo" data-metodo="paypal" >
                            
                            <i class="circle"></i>
                            <div class="metodo-desc">
                                <img src="<?=Images::getImage("card-logo-visa.svg")?>" alt="visa">
                                <img src="<?=Images::getImage("card-logo-mastercard.png")?>" alt="mastercard">
                                <img src="<?=Images::getImage("paypal_logo.png")?>" alt="paypal">
                            </div>
                        </div>
                        <!-- <div class="metodo" data-metodo="transferencia">
                        
                            <i class="circle"></i>

                            <div class="metodo-desc">
                                <p>TRANSFERENCIA BANCARIA</p>
                            </div>
                        </div> -->
                        
                        <div class="metodo" data-metodo="bizum">
                            
                            <i class="circle"></i>

                            <div class="metodo-desc">
                                <img src="<?=Images::getImage("Bizum.png")?>" alt="bizum">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col_secondary">
                    <div class="payment-details">
                        
                    </div>
                </div>
            </div>
            
            
        </section>
        <section id="step-3" style="display: none;">
            <div class="row_payment">
                <div class="col_primary">

                     <div id="paypal_module"></div>
                     
                </div>
                <div class="col_secondary">
                    <div class="payment-details">
                        
                    </div>
                </div>
            </div>
        </section>
        <section id="step-4" style="display: none;">
            <div class="row_payment">
                <div class="col_primary">
                    <div class="pending_container">
                        <div class="pending " id="bizum" style="display: none;">
                            <div class="cards mb-1">
                                <span>
                                    <img src="<?=Images::getImage("Bizum.png")?>" alt="bizum">
                                </span>
                            </div>
                            <p class="concept">
                                Concepto: <b class="number"></b>
                            </p>
                            <p class="concept mb-3">Número de teléfono: <strong>661.646.705</strong></p>
                            <p class="concept mb-4">
                                Tienes hasta 7 días para realizar el pago
                            </p>
                            <!-- <p>Valor a ingresar: <strong class="amount">12</strong><strong>€</strong></p> -->
                            <p class="light">Una vez hemos recibido el pago, tendrás el servicio activo en un plazo de 24 horas laborables.</p>
                            <p class="light">Si no has recibido el servicio en 72 horas, envíanos un correo a info@solomasajistas.com con el día de la transferencia y el concepto.</p>
                        </div>
                        <div class="pending" id="transferencia" style="display: none;">
                            <h6>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60" width="512" height="512"><path d="M13.577,59.2a3.256,3.256,0,0,0,2.139.8A3.326,3.326,0,0,0,17.1,59.7,3.212,3.212,0,0,0,19,56.75V55H52a3,3,0,0,0,3-3V40a3,3,0,0,0-3-3H50V24.1l8.874-7.658a3.22,3.22,0,0,0,0-4.893L46.423.8A3.251,3.251,0,0,0,42.9.3,3.212,3.212,0,0,0,41,3.25V5H8A3,3,0,0,0,5,8V20a3,3,0,0,0,3,3h2V35.9L1.126,43.554a3.22,3.22,0,0,0,0,4.893ZM8,21a1,1,0,0,1-1-1V8A1,1,0,0,1,8,7H42a1,1,0,0,0,1-1V3.25a1.217,1.217,0,0,1,.733-1.128,1.264,1.264,0,0,1,1.383.194L57.565,13.065a1.222,1.222,0,0,1,0,1.869L50,21.463V15a5.006,5.006,0,0,0-5-5H15a5.006,5.006,0,0,0-5,5v6Zm40-6V45a3,3,0,0,1-3,3H15a3,3,0,0,1-3-3V15a3,3,0,0,1,3-3H45A3,3,0,0,1,48,15ZM2.434,45.066,10,38.537V45a5.006,5.006,0,0,0,5,5H45a5.006,5.006,0,0,0,5-5V39h2a1,1,0,0,1,1,1V52a1,1,0,0,1-1,1H18a1,1,0,0,0-1,1v2.75a1.217,1.217,0,0,1-.733,1.128,1.259,1.259,0,0,1-1.383-.194L2.435,46.935a1.222,1.222,0,0,1,0-1.869Z"></path><path d="M44.079,22.631l-13.2-7.4a1.82,1.82,0,0,0-1.764,0h0l-13.2,7.4A1.8,1.8,0,0,0,16.8,26H17v2a1.993,1.993,0,0,0,1,1.722v9.462A3,3,0,0,0,16,42v1a2,2,0,0,0,2,2H42a2,2,0,0,0,2-2V42a3,3,0,0,0-2-2.816V29.722A1.993,1.993,0,0,0,43,28V26h.2a1.8,1.8,0,0,0,.883-3.369ZM30,17.028,42.432,24H17.568ZM38,39V30h2v9Zm-2,0H33V30h3Zm-5,0H29V30h2Zm-4,0H24V30h3Zm-5,0H20V30h2Zm20,4H18V42a1,1,0,0,1,1-1H41a1,1,0,0,1,1,1ZM41,28H19V26H41Z"></path></svg>
                                Transferencia bancaria
                            </h6>
                            <p class="concept">
                                Concepto: <b class="number"></b>
                            </p>
                            <p>Banco: <strong>BBVA</strong></p>
                            <p>Número de cuenta: <strong>ES74 0182 5326 0702 0035 2749</strong></p>
                            <p>Beneficiario: <strong>Adriana Rego</strong></p>
                            <!-- <p>Valor a ingresar: <strong class="amount">12</strong><strong>€</strong></p> -->
                            <p class="light">Una vez hemos recibido el pago, tendrás el servicio activo en un plazo de 24 horas laborables.</p>
                            <p class="light">Si no has recibido el servicio en 72 horas, envíanos un correo a info@solomasajistas.com con el día de la transferencia y el concepto.</p>
                        </div>
                    </div>
                     
                </div>
                <div class="col_secondary">
                    <div class="payment-details mt-4 pt-2">
                        
                    </div>
                </div>
            </div>
            <div class="delete-pending-container" style="display: none;">
                <button id="delete_pending" class="payment-btn delete-pending-btn">
                    Eliminar pedido
                </button>
            </div>
        </section>
     
        <section id="step-6" style="display: none;">
            
            <p class="hightlight black">
                <b>Descuento</b> para Publicistas
            </p>

            <div class="row_payment">
                <div class="col_primary">
                    <div class="metodos" id="payment_methods_extras">
                        <div class="metodo" data-metodo="paypal" >
                            <i class="circle"></i>
                            <div class="metodo-desc">
                                <img src="<?=Images::getImage("paypal_logo.png")?>" alt="paypal">
                                <img src="<?=Images::getImage("card-logo-visa.svg")?>" alt="visa">
                                <img src="<?=Images::getImage("card-logo-mastercard.png")?>" alt="mastercard">
                            </div>
                        </div>
                        <div class="metodo" data-metodo="creditos">
                                
                                <i class="circle"></i>
                                <div class="metodo-desc">
                                    <img src="<?=Images::getImage("credits.svg")?>" alt="creditos">
                                    <span>Mis créditos</span>
                                </div>
                            </div>
                            
                            <!-- <div class="metodo" data-metodo="transferencia">
                            
                                <i class="circle"></i>

                                <div class="metodo-desc">
                                    <p>TRANSFERENCIA BANCARIA</p>
                                </div>
                            </div> -->
                            
                            <!-- <div class="metodo" data-metodo="bizum">
                                
                                <i class="circle"></i>

                                <div class="metodo-desc">
                                <img src="<?=Images::getImage("Bizum.png")?>" alt="bizum">
                                </div>
                            </div> -->
                        </div>
                    <div id="paypal_container_extras" style="display: none;"></div>

                </div>
                <div class="col_secondary">
                    <div class="payment-details">
                        <div class="payment-details-row">
                            <h3> Total:</h3>
                            <p class="total"><span id="extra_anun_price">28</span>€</p>
                        </div>
                        <p id="extra_anun_services" class="services services_extras "></p>
                        <p class="info">
                            Este servicio no está habilitado para
                            empresas/autónomos de Ceuta y Melilla, ni tampoco fuera de España.
                        </p>
                    </div>
                </div>
            </div>
            
            
        </section>
    </div>
</dialog>

<dialog class="dialog" id="dialog_limits" >
    <div class="dialog-modal">
        <i onclick="this.parentElement.parentElement.close()" class="fa-times-circle fa"></i>
        <p id="limits_text">Falta horas para poder renovar</p>
        <!-- <a class="link-ren-premium" href="javascript:void(0);" id="limits_ren_premium" onclick="">Sube tu anuncio por 1 crédito</a> -->
        <!-- <p>Destaca tu anuncio con uno de nuestro planes de pago</p> -->
        <button  class="payment-btn" id="limits_payment_buttom">
            Ver opciones de pago
        </button>
    </div>
</dialog>
<dialog class="dialog" id="dialog_registred" >
    <div class="dialog-modal">
        <a style="color: black;" href="/" ><i class="fa-times-circle fa"></i></a>
        <p class="text-underline">Usuario Ya Registrado</p>
        <p class="mb-3" >Para publicar un anuncio accede a tu cuenta.</p>
        <!-- <p>Destaca tu anuncio con uno de nuestro planes de pago</p> -->
        <button onclick="gotToLogin()" class="payment-btn" >
            Acceder a mi cuenta
        </button>
    </div>
</dialog>

<dialog class="dialog" id="dialog_discount" >
    <div class="dialog-modal dialog-p-discount">
        <i onclick="this.parentElement.parentElement.close()" class="fa-times-circle fa"></i>
        <div class="payment-steps">
                <div class="payment-step">
                    <span>1 - Elige un <b>servicio</b></span>
                </div>
                <div class="payment-step">
                    <span>2- Realiza el <b>pago</b></span>
                </div>
                <div class="payment-step">
                    <span>3 - Publica tus <b>anuncios</b></span>
                </div>
                
            </div>
        <div class="position-relative">

            <h3>
                <b style="color: var(--rosa);">Descuento</b> para Publicistas
                <svg onclick="$('.publicista-info').toggleClass('hidden')" height="30" viewBox="0 0 512 512" width="30" xmlns="http://www.w3.org/2000/svg"><g id="e"><path d="m280.16 410.08c0-34.7 13.51-67.33 38.05-91.87s57.16-38.05 91.87-38.05c28.49 0 55.58 9.12 77.95 25.96 1.24-18.12 2.01-38.39 2.04-61.08-.06-57.07-4.85-98.86-9.96-129.57-8.94-50.6-54.9-96.56-105.5-105.5-30.71-5.12-72.5-9.91-129.58-9.97-57.07.06-98.87 4.85-129.58 9.96-50.59 8.94-96.55 54.9-105.49 105.5-5.11 30.71-9.89 72.5-9.96 129.57.07 57.07 4.85 98.87 9.96 129.58 8.94 50.6 54.9 96.56 105.5 105.5 30.71 5.11 72.5 9.89 129.58 9.96 22.69-.03 42.96-.8 61.08-2.04-16.84-22.37-25.96-49.45-25.96-77.95zm-156.2-291.66h242.15c15.66 0 28.36 12.7 28.36 28.36s-12.7 28.36-28.36 28.36h-242.15c-15.66 0-28.36-12.7-28.36-28.36s12.7-28.36 28.36-28.36zm0 175.15c-15.66 0-28.36-12.7-28.36-28.36s12.7-28.36 28.36-28.36h142.15c15.66 0 28.36 12.7 28.36 28.36s-12.7 28.36-28.36 28.36z"/><path d="m410.08 308.16c-56.29 0-101.92 45.63-101.92 101.92s45.63 101.92 101.92 101.92 101.92-45.63 101.92-101.92-45.63-101.92-101.92-101.92zm19 152.49c0 10.49-8.51 19-19 19s-19-8.51-19-19v-50.13c0-10.49 8.51-19 19-19s19 8.51 19 19zm-.78-90.8c-.7 3.96-4.3 7.56-8.26 8.26-2.4.4-5.68.77-10.15.78-4.47 0-7.74-.38-10.15-.78-3.96-.7-7.56-4.3-8.26-8.26-.4-2.4-.77-5.68-.78-10.15 0-4.47.38-7.74.78-10.15.7-3.96 4.3-7.56 8.26-8.26 2.4-.4 5.68-.77 10.15-.78 4.47 0 7.74.38 10.15.78 3.96.7 7.56 4.3 8.26 8.26.4 2.4.77 5.68.78 10.15 0 4.47-.38 7.74-.78 10.15z"/></g></svg>
            </h3>
            <div class="publicista-info hidden">
                <i onclick="$('.publicista-info').addClass('hidden')" class="fa fa-times-circle close-info"></i>
                <p>Elige el plan de acuerdo con la cantidad de anuncios en tu cuenta.</p>
                <p>Tendrás 48 horas para publicar o activar tus anuncios, sino el servicio se 
                activará  automaticamente.</p>
                <p>Podrás publicar o activar los anuncios ya publicados.</p>
                <p>El servicio tiene una caducidad de 30 días, si no lo usas se perderá.</p>
                <p>Para que el anuncio se valide más rápido publica en horario comercial.</p>
            </div>
        </div>
        <div class="p-discount-display">
            <form action="" id="form_discount" >

                <label class="p-discount-container" >
                    <p>
                        <input type="radio" name="p_discount"  value="5">
                        5 anuncios
                    </p>
                    <p>
                        30 días <strong>10€</strong>
                    </p>
                </label>
                <label class="p-discount-container" >
                    <p>
                        <input type="radio" name="p_discount"  value="10">
                        10 anuncios
                    </p>
                    <p>
                        30 días <strong>18€</strong>
                    </label>
                </div>
            </form>
            <div class="text-center my-2 p-discount-buttons">
                <button onclick="pDiscount()" class="btn btn-standar ">Siguiente</button>
                <button onclick="$('#dialog_discount')[0].close()" class="btn btn-main btn-standar">Cancelar</button>
            </div>
            
            <p class="info-payment">Al clicar en pagar implica haber leído y aceptado nuestras condiciones de uso.</p>
        
    </div>
</dialog>


<?php if(isset($ANUNCIO_NUEVO_PREMIUM)): ?>
    <dialog class="dialog"  <?=$ANUNCIO_NUEVO_PREMIUM ? "open" : "" ?> id="adlimits_msg" >
        <div class="dialog-modal">
            <a href="/index.php" style="color: black;">
                <i class="fa-times-circle fa"></i>
            </a>
            <p>Solo puedes tener 1 anuncio <b>GRATIS.</b></p>
            <p>Publica un anuncio <b>EXTRA</b> con nuestros <br> planes de pago</p>
           
            <button onclick="openPayment(); " class="payment-btn">
                Ver opciones de pago
            </button>
        </div>
    </dialog>
<?php endif ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?=PAYPAL_ID?>&currency=EUR&locale=es_ES"></script>
<script src="<?=JS_PATH?>payment.js"></script>