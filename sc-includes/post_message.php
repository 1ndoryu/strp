<?php
    if(isset($_GET['payad']))
    {
        $order_id = $_GET['payad'];
        include("main.php");
        
        loadBlock('payad_details');
    }else{
?>
<div class="info_post">

    <span class="title_one">¡Tu anuncio ya ha sido enviado!
    </span>

    <span class="title_two">
    Revisaremos tu anuncio en un plazo de 24 horas. No trabajamos fines de
semana ni festivos.
    </span>

    <?php if(isset($_SESSION['data']['ID_user'])): ?>
        <a href="mis-anuncios/" class="post_again">Mis Anuncios</a>
      <? else: ?>
        <a href="index.php?login" class="post_again">Mis Anuncios</a>
    <?php endif ?>
    

</div>
<?php
    }   
?>