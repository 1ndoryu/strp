function show_menu_premium(idad){
    $('#premium_id').val(idad);
    $('#premium-modal').modal('toggle');
}

function show_menu_listing(idad){
    $('#listado_id').val(idad);
    $('#listado-modal').modal('toggle');
}
function show_menu_destacado(idad){
    $('#destacado_id').val(idad);
    $('#destacado-modal').modal('toggle');
}
function show_menu_autosubida(idad){
    $('#autorenueva_id').val(idad);
    $('#autosubida-modal').modal('toggle');
}

function toggle_display(elemid){
    if (document.getElementById(elemid).style.display === 'none'){
        document.getElementById(elemid).style.display = 'block';
    } else {
        document.getElementById(elemid).style.display = 'none';
    }
}

function toggle_display_selec(elemid){
    if (document.getElementById(elemid).style.display === 'none'){
        document.getElementById(elemid).style.display = 'inline-grid';
    } else {
        document.getElementById(elemid).style.display = 'none';
    }
}

function set_href(sel){
    var id_array = sel.id.split('_');
    var ad_id = id_array[id_array.length - 1];
    
    console.log(ad_id);
    document.getElementById("send_" + ad_id).href += '&value=' + sel.value;
}

function SetMailer(mail, title, id){
    $('#form-mailer-para').val(mail);
    $('#form-mailer-title').val(title)
    $('#form-mailer-id').val(id);
}

function setModalDel(id, r) {
	$("#delad-form input").removeClass('is-invalid');

    $("#delad-modal .delete-text").html("Eliminar");

    if(r == 1){
        $("#delad-comment").hide();
        $("#desactivar-comment").show();
    }else{
        $("#delad-comment").show();
        $("#desactivar-comment").hide();
    }

    $.get("inc/ajax/load_del_options.php", {r},
    function(data){
        $("#delad-motivo").html(data);
        console.log(data);
        $("#delad-modal").modal('show');
    }, "html");

	$('#delad-id').val(id);
}

function removeImage(id){
	$.ajax({
		type: "POST",
		url: "inc/ajax/remove_image.php",
		data: {id: id},
		success: function(data){
			console.log(data);
			$("i[data-id='" + id + "']").parent().remove();
		}
	});
}

function setModalDesactivar(id)
{
    $("#desactivar_id").val(id);
    $("#modal_desactivar").modal("show");
}

$(document).ready(function(){
    $('.send_mail').click(function(e){
        e.preventDefault();
        $('#modal-mailer').modal('show');
        $('#form-mailer-content').removeClass('is-invalid');
        $('#form-mailer-asunto').removeClass('is-invalid');
        $('#form-mailer-title').removeClass('is-invalid');
    });

    $('.min-img-item .fa-times-circle').click(function(e){
        e.preventDefault();
        var id = $(this).data('id');
        console.log(id);
        removeImage(id);
    });

    $('#modal-mailer-btn').click(function(e){
        e.preventDefault();
        const content = $('#form-mailer-content');
        const asunto = $('#form-mailer-asunto');
        const titulo = $('#form-mailer-title');
        content.removeClass('is-invalid');
        asunto.removeClass('is-invalid');
        titulo.removeClass('is-invalid');
        
        if(asunto.val() == ''){
            asunto.addClass('is-invalid');
            return false;
        }
        if(titulo.val() == ''){
            titulo.addClass('is-invalid');
            return false;
        }
        
        if(content.val() == ''){
            content.addClass('is-invalid');
            return false;
        }

        $('#form-mailer').submit();


    });

    $('#action_do').change(function(){
        if($(this).val() == '3'){
            $(".hidden-action-filds").removeClass("hidden");
        }else
            $(".hidden-action-filds").addClass("hidden");
 
    });
    
});

function setDiscard(idad)
{
    $("#discard_id").val(idad);
    $('#discard-modal').modal('show');
}

function extenderPlazo(idad, service)
{
    $("#extend_id").val(idad);
    $("#service_extend").val(service);
    $('#extend-modal').modal('show');
}