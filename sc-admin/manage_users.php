<?
$exito_div=false;
$error_div=false;
loadModule("users");


list($exito_div, $error_div) = Users::catch();

if(isset($_GET['c'])){
	if($_GET['c'] == 1)
	$exito_div = "Usuario Editado";
}

if(isset($_GET['sp']))
{
	$user = User::getUserById($_GET['sp']);
	if(count($user))
	{
		mailRecPass($user['name'] ,$user['mail'], $user['pass']);
		$exito_div = "Contraseña enviada";
	}
}


if(isset($_POST['user_action'])){
	switch ($_POST['user_action']) {
		case '1':
			$creditos = $_POST['credits'];
			$users = json_decode($_POST['users'], true);
			foreach ($users as $key => $user) {
				User::addCredits($user, $creditos);
			}
			$exito_div = "Créditos actualizados exitosamente";
			break;
		case '2':
			$users = json_decode($_POST['users'], true);
			foreach ($users as $key => $user) {
				deleteUser($user,true);
			}
			$exito_div = "Usuarios eliminados exitosamente";
			break;
		
		default:
			# code...
			break;
	}
}

if(isset($_GET['delete'])) {
	deleteUser($_GET['delete'],true);
	$exito_div=$language_admin['manage_user.user_deleted'];
}
if(isset($_GET['disabled'])){
	$usu = updateSQL("sc_user",$d=array('active'=>0),$w=array('ID_user'=>$_GET['disabled']));
	$ad = updateSQL("sc_ad",$d=array('active'=>0),$w=array('ID_user'=>$_GET['disabled']));
	$exito_div = "Acceso Impedido";
}
if(isset($_GET['enabled'])){
	$usu = updateSQL("sc_user",$d=array('active'=>1),$w=array('ID_user'=>$_GET['enabled']));
	$ad = updateSQL("sc_ad",$d=array('active'=>1),$w=array('ID_user'=>$_GET['enabled']));
	$exito_div = "Acceso Permitido";
}

$w=array();
if(isset($_GET['q'])){
	if($_GET['field']=="ID_user")
	$w[$_GET['field']]=trim($_GET['q']);
	else
	$w[$_GET['field']]=trim($_GET['q'])."%";
}

if(isset($_GET['type']) && $_GET['type']!="all")
{
	if($_GET['type'] !== "blocked")
		$w['rol']=$_GET['type'];
	else
		$w['active'] = 0;
}

$MAX_PAG = 20; 
$pag = $_GET["pag"]; 
if (!$pag){ 
	$start = 0; 
	$pag=1; 
}else $start = ($pag - 1) * $MAX_PAG;

$usuarios=selectSQL("sc_user",$w,"ID_user DESC LIMIT ".$start.",".$MAX_PAG."");
$total_reg=countSQL("sc_user",$w,"ID_user DESC");
$tot_pag = ceil($total_reg / $MAX_PAG);
if($total_reg < $MAX_PAG){ 
	if($total_reg == 0) $inn = 0; else $inn=1;
	$por_pagina = $total_reg;
}else{ 
	$inn = 1+($MAX_PAG*($pag-1));
	if($pag == $tot_pag)
		$por_pagina=$total_reg;
	else
		$por_pagina = $MAX_PAG*($pag);
}

?>
<h2><?=$language_admin['manage_user.title_h1']?></h2>
<form action="index.php" method="get" class="addCat">
<label><?=$language_admin['manage_user.search_user']?></label>
<input name="q" type="text" value="<?=$_GET['q'];?>">
<select name="field">
	<option value="mail" <? if($_GET['field']=="mail") echo "selected";?>><?=$language_admin['manage_user.mail']?></option>
	<option value="ID_user" <? if($_GET['field']=="ID_user") echo "selected";?>><?=$language_admin['manage_user.id_user']?></option>
	<option value="phone" <? if($_GET['field']=="phone") echo "selected";?>>Telefono</option>
</select>
<input name="Buscar" type="submit" value="<?=$language_admin['manage_user.button_search']?>" class="button_form">
<button type="button" class="btn-main" onclick="newUser()">Crear Cuenta</button>
<div align="center"><a href="inc/export_user.php" target="_blank"><?=$language_admin['manage_user.export_user']?></a></div>
<input type="hidden" name="id" value="manage_users">
</form>
<div class="filter_list_ad">
	<a href="index.php?id=manage_users&type=all" <?=(!isset($_GET['type']) || $_GET['type']=="all") ? "class='sel'" : ""?>>Todos</a>
	<a href="index.php?id=manage_users&type=<?=UserRole::Particular?>" <?=isset($_GET['type']) && $_GET['type']== UserRole::Particular ? "class='sel'" : ""?>><?=UserRole::NAME(UserRole::Particular)?></a>
	<a href="index.php?id=manage_users&type=<?=UserRole::Centro?>" <?=isset($_GET['type']) && $_GET['type']== UserRole::Centro ? "class='sel'" : ""?>><?=UserRole::NAME(UserRole::Centro)?></a>
	<a href="index.php?id=manage_users&type=<?=UserRole::Publicista?>" <?=isset($_GET['type']) && $_GET['type']== UserRole::Publicista ? "class='sel'" : ""?>><?=UserRole::NAME(UserRole::Publicista)?></a>
	<!-- <a href="index.php?id=manage_users&type=<?=UserRole::Profesional?>" <?=isset($_GET['type']) && $_GET['type']== UserRole::Profesional ? "class='sel'" : ""?>><?=UserRole::NAME(UserRole::Profesional)?></a> -->
	<!-- <a href="index.php?id=manage_users&type=<?=UserRole::Visitante?>" <?=isset($_GET['type']) && $_GET['type']== UserRole::Visitante ? "class='sel'" : ""?>><?=UserRole::NAME(UserRole::Visitante)?></a> -->
	<a href="index.php?id=manage_users&type=blocked" <?=isset($_GET['type']) && $_GET['type']== "blocked" ? "class='sel'" : ""?>>Bloqueados</a>
</div>

<div><?=$language_admin['manage_user.total_user']?>: <b><?=countSQL("sc_user");?></b></div>

<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<? if($error_div!==FALSE) {?>
<div class="info_invalid"><i class="fa fa-times-circle" aria-hidden="true"></i><?=$error_div;?></div>
<? } ?>

<table class="table w-100 table-responsive-md table-users">
	<thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Telefono</th>
			<th scope="col">Email</th>
			<th scope="col">IP del usuario</th>
			<th scope="col">Fecha de Registro</th>
			<th scope="col">Última compra</th>
			<th scope="col">Créditos</th>
			<th>Opciones</th>
		</tr>
	</thead>
	<tbody>
		<? 
		for($i=0;$i<count($usuarios);$i++){?>
			<tr>
				<td>
					<input class="user_check" type="checkbox" name="user_id[]" value="<?=$usuarios[$i]['ID_user'];?>">
					<?=$usuarios[$i]['ID_user'];?></td>
				<td><?=$usuarios[$i]['phone'];?></td>
				<td>
					<div class="info-user">
						<p><?=strtolower($usuarios[$i]['mail']);?></p>
						<span><?=UserRole::NAME($usuarios[$i]['rol']);?></span>
					</div>		
				</td>
				<td class="user-ip"><?=$usuarios[$i]['IP_user'];?></td>
				<td>
					<span class="date">
					<?=date('d-m-Y',$usuarios[$i]['date_reg']);?>
					</span>
				</td>
				<td><?= timeSince($usuarios[$i]['date_reg'], false);?></td>
				<td><?= ($usuarios[$i]['credits'] == "") ? "0" : $usuarios[$i]['credits'];?></td>
				<td>
					<div class="user-options">
						<a  onclick="setCreditModal('<?=$usuarios[$i]['ID_user'];?>')" style="cursor: pointer">Agregar créditos</a>				
						<a  onclick="setUserModal('<?=$usuarios[$i]['ID_user'];?>', '<?=$usuarios[$i]['credits'];?>', '<?=$usuarios[$i]['name'];?>', '<?=$usuarios[$i]['mail'];?>', '<?=$usuarios[$i]['pass'];?>','<?=$usuarios[$i]['rol'];?>', '<?=$usuarios[$i]['anun_limit'];?>')" style="cursor: pointer">Editar <i class="fa fa-pencil-alt"></i></a>
						<a  href="<?=getPagParam('sp',$usuarios[$i]['ID_user']);?>" style="cursor: pointer"><i class="fa fa-lock"></i></a>
						<a target="_blank"  href="/mis-anuncios/?autologin=<?=$usuarios[$i]['ID_user']?>" style="cursor: pointer"><i class="fa fa-user"></i></a>
						<?php if(coutUserAds($usuarios[$i]['ID_user']) > 0): ?>
								<a href="index.php?id=manage_items&user=<?=$usuarios[$i]['ID_user']?>"><?=$language_admin['manage_user.view_ads']?></a>
							<?php else: ?>
								<a href="javascript:void(0);" class="danger">Sin anuncios</a>
						<?php endif ?>
						<a href="<?=getPagParam('delete',$usuarios[$i]['ID_user']);?>">Eliminar</a>
						<a href="javascript:void(0);" class="<?=$usuarios[$i]['active']==0?'danger':''?>" onclick="setBanModal('<?=$usuarios[$i]['ID_user'];?>', '<?=$usuarios[$i]['bloqueo'];?>', '<?=$usuarios[$i]['bloqueo_date'];?>')">Bloqueo</a>
					</div>
				</td>
			</tr>
		<? } ?>
</table>


<div class="addCat">
  
    <label><?=$language_admin['manage_item.multiple_choices']?></label>
	<form action="" method="post" id="user_action_form">
    <select id="user_action" name="user_action">
		<option>Selecciona</option>
		<option value="1">Recargar Creditos</option>
		<option value="2">Eliminar Usuarios</option>
    </select>
	<input type="hidden" name="users" id="user_data">
    <input type="button" name="actions_group_submit" id="actions_users" value="<?=$language_admin['manage_item.button_aply']?>" class="button_form">
	</form>
</div>

<div class="modal" id="modal-credit" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fa fa-credit-card"></i> Agregar créditos</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="credit-form" method="post">
					<div class="form-group">
						<label for="adminedit-nombre">Cantidad</label>
						<input type="number" name="credit" class=" form-control">
					</div>

					<input type="hidden" name="add-credit-id" id="add-credit-id">
				</form>
			</div>
			<div class="modal-footer">
				<button type="submit" onclick="$('#credit-form').submit();" id="credit-btn" class="btn btn-primary">Agregar</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="modal-ban" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fa fa-ban"></i> <span id="ban_title">Bloqueo</span> de Usuario</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="ban-form" method="post">
					<div class="form-group">
						<label for="adminedit-nombre">Motivo</label>
						<select name="bloqueo" id="ban-motivo" class="form-control">
							<option value="<?=Bloqueos::Incumplimiento?>">No cumple con las condiciones de uso</option>
							<option value="<?=Bloqueos::Denuncias?>">Denuncias</option>
							<option value="<?=Bloqueos::Spam?>">Spam</option>
							<option value="<?=Bloqueos::Actividad_ilegal?>">Actividad ilegal</option>
							<option value="<?=Bloqueos::Suplantacion?>">Suplantación de identidad</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
						</select>
					</div>

					<div class="form-group" id="ban-date-div">
						<label for="adminedit-nombre">Fecha de bloqueo</label>
						<input readonly type="date" name="" id="ban-date" class=" form-control">
					</div>
					<input type="hidden" name="BAN_ACTION" id="ban-action" >
					<input type="hidden" name="BAN_ID" id="ban-id" >
				</form>
			</div>
			<div class="modal-footer">
				<button type="submit" onclick="$('#ban-form').submit();" id="ban-btn" class="btn btn-primary">Bloquear</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="modal-newuser" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-pencil-alt"></i> Nuevo Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form  method="post">
			  <div class="form-group">
					<label for="adminedit-nombre">Nombre</label>
						<input type="text" name="nombre"  class=" form-control">
			  </div>
			  <div class="form-group">
					<label for="adminedit-mail">Correo</label>
					<input type="email" required name="mail"  class=" form-control">

			  </div>
			
			  <div class="form-group">
					<label for="adminedit-nombre">Contraseña</label>
					<input type="text" required name="pass"  class=" form-control">
					
			  </div>

			  <div class="form-group">
					<label for="adminedit-nombre">Rol</label>
					<select name="rol" class=" form-control">
						<option value="<?=UserRole::Particular?>">Particular</option>
						<option value="<?=UserRole::Centro?>">Centro</option>
						<option value="<?=UserRole::Publicista?>">Publicista</option>

					</select>
					
			  </div>
			  <input type="hidden" name="phone" value="">
			  <input type="hidden" name="newuser" value="1" >
			  
		  
        </div>
        <div class="modal-footer">
          <button type="submit"  class="btn btn-primary">Crear</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
		</div>
		</form>
      </div>
    </div>
</div>
<div class="modal" id="modal-edituser" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-pencil-alt"></i> Editar Usuario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="edituser-form" method="post">
			  <div class="form-group">
					<label for="adminedit-nombre">Nombre</label>
						<input type="text" name="nombre" id="adminedit-nombre" class=" form-control">
			  </div>
			  <div class="form-group">
					<label for="adminedit-mail">Correo</label>
						<input type="email" required name="mail" id="adminedit-mail" class=" form-control">
					<div class="invalid-feedback">Escriba El Correo</div>
			  </div>
			  <div class="form-group">
					<label for="adminedit-nombre">Contraseña</label>
					<input type="text" required name="pass" id="adminedit-pass" class=" form-control">
					<div class="invalid-feedback">Escriba Una Contraseña</div>
			  </div>
			  <div class="form-group">
					<label for="adminedit-nombre">Creditos</label>
					<input type="number" required name="credits" id="adminedit-credits" class=" form-control">
					<div class="invalid-feedback">Creditos invalidos</div>
			  </div>
			  <div class="form-group">
					<label for="adminedit-nombre">Rol</label>
					<select name="rol" id="adminedit-rol" class=" form-control">
						<option value="<?=UserRole::Particular?>">Particular</option>
						<option value="<?=UserRole::Centro?>">Centro</option>
						<option value="<?=UserRole::Publicista?>">Publicista</option>

					</select>
					<div class="invalid-feedback">Rol invalido</div>
			  </div>
			  <div class="form-group">
					<label for="adminedit-nombre">limite</label>
					<input type="text" required name="limit" id="adminedit-limit" class=" form-control">
					<div class="invalid-feedback">El limite debe ser distinto de 0</div>
			  </div>

			  <input type="hidden" name="ID" id="adminedit-id" >
		  
        </div>
        <div class="modal-footer">
          <button type="submit" id="edituser-btn" class="btn btn-primary">Editar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
		</div>
		</form>
      </div>
    </div>
</div>

<? 
	createPagButtons($tot_pag, $pag, "/sc-admin/index.php?id=manage_users");
?>

<script src="res/user.js"></script>