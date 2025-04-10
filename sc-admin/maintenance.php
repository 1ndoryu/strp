<?php 
    $mode = getConfParam('MAINTENANCE_MODE');
    if(isset($_GET['new_mode']))
    {

       $mode = $_GET['new_mode'];
       setConfParam('MAINTENANCE_MODE', $mode);
       
    }

?>

<h2>Mantenimiento</h2>
<?php if( $mode == 1 ): ?>
   <p>la pagina esta en modo mantenimiento</p>
<?php endif ?>

<form action="" method="get">
    <input type="hidden" name="id" value="maintenance">
    <input type="hidden" name="new_mode" value="<?= $mode == 0 ? '1' :  '0' ?>">
    <input type="submit" value="<?= $mode   == 0 ? 'Activar Mantenimiento' : 'Desactivar Mantenimiento' ?>">
</form>