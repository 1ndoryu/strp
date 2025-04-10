<script type="application/ld+json">
{
 "@context": "http://schema.org",
 "@type": "BreadcrumbList",
 "itemListElement":
 [
    <?php foreach($data as $key => $d){ ?>
        {
        "@type": "ListItem",
        "position": <?=$d['position']?>,
        "item":
            {
                "@id": "<?=$d['id']?>",
                "name": "<?=$d['name']?>"
            }
        }<?php if($key != count($data) - 1) echo ","; ?>
    <?php } ?>
 ]
}
</script>
