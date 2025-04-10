<?php
    global $DATAJSON;
    if(isset($DATAJSON))
    {
        $DATAJSON = json_encode($DATAJSON, true);
    }
?>

<script>
    var DATAJSON = <?php echo $DATAJSON; ?>;
</script>