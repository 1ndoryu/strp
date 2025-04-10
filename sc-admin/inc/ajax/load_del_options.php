<?php include("../../../settings.inc.php"); ?>
<?php if(isset($_GET['r']) && $_GET['r'] == 0): ?>
    <option value="0">Selecciona</option>
    <option value="1" >1</option>
    <option value="2" >2</option>
    <option value="3" >3</option>
    <option value="4" >4</option>
    <option value="5" >5</option>
    <option value="6" >6</option>
    <option value="<?=Motivo::SIN_AVISO?>" >Sin aviso</option>
    <option value="<?= Motivo::Denunciado?>" >Denunciado</option>
<?php endif ?>
<?php if(isset($_GET['r']) && $_GET['r'] == -1): ?>
    <option value="0">Selecciona</option>
    <option value="<?= Motivo::INCUMPLIMIENTO ?>" >No cumple con las condiciones de uso</option>
    <option value="<?= Motivo::Repetido ?>" >Repetido</option>
    <option value="<?= Motivo::SIN_AVISO ?>" >Sin aviso</option>

<?php endif ?>
<?php if(isset($_GET['r']) && $_GET['r'] == 1): ?>
    <option value="<?= Motivo::Cancelado?>" >Cancelar</option>
<?php endif ?>
<?php if(isset($_GET['r']) && $_GET['r'] == 2): ?>
    <option value="descartar">Descartar cambios</option>
    <option value="<?= Motivo::Desactivado ?>" >Desactivar</option>
   
<?php endif ?>