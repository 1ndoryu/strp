$(function(){
	$(".open-menu").click(function(){
		$(".menu").slideToggle();
	});
	$("input[name='SSL_OPTION']").click(function(){
		var sit_=$("input[name='SITE_URL']").val();
		if($("input[name='SSL_OPTION']:checked").val()=="1"){
			sit_=$("input[name='SITE_URL']").val().replace('http://','https://');
		}else{
			sit_=$("input[name='SITE_URL']").val().replace('https://','http://');
		}
		$("input[name='SITE_URL']").val(sit_);
	});
	
	
	$(".items_check").click(function(){
		if($(this).is(':checked')){
			$(".item_check, .items_check").each(function() {
                $(this).prop( "checked", true );
            });
		}else{
			$(".item_check, .items_check").each(function() {
                $(this).prop( "checked", false );
            });
		}
	});

	$('#trash-btn-restart').click(function(){
		if($('#trash-id').val() != ""){
			$('#trash-id').attr('name', 'restart');
			$('#trash-form').submit();
		}
	});

	$('#trash-btn-delete').click(function(){
		if($('#trash-id').val() != ""){
			$('#trash-id').attr('name', 'del');
			$('#trash-form').submit();
		}
	});

	$("#recovery_admin_pass").click(function(){
		$('.adminform').fadeOut(0);
		$('.recoverAdmin').fadeIn(0);
	});

	$('#delad-btn').click(function(e){
		e.preventDefault();
		if($('#delad-motivo').val() == 0){
			$('#delad-motivo').addClass('is-invalid');
		}else
			$('#delad-form').submit();
	});

	$("#backtoadminlogin").click(function(){
		$('.recoverAdmin').fadeOut(0);
		$('.adminform').fadeIn(0);
	});

	$('#edituser-btn').click(function(e){
		e.preventDefault();
		if($('#adminedit-mail').val() == ""){
			$('#adminedit-mail').addClass('is-invalid');
			return false;
		}
		if($('#adminedit-pass').val() == ""){
			$('#adminedit-pass').addClass('is-invalid');
			return false;
		}
		setUser();

	});
	
	$('#recoverPassAdmin').click(function(event){
		event.preventDefault();
		$.ajax({
			type: "post",
			url: "inc/recover_admin_pass.php",
			data: {ma: $('#recoverAdminMail').val()},
			dataType: "text",
			success: function (response) {
				if(response == 1){
					$('#recoverAdminMail').addClass('is-valid');
					$('#recoverAdminMail').val("");

				}else{
					$('#recoverAdminMail').addClass('is-invalid');
					$('.recoverAdmin div[class="invalid-feedback"]').html(response);
				}
			}
		});
	});
	$('#newpassadminb').click(function(event){
		const pass2 = $('#pass-confirm');
		if($('#newpassadmin').val() != pass2.val()){
			pass2.addClass('is-invalid');
			return false;
		}
	});
});


function setUserModal(elm) {
	var datos = $("#" + elm.id).data('usuario');
	console.log(datos);
	$("#adminedit-nombre").val(datos.nombre);
	$('#adminedit-mail').val(datos.mail);
	$('#adminedit-pass').val(datos.pass);
	$('#adminedit-id').val(datos.ID);
	$('#adultcredits').val(datos.Acredits);
	$('#credits').val(datos.credits);
	$('#edituser-form input').removeClass('is-invalid');
}

function getUser(index, user){
	elm = $("#" + index);
	elm.data('usuario', user);
}





function setTrash(index, datos){
	$('#'+index).data('ad', datos);
}

function setTrashModal(datos){

	console.log(datos);
	const form = "#trash-form ";
	if(typeof(datos.image) == "string")
		$(form + 'div[role="image"]').html('<img src="'+ datos.image + '" alt="'+ datos.titulo +'" class="trash_img img-fluid">');
	else{
		var htmlstart = '<div class="glide carousel-admin">'+
			'<div class="glide__track" data-glide-el="track">'+
				'<ul class="glide__slides">';
		var htmlend = '</ul>'+
			'</div>'+
				'<div class="glide__arrows" data-glide-el="controls">'+
					'<button class="glide__arrow glide__arrow--prev" aria-label="anterior" data-glide-dir="<">'+
						'<i class="fa fa-chevron-left"></i>'+
					'</button>'+
					'<button class="glide__arrow glide__arrow--next" aria-label="siguiente" data-glide-dir=">" >'+
						'<i class="fa fa-chevron-right"></i>' +
					'</button>' +
				'</div>' +
			'</div>' +
			'<script type="text/javascript" src="../src/js/carousel2.js"></script>';
	
		var htmlcontent = "";
		datos.image.forEach(element => {
			htmlcontent += '<li class="glide__slide">'
			htmlcontent += '<img src="'+ element + '" alt="'+ datos.titulo +'" class="trash_img w-100 img-fluid">';
			htmlcontent += '</li>';
		});
	
		var html = htmlstart + htmlcontent + htmlend;
			$(form + 'div[role="image"]').html(html);
	}
	$(form + 'h5').html(datos.titulo);
	$(form + 'p').html(datos.texto);
	$(form + 'span[class="float-right"]').html(datos.user);
	$(form + 'span[class="text-date"]').html("Eliminado el "+datos.date);
	$('#trash-id').val(datos.id);
}

function togglePass(e)
{
	if($('#pass').attr('type')=='password')
	{
		$('#pass').attr('type','text');
		$(e).removeClass('fa-eye');
		$(e).addClass('fa-eye-slash')
	}
	else
	{
		$('#pass').attr('type','password');
		$(e).removeClass('fa-eye-slash')
		$(e).addClass('fa-eye');
	}
}