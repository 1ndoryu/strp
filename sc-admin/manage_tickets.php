<?php
    if(isset($_GET['type']))
        $type = $_GET['type'];
    else
        $type = "rec";

    if(isset($_POST['id-factura']))
    {
        $id_factura = $_POST['id-factura'];
        $id_ticket = $_POST['id-ticket'];
        $time = time();
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $name = $time .".". $ext;
        $filename = ABSPATH . "src/facturas/" . $name;
        $d = array('src' => $name, 'date' => "NOW", 'ID_factura' => $id_factura, 'ID_ticket' => $id_ticket);
        insertSQL('sc_files', $d);
        updateSQL('sc_tickets', $d = array('ID_factura' => "0"), $w = array('ID_ticket' => $id_ticket));
        move_uploaded_file($_FILES['file']['tmp_name'], $filename);
    }
?>

<h2>Pagos recibidos</h2>

<?php if (isset($success_div) && $success_div != ""): ?>
    <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$success_div;?></div>
<?php endif; ?>
<div class="filter_list_ad">
    <a href="index.php?id=manage_tickets&type=rec" <?=(!isset($_GET['type']) || $_GET['type']=="rec") ? "class='sel'" : ""?>>Recibidos</a>
    <a href="index.php?id=manage_tickets&type=pen" <?=isset($_GET['type']) && $_GET['type']=="pen" ? "class='sel'" : ""?>>Pendientes</a>
    <a href="index.php?id=manage_tickets&type=fin" <?=isset($_GET['type']) && $_GET['type']=="fin" ? "class='sel'" : ""?>>Finalizados</a>
    <a href="index.php?id=manage_tickets&type=fac" <?=isset($_GET['type']) && $_GET['type']=="fac" ? "class='sel'" : ""?>>Facturas</a>

</div>

<? 
    switch ($type) {
        case "rec":
            include('./block/tickets_items.php');
            break;
        case "pen":
        case "fin":
            include('./block/pending_items.php');
            break;
        case "fac":
            include('./block/facturas.php');
            break;
    }

?>


<script defer src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
<script defer src="res/tickets.js"></script>