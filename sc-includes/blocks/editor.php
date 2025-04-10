<div class="modal" id="editor_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-body">
            <div class="editor-box">

                <img id="editor_img" src="" alt="Imagen">
            </div>
		  
        </div>
        <div class="modal-footer">
          <button type="botton" onclick="saveImage()" id="edit_btn" class="btn btn-danger">Aplicar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		</div>
	
      </div>
    </div>
</div>

<script defer src="<?=getConfParam("SITE_URL")?><?=JS_PATH?>cropper.min.js?v=0.1"></script>
<script defer src="<?=getConfParam("SITE_URL")?><?=JS_PATH?>image-editor.js?v=0.1"></script>