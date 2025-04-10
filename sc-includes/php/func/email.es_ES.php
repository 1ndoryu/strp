<?php
require('phpmailer/class.phpmailer.php');
require('phpmailer/class.smtp.php');
$html_start='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
		<title>{subject}</title>
		<style></style>
    </head>
<body style="-webkit-text-size-adjust:none;font-family:Open-sans, sans-serif;color:#555454;font-size:13px;margin:auto;background: #F0F0F0;">
	<div class="containerMain" style="width: 100%;height: 100%;display: block;padding: 50px 0;">
    <table width="602" align="center" cellpadding="0" cellspacing="0">
	<tr>
	<td align="center">
    <div class="contentBody" style="width: 600px !important;height: auto; margin:auto !important;text-align:left">
            <div class="top_head" style="width: 100%;height: 60px;margin: auto;display: block;">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td width="100%" height="60" align="left" valign="bottom" bgcolor="#FFFFFF">
                    <a href="'.getConfParam('SITE_URL').'" target="_blank">
						<img src="'.getConfParam('SITE_URL').'src/images/logo_mail.png" border="0" height="50" style="margin:0 auto;display: block;">
					</a>
                  </td>
              </tr>
            </table>
          </div>
            <div class="cont" style="border-radius:10px; -webkit-border-radius:10px; -moz-border-radius:10px; -o-border-radius:10px;margin-top: auto;margin-right: auto;margin-bottom: auto;display: block;font-size: 14px;color: #555;word-break: break-word;">
            <table width="598" cellpadding="2" cellspacing="2" style="display:block; background:#FFFFFF; border:1px solid #DDDDDD" background="#FFFFFF">
                            <tr>
                                <td height="10" colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="20">&nbsp;</td>
                                <td width="660">';
								
$html_end='
<div style="padding: 5px; margin: 15px 2px; text-align: justify; font-size: 0.9em;">
Este mensaje va dirigido, de manera exclusiva, a su destinatario y puede
contener información confidencial y sujeta al secreto profesional, cuya
divulgación no está permitida por Ley. En caso de haber recibido este
mensaje por error, le rogamos que de forma inmediata, nos lo comunique
mediante correo electrónico remitido a nuestra atención y proceda a su
eliminación, así como a la de cualquier documento adjunto al mismo.
Asimismo, le comunicamos que la distribución, copia o utilización de este
mensaje, o de cualquier documento adjunto al mismo, cualquiera que fuera su
finalidad, están prohibidas por la ley. En aras del cumplimiento del
Reglamento (UE) 2016/679 del Parlamento Europeo y del Consejo, de 27 de
abril de 2016, puede ejercer los derechos de acceso, rectificación,
cancelación, limitación, oposición y portabilidad de manera gratuita
mediante correo electrónico a: info@solomasajistas.com
</div>

</td>
                                <td width="20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="20" colspan="3">&nbsp;</td>
                            </tr>
            </table>
            </div>
          <div class="foot" style="padding: 10px;margin: auto;display: block;font-size: 12px;color: #666;width: auto;text-align: center;">

            <font face="Open-sans, sans-serif" style="color:#999">Has recibido este email porque estás registrado en <a href="'.getConfParam('SITE_URL').'" style="color:#09c">'.getConfParam('SITE_NAME').'</a>.<br>
    Por favor, no respondas a este mensaje. Utiliza nuestro <a href="'.getConfParam('SITE_URL').'contactar/" style="color:#999">formulario de contacto</a>.<br>
    '.getConfParam('SITE_NAME').' &copy; '.date("Y",time()).'  Anuncios clasificados gratis | Todos los derechos reservados.</font>
            </div>
        </div>
	</td>
	</tr>
	</table>
</div>
</body>
</html>';
$html_end_clean='


</td>
                                <td width="20">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="20" colspan="3">&nbsp;</td>
                            </tr>
            </table>
            </div>
          <div class="foot" style="padding: 10px;margin: auto;display: block;font-size: 12px;color: #666;width: auto;text-align: center;">

            <font face="Open-sans, sans-serif" style="color:#999">Has recibido este email porque estás registrado en <a href="'.getConfParam('SITE_URL').'" style="color:#09c">'.getConfParam('SITE_NAME').'</a>.<br>
    Por favor, no respondas a este mensaje. Utiliza nuestro <a href="'.getConfParam('SITE_URL').'contactar/" style="color:#999">formulario de contacto</a>.<br>
    '.getConfParam('SITE_NAME').' &copy; '.date("Y",time()).'  Anuncios clasificados gratis | Todos los derechos reservados.</font>
            </div>
        </div>
	</td>
	</tr>
	</table>
</div>
</body>
</html>';
function mailRegister($name,$email,$confirm){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($email);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject=$name.", ¡Bienvenido a ".getConfParam('SITE_NAME')."!";
	$mail->Subject = $subject;
	$data = array(
		'nombre' => $name,
		'nombre_pagina' => getConfParam('SITE_NAME'),
		'url' => getConfParam('SITE_URL').'registro/?confirm='.$confirm
	);
	$body = loadTemplate('confirm_register', $data);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}

function orderMail($status, $order, $plan, $amount, $method, $iduser = 0, $idad = 0)
{
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress(getConfParam('NOTIFY_EMAIL'));
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject = 'Nuevo pedido';
	$mail->Subject = $subject;

	if($iduser > 0)
	{
		$user = selectSQL("sc_user", array("ID_user" => $iduser));
		$name = $user[0]['name'];
		$email = $user[0]['mail'];
	}
	
	if($idad > 0)
	{
		$ad = getDataAd($idad);
		$name = $ad['user']['name'];	
		$email = $ad['user']['mail'];
	}

	if(is_numeric($plan))
	{
		$plan = Payment::getPlan($plan);
		$service = $plan['name'];
	}else
	{
		$service = $plan;
	}


	$data = array(	
		'nombre' => $name,
		'mail' => $email,
		'servicio' => $service,
		'estado' => $status,
		'monto' => $amount,
		'metodo' => $method,
		'orden' => $order
	);
	$body = loadTemplate('payin_mail', $data);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}

function mailWelcome($name,$email,$pass){
	global $html_start, $html_end;
	Statistic::addUsuarioNuevo();
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($email);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject="Te enviamos la contraseña - " . getConfParam('SITE_NAME');
	$mail->Subject = $subject;
	$data = array(
		'nombre' => $name,
		'email' => $email,
		'pass' => $pass,
	);
	$body = loadTemplate('welcome', $data);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}
function mailRecPass($name,$email,$pass){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($email);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject="Recuperar contraseña - ". getConfParam('SITE_NAME');
	$mail->Subject = $subject;
	$body = loadTemplate('rec_pass_mail', 
		array(
			'pass' => $pass
		)
	);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}

function mailCancelAd($id)
{
	$ad = getDataAd($id);
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$email = $ad['user']['mail'];
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($email);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject='Tu anuncio no ha sido aprobado';
	$mail->Subject = $subject;
	$data = array(	
		'name' => $ad['user']['name'],
		'title' => $ad['ad']['title'],
	);
	$body = loadTemplate('cancelado_mail', $data);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}

function mailNoAproveAd($id)
{
	$ad = getDataAd($id);
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$email = $ad['user']['mail'];
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($email);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject='Tu anuncio no ha sido aprobado';
	$mail->Subject = $subject;

	$body = loadTemplate('no_aproved_mail');
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}
function mailDeleteAd($id)
{
	$ad = getDataAd($id);
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$email = $ad['user']['mail'];
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($email);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject='Tu anuncio ha sido eliminado';
	$mail->Subject = $subject;
	$data = array(	
		'name' => $ad['user']['name'],
		'title' => $ad['ad']['title']
	);
	$body = loadTemplate('eliminado_mail', $data);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}

function adminRecPass($token,$email){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($email);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$mail->Subject = "Recuperar Contraseña";
	$body ='
<h1 style="font-size:20px; margin:15px 0;">Recuperacion De Contraseña</h1>
<p style="font-family:Arial;">Hola administrado, has olvidado tu contraseña actual</p>
<p style="font-family:Arial;">
para accesder a tu cuenta y crear tu nueva contraseña, sigue este link
</p>
<table border="0" align="center" cellpadding="0" cellspacing="0" style="border-collapse: separate !important; border-radius: 3px; background-color: #00BCD4;">
                    <tbody>
                        <tr>
                            <td align="center" valign="middle" style="font-family: Arial; font-size: 16px; padding: 15px;">
                                <a title="Acceder a mi cuenta" href="'.getConfParam('SITE_URL').'sc-admin/index.php?token='.$token.'" target="_blank" style="font-weight: bold; letter-spacing: normal; line-height: 100%; text-align: center; text-decoration: none; color: rgb(255, 255, 255);">Recuperar Mi Contraseña</a>
                            </td>
                        </tr>
                    </tbody>
				</table>
';
	$full_content=str_replace("{subject}","Recuperar Contraseña",$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return true;
	}
}

function mailNewAd($id){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$ad= getDataAd($id);
	user::insertEvent($ad['user']['ID_user']);
	Statistic::addAnuncioNuevo();
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($ad['user']['mail']);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject=formatName($ad['user']['name']).", has publicado un anuncio!";
	$mail->Subject = $subject;

	$data = array(
		'name' => formatName($ad['user']['name']),
		'email' => $ad['user']['mail'],
		'pass' => $ad['user']['pass']
	);

	
	$body = loadTemplate('welcome', $data);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}
function notifyEmail($subject,$content){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('CONTACT_MAIL'), getConfParam('CONTACT_NAME'));
	$mail->addAddress(getConfParam('NOTIFY_EMAIL'));
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	

	$body = $content;
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}
function SendMailToAdmin($subject,$content,$email, $name){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom($email, $name);
	$mail->addAddress(getConfParam('CONTACT_MAIL'));
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	

	$body = $content;
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}

function SendMail($subject,$content,$email, $titulo, $id = ''){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($email);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	$id_msg = '';
	if($id != '')
		$id_msg = '<p style="font-family:Arial; font-weight: 900;">ID del Anuncio: ' . $id . '</p>';

	$body = '
	<h1 style="font-size:20px; margin:15px 0;">'.$titulo.'</h1>
	'. $id_msg . $content.'
';
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}

function CaducadoMail($to, $idad, $username)
{
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($to);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject = 'Tu anuncio está a punto de caducar';
	$mail->Subject = $subject;
	$data = array(	
		'nombre_pagina' => getConfParam('SITE_NAME'),
		'id' => $idad,
		'url' => getConfParam('SITE_URL').'mis-anuncios/',
		'tiempo' => getConfParam('ITEM_TIME_NOTICE')
	);
	$body = loadTemplate('caducado_mail', $data);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
	
}


function CompartirMail($to, $toname,$from, $fromname, $msg, $ad){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom($from, $fromname);
	$mail->addAddress($to, $toname);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$mail->Subject = $subject = '!Mira este Anuncio¡';
	
	if($ad != '')
		$body = $msg. '<p><a href="'.$ad.'" >Anuncio</a></p>';
	else
		$body = $msg;

	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}


function mailAdNotNotification($id){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$ad= getDataAd($id);
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress('nomensajes@subdominioanunciclas.solomasajistas.com');
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject=formatName("El anuncio '".$ad['ad']['title']."' no quiere recibir información relacionada");
	$mail->Subject = $subject;
	$body = '
	<h1 style="font-size:20px; margin:15px 0;">El anuncio "'.stripslashes($ad['ad']['title']).'" no quiere recibir información relacionada</h1>
	<p style="font-family:Arial;">Usuario: '.formatName($ad['user']['name']).' ha solicitado que ese anuncio no reciba ningun tipo de información</p>
	<p>
		<b>Dirección de correo:</b> <span class="dat">'.$ad['user']['mail'].'</span><br />
	</p>
	';
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}

function mailAdPremium_home($id){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$ad= getDataAd($id);
	$paypal_configuration=selectSQL("sc_paypal",$w=array('ID_paypal'=>1));
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($ad['user']['mail']);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject='¡Tu anuncio ha sido destacado!';
	$mail->Subject = $subject;
	$body = '<h1 style="font-size:20px; margin:15px 0;">¡Tu anuncio "'.stripslashes($ad['ad']['title']).'" ha sido destacado!</h1>
<p style="font-family:Arial;">Tu anuncio ha sido destacado en la portada de la web y será mucho más visible. Recibirá más visitas y tendrás más opciones de encontrar comprador para tu anuncio.</p>
<p style="font-family:Arial;">Permanecerá destacado durante <b>'.$paypal_configuration[0]['time_1'].'</b> días.</p>
';
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}
function mailAdPremium_list($id){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$ad= getDataAd($id);
	$paypal_configuration=selectSQL("sc_paypal",$w=array('ID_paypal'=>1));
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($ad['user']['mail']);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject= 'Tu anuncio ha sido subido en los listados!';
	$mail->Subject = $subject;
	$body = '<h1 style="font-size:20px; margin:15px 0;">¡Tu anuncio "'.stripslashes($ad['ad']['title']).'" ha sido destacado!</h1>
<p style="font-family:Arial;">Tu anuncio ha sido subido en los listados, estará siempre en las primeras posiciones de los listados. Recibirá más visitas y tendrás más opciones de encontrar comprador para tu anuncio.</p>
<p style="font-family:Arial;">Permanecerá en las primeras posiciones durante <b>'.$paypal_configuration[0]['time_2'].'</b> días.</p>
';
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}
function mailAdContact($name,$email,$phone,$msj,$id){
	global $html_start, $html_end;
	$ad= getDataAd($id);
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($ad['user']['mail']);
	$mail->addReplyTo($email,$name);

	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject = $name.' está interesado en tu anuncio';
	$mail->Subject = $subject;
	if($phone == '')
		$body = loadTemplate('contact_ad_mail_no_phone', 
		array(
		'name' => $name,
		'email' => $email,
		'msg' => $msj,
		'site_name' => getConfParam('SITE_NAME'))
		);
	else
		$body = loadTemplate('contact_ad_mail', 
		array(
		'name' => $name,
		'email' => $email,
		'phone' => $phone,
		'msg' => $msj,
		'site_name' => getConfParam('SITE_NAME'))
		);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}
function mailContactWeb($name,$email,$msj,$subject){
	global $html_start, $html_end;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$from = $name . ' <' .$email . '>'; 
	$mail->setFrom($email, $from);
	//$mail->addReplyTo($email, $name);
	$mail->addAddress(getConfParam('CONTACT_MAIL'));
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$mail->Subject = $subject;
	$data = array(	
		'name' => $name,
		'email' => $email,
		'msj' => $msj
	);
	$body = loadTemplate('contact_mail', $data);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end;
	$mail->msgHTML($full_content);
	//replyContactUser($name,$email);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}
function replyContactUser($name,$email){
	global $html_start, $html_end_clean;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addAddress($email);
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$subject = 'Tu mensaje ha sido recibido';
	$mail->Subject = $subject;
	$data = array(	
		'name' => $name,
		'site_name' => getConfParam('SITE_NAME')
	);
	$body = loadTemplate('contact_user_mail', $data);
	$full_content=str_replace("{subject}",$subject,$html_start).$body.$html_end_clean;
	$mail->msgHTML($full_content);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}

}
function mailPassAdmin(){
	global $header_mail, $footer_mail;
	$mail = new PHPMailer();
	if(activeSMTP()){
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'ssl'; 
		$mail->Host       = getConfParam('SMTP_HOST');
		$mail->Port       = getConfParam('SMTP_PORT');
		$mail->Username   = getConfParam('SMTP_USER');
		$mail->Password   = getConfParam('SMTP_PASSWORD');  
	}
	$mail->setFrom(getConfParam('DEFAULT_MAIL'),getConfParam('DEFAULT_NAME'));
	$mail->addReplyTo($email);
	$mail->addAddress(getConfParam('ADMIN_MAIL'));
	$mail->CharSet = 'UTF-8'; 
	$mail->IsHTML(true);
	$mail->Subject = 'Tus datos de administrador';
	$body = $header_mail.'<h1 style="font-size:20px; margin:15px 0;">Te enviamos tus datos de administrador</h1>
A continuación se indican los datos de acceso para el administrador.<br /><br />
<b>Usuario:</b><br>
'.getConfParam('ADMIN_USER').'<br />
<b>Contraseña:</b><br>
'.base64_decode(getConfParam('ADMIN_PASS')).'<br />
<br /><br />
'.$footer_mail;
	$mail->msgHTML($body);
	if ($mail->send()) {
		return true;
	} else {
		return false;
	}
}
//
function checkSMTP(){
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPAuth   = true;
	$mail->SMTPSecure = 'ssl'; 
	$mail->Host       = getConfParam('SMTP_HOST');
	$mail->Port       = getConfParam('SMTP_PORT');
	$mail->Username   = getConfParam('SMTP_USER');
	$mail->Password   = getConfParam('SMTP_PASSWORD');  

	$mail->smtpConnect();
	if($mail->smtpConnect()){
		$mail->smtpClose();
	    return true;
	}
	else{
		return false;
	}
}

function activeSMTP(){
	if(getConfParam('SMTP')==1){

		if(getConfParam('SMTP_HOST')!="" && getConfParam('SMTP_PORT')!="" && getConfParam('SMTP_USER')!="" && getConfParam('SMTP_PASSWORD')!=""){
			if(checkSMTP()){
				return true;
			}else{
				return false;
			}
		}else return false;
	
	}else return false;
}
