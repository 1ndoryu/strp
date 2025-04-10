<?php if(!isset($_COOKIE['post_warning']) ): ?>
    <div class="post-warning-container">
        <div class="post-warning">
            
            <p>Antes de publicar tu anuncio, revisa nuestra <a href="/terminos-y-condiciones-de-uso" target="_blank">normas de publicación</a>.</p>
            <button onclick="closePostWarning()" class="post-warning-button">
                ENTENDIDO            
            </button>
        </div>
    </div>
<?php endif; ?>

<script>
    function closePostWarning()
    {
        setCookie('post_warning', 0, 365);
        $('.post-warning-container').hide();
    }
</script>