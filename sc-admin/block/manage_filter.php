<?php

    $filters = ModFilter::getFilters();
?>
<div class="panel-header mt-md-0 mt-5">
    <h3>Filtros de categorías</h3>
    <button class="btn btn-panel" onclick="addFilter()" >Añadir filtro</button>
</div>

<table class="table table-panel table-responsive-md">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>categorias</th>
            <th>Palabras clave</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($filters as $filter): ?>
            <tr>
                <td><?php echo $filter['name']; ?></td>
                <td>
                    <?php foreach($filter['cats'] as $cat): ?>
                        <span><?php echo $cat['name']; ?></span>
                    <?php endforeach; ?>
                </td>
                <td>
                    <?php foreach($filter['words'] as $word): ?>
                        <span><?php echo $word; ?></span>
                    <?php endforeach; ?>
                </td>
                <td>
                    <a href="javascript:void(0)" data-name=<?=$filter['name']?> data-cats="<?=$filter[2]?>" data-word="<?=$filter[1]?>" data-id="<?php echo $filter['ID_filter']; ?>" onclick="editFilter(this)" class="btn btn-panel">Editar</a>
                    <a href="<?=getConfParam("SITE_URL")?>sc-admin/index.php?id=manage_categories&deletefilter=<?php echo $filter['ID_filter']; ?>" class="btn btn-panel">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
       
    </tbody>
  
</table>

<div class="modal" id="filter_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Filtro de palabras</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="delad-form" method="post">
        <div class="modal-body">
			  <div class="form-group row">
                    <label class=" col-form-label col-12" for="#delad-motivo">Motivo</label>
                    <div class="col-12">
                        <?php loadBlock('search-cats', array('id'=>'filter_category')); ?>

                    </div>
			  </div>
			  <div class="form-group row">
                    <label for="adminedit-mail" class=" col-form-label col-12">Nombre</label>
                    <div class="col-12">
						<input type="text" required name="name" value="" id="filter_name" class=" form-control">
                    </div>
			  </div>
			  <div class="form-group row">
                    <label  class=" col-form-label col-12">Palabras</label>
                    <div class="col-12">
						<textarea name="words" required id="filter_words" class="form-control mx-0"></textarea>
                        <span class="info">Separar palabras con comas y sin espacios</span>
                    </div>
			  </div>

              
			  <input type="hidden" name="filter-id" id="filter_id" value="0" >
		  
        </div>
        <div class="modal-footer">
          <button type="submit" id="delad-btn" class="btn btn-danger">Aceptar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		</div>
		</form>
      </div>
    </div>
</div>
