<?php

include("../../../settings.inc.php");

$files = selectSQL("sc_files", $w = array('ID_factura' => $_GET['id']));

header('Content-Type: application/html');
?>
<ul>
    <?php foreach($files as $files): ?>
        <li>
            <a target="_blank" href="<?= getConfParam('SITE_URL') ?>print_factura.php?file=<?= $files['src'] ?>">
                <?=$files['date'] ?> -  Nº: <?=$files['ID_ticket'] ?>
            </a>
        </li>
    <?php endforeach ?>
</ul>

