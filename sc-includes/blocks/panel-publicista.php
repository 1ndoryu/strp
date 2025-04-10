<?php

if($_SESSION['data']['rol'] == UserRole::Publicista)
{
?>

<div class="panel-publicista">
    <button onclick="openMasivePayment()">Comprar Servicios</button>
</div>

<?php 
}?>