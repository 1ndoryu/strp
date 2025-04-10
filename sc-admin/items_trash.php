<?php
    loadModule('trash');
    $exito_div = Trash::catch();


    //eliminar
    if(isset($_GET['del'])){
        deleteAdRoot($_GET['del'], true);
        $exito_div = "Anuncio Eliminado";
    }

    //restaurar
    if(isset($_GET['restart'])){
        $id = $_GET['restart'];
        $datos = array();
        $datos['trash'] = 0;
        $datos['date_trash'] = 0;
        $datos['motivo'] = 0;
        $datos['trash_comment'] = '';
        updateSQL('sc_ad', $datos, $w = array('ID_ad' => $id));
        $exito_div = "Anuncio Restaurado";
    }



    //busqueda

    $filter=array('trash' => 1);
    $cusWhere = Trash::filter();
    $search = array();

    if(isset($_GET['type']) && $_GET['type']=="dm"){
        $filter['motivo'] = Motivo::Usuario . "<";
    }
    if(isset($_GET['type']) && $_GET['type']=="du"){
        $filter['motivo'] = Motivo::Usuario;
    }

    if(isset($_GET['q']) && isset($_GET['field']) && $_GET['q']!=''){
        switch ($_GET['field']) {
            case 'ID_ad':
                $filter['ref'] = $_GET['q'];
                break;
            case 'mail':
                $w = array();
                $w[$_GET['field']]=trim($_GET['q'])."%";
                $usuarios=selectSQL("sc_user",$w);
                foreach ($usuarios as $key => $value) {
                    $search['ID_user'] = strval($value['ID_user']);
                }
                break;
            case 'title':
                $search = array('title' => trim($_GET['q']), 'texto' => trim($_GET['q']));
                break;
            default:
                # code...
                break;
        }
    }
    //paginacion 
    $TAMANO_PAGINA = getConfParam('ITEM_PER_PAGE'); 
    $pagina = $_GET["pag"]; 
    $num_total_registros = countSQL("sc_ad",$filter,"");
    $total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
    if (!$pagina){ 
        $inicio = 0; 
        $pagina=1; 
    } else { 
        $inicio = ($pagina - 1) * $TAMANO_PAGINA; 
    }

    $orden_comun = " date_trash DESC limit $inicio, $TAMANO_PAGINA";

    $result = selectSQL("sc_ad",$filter,$orden_comun, $search, $cusWhere);

    foreach ($result as $key => $value) 
    {
        $t = time();
       
        $t = $t - $value['date_trash'];;

        if($t >= 86400*30 ){
            deleteAdRoot($value['ID_ad'], true);
            unset($result[$key]);

        }
        
    }

    $legend = (!isset($_GET['type']) || ($_GET['type']=="all" || $_GET['type'] == "del"));


?>

<h2>Anuncios Eliminados</h2>
<? if($exito_div!==FALSE){?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? }?>

<form action="index.php" method="get" class="addCat">
    <label>Buscar Anuncio</label>
    <input name="q" type="text" value="<?php if(isset($_GET['q'])) print $_GET['q'];?>">
    <?php if( isset($_GET['field'] ) ): ?>
        <select name="field">
            <option value="ID_ad" <? if($_GET['field']=="ID_ad") echo "selected";?> >REF Anuncio</option>
            <option value="mail" <? if($_GET['field']=="mail") echo "selected";?> ><?=$language_admin['manage_user.mail']?></option>
            <option value="title"<? if($_GET['field']=="title") echo "selected";?> >Por Palabras</option>
        </select>
    <?php else : ?>
        <select name="field">
            <option value="ID_ad" >REF Anuncio</option>
            <option value="mail" ><?=$language_admin['manage_user.mail']?></option>
            <option value="title" selected >Por Palabras</option>
        </select>
    <?php endif ?>
    <input name="Buscar" type="submit" value="Buscar Anuncio" class="button_form">
    <input type="hidden" name="id" value="items_trash">
    <input type="hidden" name="type" value="<?=$_GET['type']?>">
</form>

<div class="filter_list_ad">
    <a href="index.php?id=items_trash&type=all" <?=(!isset($_GET['type']) || $_GET['type']=="all") ? "class='sel'" : ""?>>Todos</a>
    <a href="index.php?id=items_trash&type=del" <?=isset($_GET['type']) && $_GET['type']=="del" ? "class='sel'" : ""?>>Eliminados</a>
    <a href="index.php?id=items_trash&type=des" <?=isset($_GET['type']) && $_GET['type']=="des" ? "class='sel'" : ""?>>Desactivados</a>
    <a href="index.php?id=items_trash&type=den" <?=isset($_GET['type']) && $_GET['type']=="den" ? "class='sel'" : ""?>>Denunciados</a>
    <a href="index.php?id=items_trash&type=dm" <?=isset($_GET['type']) && $_GET['type']=="dm" ? "class='sel'" : ""?>>Por mi</a>
    <a href="index.php?id=items_trash&type=du" <?=isset($_GET['type']) && $_GET['type']=="du" ? "class='sel'" : ""?>>Por usuario</a>
</div>

<?php if($legend): ?>
    <div class="trash-legend">
        <span>
            Por mi
            <i class="admin"></i>
        </span>
        <span>
            Por el usuario
            <i class="user"></i>
        </span>
        <?php if(!isset($_GET['type']) || $_GET['type']=="all"): ?>
          
            <span>
                No cumple con las condiciones
                <i class="cancel"></i>
            </span>
            
        <?php endif ?>
    </div>
<?php endif; ?>
<div class="table-responsive table-trash">
<form method="POST" id="trash_form">
  <table class="table table-sm">
    <thead>
      <tr>
        <th><input type="checkbox" class="items_check"></th>
        <th>Titulo</th>
        <th>Categoria</th>
        <th>Fecha</th>
        <th>Motivo</th>
        <th class="text-center">Acciones</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
        <?php foreach( $result as $value ): ?>
            <?php 
                $image=false;
                $ad = getDataAd($value['ID_ad']);
                if(count($ad['images'])!=0) 
                    $image=true;

                $date = date('d.m.Y', $value['date_trash']);


                $flag = "admin";
                if($ad['ad']['motivo'] >= Motivo::Usuario)
                    $flag = "user";
                if($ad['ad']['motivo'] >= Motivo::Cancelado)
                    $flag = "cancelado";
             ?>
           <tr>
               <td>
                    <input type="checkbox" class="item_check" name="anuncio[]" value="<?=$value['ID_ad']?>">
               </td>
               <td>
                <div class="ad_trash">
                    <a target="_blank" href="<?=urlAd($ad['ad']['ID_ad'])?>?edited">
                        <img src="<?=Images::getImage($ad['images'][0]['name_image'], IMG_ADS, true)?>" alt="">
                    </a>
                    <div class="ad_trash_content">
                        <h3><?=$ad['ad']['title']?></h3>
                        <p><?=$ad['user']['mail']?></p>
                        <span><b>Ref:</b> <?=$value['ref'] ?></span>
                    </div>
                </div>
                   
               </td>
               <td>
                   <?=$ad['parent_cat']['name'] ?>
               </td>
 
               <td><?=$date?></td>
               <td><?=Trash::parseMotivo($ad['ad']['motivo'])?></td>
               <td>
                <div class="trash_module">
                    <button type="button" class="trash_button">Acciones</button>
                    <div class="trash_options">
                        <a href="index.php?id=edit_item&a=<?=$ad['ad']['ID_ad']?>" class="trash_option">Editar</a>
                        <a href="index.php?id=items_trash&restart=<?=$value['ID_ad']?>" class="trash_option">Restaurar</a>
                        <a href="index.php?id=items_trash&del=<?=$value['ID_ad']?>" class="trash_option" id="trash_delete_<?=$value['ID_ad']?>" onclick="deleteAd('<?=$value['ID_ad']?>', true);">Eliminar</a>
                        <a href="javascript:void(0);" onclick="openCommentModal('<?=$value['ID_ad']?>', '<?=$ad['ad']['trash_comment']?>');" class="trash_option">Comentario</a>
                    </div>
                </div>
               </td>
               <td>
                    <?php if($legend):?>
                        <span class="flag <?=$flag?>"></span>
                    <?php endif; ?>
               </td>

           </tr>
        <?php endforeach ?>
    </tbody>
  </table>
</div>
    <div class="addCat">
    
        <label><?=$language_admin['manage_item.multiple_choices']?></label>
        <select id="user_action" name="action">
            <option value="1">Eliminar Permanente</option>
  
        </select>
        <input type="submit" name="actions_group_submit" value="<?=$language_admin['manage_item.button_aply']?>" class="button_form">

    </div>
</form>
<?php
    createPagButtons($total_paginas, $pagina, "/sc-admin/index.php?id=items_trash");
?>


<div class="modal" id="trash-comment-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Comentario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> 
          </button>
        </div>
        <form id="trash-comment-form" method="get">
            <div class="modal-body">
                <textarea name="comment" id="trash-comment" class="form-control m-0" rows="3"></textarea>
                <input type="hidden" name="save-comment" id="trash-comment-id" >
                <input type="hidden" name="id" value="items_trash">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success"><i class="fa fa-check pr-2"></i>Guardar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
		</form>
      </div>
    </div>
</div>

<div class="modal" id="trash-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></i> Anuncio</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="trash-form" method="get">
        <div class="modal-body">
            <div role="image">

            </div>

            <hr class="divider my-3" >
            <h5 class="py-1 my-0"></h5>
            <span class="text-date"></span>
            <p></p>
            <h6 class="py-1">Usuario: <span class="float-right"></span></h6>
              
            <input type="hidden" name="id" value="items_trash">
            <input type="hidden" name="" id="trash-id" >
		  
        </div>
        <div class="modal-footer">
          <button type="submit" id="trash-btn-delete" class="btn btn-left btn-danger"><i class="fa fa-trash-alt pr-2"></i>Eliminar</button>
          <button type="button" id="trash-btn-restart" class="btn btn-success">Restaurar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
		</div>
		</form>
      </div>
    </div>
</div>

<script src="res/trash.js"></script>