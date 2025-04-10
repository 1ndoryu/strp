
<?php
    global $order_id;
    list($precio, $details) = Orders::getOrderDetails($order_id);
    list($total, $discount) = Payment::getDiscount($precio);
?>
<dialog class="payment" open>
    <div  class="payment-modal" id="payment">
        <?php if(!isset($_SESSION['usr'])): ?>
                <a href="/index.php" style="color: black;">
                    <i class="fa-times-circle fa"></i>
                </a>
            <?php else: ?>
                <a href="/mis-anuncios" style="color: black;">
                    <i class="fa-times-circle fa"></i>
                </a>
        <?php endif ?>
        <div class="row_payment">
            <div class="col_primary">

            <div class="payad-details">
                <h3>Tu anuncio ha sido enviado.</h3>
                <p>Tu anuncio será revisado, una vez sea aprobado <br> podrás pagar para activar tu anuncio.</p>
            </div>
                
            </div>
            <div class="col_secondary">
                <div class="payment-details">
                    <?php if($discount == 0): ?>
                        <h3>
                            Total:
                        </h3>
                        <p class="total"><span ><?=$precio?></span>€</p>
                        <p class="services"><?=$details?></p>
                        <p class="info">
                            Este servicio no está habilitado para
                            empresas/autónomos de Ceuta y Melilla, ni tampoco fuera de España.
                        </p>
                        <?else: ?>
                            <h3>
                                Total: <b><?=$precio?>€</b>
                            </h3>
                        <p class="discount">Total con descuento de <b><?=$discount * 100?>%</b></p>
                        <p class="total"><span ><?=$total?></span>€</p>
                        <p class="services"><?=$details?></p>
                        <p class="info">
                            Este servicio no está habilitado para
                            empresas/autónomos Ceuta y Melilla, ni tampoco fuera de España.
                        </p>
                    <?php endif ?>
                   
                </div>
            </div>
        </div>
    </div>