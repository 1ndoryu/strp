$(document).ready(function(){
    const container = $(".photos_list");
    real_time_validate_form();

    $("#post_photo").on('change', function(){
        if(this.files[0].type.match('image/jpeg') ||
         this.files[0].type.match('image/png') ||
         this.files[0].type.match('image/gif'))
        {
            if(this.files[0].size <= max_file_size * 1000 * 1000)
            {
                var formData = new FormData();
                formData.append('userImage', this.files[0]);
                const index = container.children().length + 1;
                const element = createImageBoxElement(0, index);
                container.append(element);
                $.ajax({
                    url: site_url + "sc-includes/php/ajax/upload_picture.php",
                    type: "POST",
                    data:  formData,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(data_){
                        $("#photo_container-" + index).removeClass('loading');
                        $("#photo_container-" + index).html(data_);
                        $("#photo_container-" + index + " .edit-photo-icon")[0].onclick = function(){
                            editImage(index);
                        };
                        container.show();
                        $("#error_photo").hide();
                        $("#error_photos").hide();
                        
                        updateBoxButtons();
                        if(DATAJSON['max_photos'] == index)
                        {
                            $("#post_photo").attr('disabled', true);
                            $(".photos-button").addClass('disabled');
                        }
                    },
                    timeout: 30000        
                });

            }else $("#error_photos").html(lang_var[3] + " " + max_file_size + " Mb").show();

        }else $("#error_photos").html(lang_var[4]).show();

    });

    $("#post_info_btn").on('click', function(){
        if($("#post_info").attr('open') == "open")
            $("#post_info").attr('open', false);
        else
            $("#post_info").attr('open', true);
        $(this).find('i').toggleClass('fa-info-circle fa-times-circle');
    });
    $("#phone").on('input', formatPhone);

    addStyle('cropper.min.css')
    

});

function real_time_validate_form()
{
    $("#email").on('input', function()
    {
        checkLimits();
    });

    $("#region_container").click(function(){
        pre_validate_form(1);
    });
    $("#city").on('focusin', function(){
        pre_validate_form(2);
    });
    $("#tit").on('focusout', function(){
        pre_validate_form(3);
    });
    $("#text_editable").on('focusout', function(){
        pre_validate_form(4);
    });
    $("#horario-container").on('focusout', function(){
        pre_validate_form(5);
    });
    $("#dis-container").on('focusout', function(){
        pre_validate_form(6);
    });

    $("#name").on('focusin', function(){
        pre_validate_form(7);
    });

    $("#name").on('focusout', function(){
        pre_validate_form(8);
    });

    $("#email").on('focusout', function(){
        pre_validate_form(9);
    });
    $("#phone").on('focusout', function(){
        pre_validate_form(10);
    });

}

function gotToLogin()
{
    window.location.href = "/index.php?login";
}

function updatePhotolist()
{
    const container = $(".photos_list");

    if(container[0].children.length > 0)
        container.show();
    else
        container.hide();

}
function rotateLeft(index)
{
    const img  = $(`#photo_container-${index} img`);
    const rotation = $(`#rotation-${index}`);
    let n_rotation = parseInt(rotation.val());
    n_rotation -= 90;
    if(n_rotation < 0)
        n_rotation = 270;
    rotation.val(n_rotation);
    if(img.length == 0)
        return;
    img.css('transform', `rotate(${n_rotation}deg)`);
}

function rotateRight(index)
{
    const img  = $(`#photo_container-${index} img`);
    const rotation = $(`#rotation-${index}`);
    let n_rotation = parseInt(rotation.val());
    n_rotation += 90;
    if(n_rotation >= 360)
        n_rotation = 0;
    rotation.val(n_rotation);
    if(img.length == 0)
        return;
    img.css('transform', `rotate(${n_rotation}deg)`);
}

function transferPhoto(index1, index2)
{
    const photo1 = $(`#photo_container-${index1} img`);
    const photo2 = $(`#photo_container-${index2} img`);
  
    if(photo1.length == 0)
        return;

    const container1 = $(`#photo_container-${index1}`);
    const container2 = $(`#photo_container-${index2}`);
    const html = container1.html();
    container1.html(container2.html());
    container2.html(html);
    const rotation = $(`#rotation-${index1}`).val();
    $(`#rotation-${index1}`).val($(`#rotation-${index2}`).val());
    $(`#rotation-${index2}`).val(rotation);
    
    if(photo2.length == 0)
    {
        container1.addClass('free');
        $(`#photo_container-${index1} input[type=file]`)[0].id = `photo-${index1}`;
        $(`#rotation-${index1}`).val(0);
        return;
    }

}
function removePhotoBox(index)
{
    const container = $(`#photo_container-${index}`);
    const box = container.parent();
    box.remove();
}

function formatPhone(e)
{
    // Eliminar caracteres no numéricos
    let telefono = e.target.value.replace(/\D/g, '');

    // Formatear con el patrón xxx.xxx.xxx
    // if (telefono.length > 3) {
    //     telefono = telefono.slice(0, 3) + '.' + telefono.slice(3);
    // }
    // if (telefono.length > 7) {
    //     telefono = telefono.slice(0, 7) + '.' + telefono.slice(7);
    // }

    e.target.value = telefono;
}

function checkImagesEdit()
{
    return $(".photos_list img").length > 0
}

function checkImages()
{
    if($("#fieldset_photos").is(':hidden'))
        return true;

    const container = $(".photos_list");

    if(container[0].children.length > 0)
        return true;

    return false;
}

function createImageBoxElement(rotation, index)
{
    const box = document.createElement('div');
    box.classList.add('photo_box');
    box.draggable = true;
    const photoContainer = document.createElement('div');
    photoContainer.classList.add('photo_list');
    photoContainer.classList.add('loading');
    photoContainer.id = `photo_container-${index}`;
    // photoContainer.innerHTML = `
    // <div class="removeImg"><i class="fa fa-times" aria-hidden="true"></i></div>
    // <span class="helper"></span>
    // <img src="${src}" alt="photo" class="img-responsive">
    // <input type="hidden" name="photo_name[]" value="${value}">
    // `;
    const photos_options = document.createElement('div');
    photos_options.classList.add('photos_options');
    photos_options.innerHTML = `
            <a style="display: none;" href="javascript:void(0);" onclick="transferPhoto(${index},${index-1})">
				<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
			</a>
            <a href="javascript:void(0);" onclick="rotateRight(${index})">
				<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M522-80v-82q34-5 66.5-18t61.5-34l56 58q-42 32-88 51.5T522-80Zm-80 0Q304-98 213-199.5T122-438q0-75 28.5-140.5t77-114q48.5-48.5 114-77T482-798h6l-62-62 56-58 160 160-160 160-56-56 64-64h-8q-117 0-198.5 81.5T202-438q0 104 68 182.5T442-162v82Zm322-134-58-56q21-29 34-61.5t18-66.5h82q-5 50-24.5 96T764-214Zm76-264h-82q-5-34-18-66.5T706-606l58-56q32 39 51 86t25 98Z"/></svg>
			</a>
     
            <a style="display: none;" href="javascript:void(0);" onclick="transferPhoto(${index},${index+1})">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/></svg>
            </a>
            `;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'optImgage[][rotation]';
    input.classList.add('rotation');
    input.value = rotation;
    input.id = `rotation-${index}`;
    box.appendChild(photoContainer);
    box.appendChild(photos_options);
    box.appendChild(input);

    return box;

}

function createImageOptions(index)
{
    return `<a style="display: none;" href="javascript:void(0);" onclick="transferPhoto(${index},${index-1})">
				<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
			</a>
            <a href="javascript:void(0);" onclick="rotateRight(${index})">
				<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M522-80v-82q34-5 66.5-18t61.5-34l56 58q-42 32-88 51.5T522-80Zm-80 0Q304-98 213-199.5T122-438q0-75 28.5-140.5t77-114q48.5-48.5 114-77T482-798h6l-62-62 56-58 160 160-160 160-56-56 64-64h-8q-117 0-198.5 81.5T202-438q0 104 68 182.5T442-162v82Zm322-134-58-56q21-29 34-61.5t18-66.5h82q-5 50-24.5 96T764-214Zm76-264h-82q-5-34-18-66.5T706-606l58-56q32 39 51 86t25 98Z"/></svg>
			</a>

            <a style="display: none;" href="javascript:void(0);" onclick="transferPhoto(${index},${index+1})">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/></svg>
            </a>
            `;
}

function updateBoxButtons()
{
    const container = $(".photos_list .photo_box");
    for(let i = 0; i < container.length; i++)
    {
        const photo  = $(container[i]).find('.photo_list'); 
        photo.attr('id', `photo_container-${i + 1}`);
        const options = $(container[i]).find('.photos_options');
        options.html(createImageOptions(i + 1));
        $(container[i]).find('.rotation')[0].id = `rotation-${i + 1}`;
    }

    if($("#post_photo").data('arrow') == false)
        return;
    if(container.length < 2)
        return;
    for(let i = 0; i < container.length; i++)
    {
        const options = $(container[i]).find('.photos_options');
        if(options.children().length > 0)
        {
            options.find('a:first').show();
            options.find('a:last').show();
            if(i == 0)
                options.find('a:first').hide();
            if(i == container.length - 1)
                options.find('a:last').hide();
        }
    }
}

function filterFieldset(cat)
{
    const fieldset = $("#fieldset_photos");
    if(cat == 331)
    {
        fieldset.hide();
        $("#esp_fields").hide();
        return;
    }
    $("#esp_fields").show();
    fieldset.show();
}

function checkLimits()
{
    const mail = $("#email").val();
    return new Promise((resolve, reject) => {
        $.get(site_url + "sc-includes/php/ajax/check_limits.php", { mail: mail } , function(res)
        {
            if(res.status == 1)
            {
                $("#dialog_registred").attr('open', true);
                reject()
            }
            resolve();
        }, "json");
    });

}

function ajustarTexto()
{
    let text = $("#text").val();
    text = asegurarPuntoFinal(text);
    $("#text").val(text);
    $("#text_editable").text(text);

}

function pre_validate_form( step){

	hiddenError();
    ajustarTexto();
    if(!validMail("#email")){

		error=true; $("#error_email").show(); 

		return false;

	}else $("#error_email").hide();

    if(step == 9)
        return true;

	sel=['category','region'];

    for(var i=0; i< sel.length; i++){

		if(!valSelect($("#"+sel[i]).val())){

			error=true; 

			$("#error_"+sel[i]).show(); 

			//scroll_To(sel[i]);


			return false;
		}else 
        {
            $("#error_"+sel[i]).hide();
            if(i >= step)
                return true;
        }

	}

    if(step == 2)
        return true;
	error=false;

	extra_fields=['km_car', 'date_car', 'date_car', 'fuel_car', 'room', 'bathroom', 'area'];


    var tit=$("#tit").val();
    if(!filterWordTitle())
        return false;

	if(!req($("#tit").val()) || (tit.length<10 || tit.length>50))
    {

		error=true; 
		if(tit.length==0)
        {
            $("#error_tit").html('Escriba un título para el anuncio').show(); 
        }
		else if(tit.length<10)
        {
            $("#error_tit").html('El titulo debe tener más de 10 caracteres').show(); 
        }
        else{
            $("#error_tit").html('Exede el número de caracteres permitidos en el título para el anuncio').show(); 
        }
		

		scroll_To('tit'); 
		return false;

	}else $("#error_tit").hide()

    if(step == 3)
        return true;

	var text=$("#text").val();
    if(!filterWordText())
        return false;

	if(!req($("#text").val()) || (text.length<30 || text.length>500)){

		error=true; 
		if(text.length==0)
        {
            $("#error_text").html('Escriba una descripción para su anuncio').show();  
        }
		else if(text.length<30)
        {
            $("#error_text").html('La descripción debe tener más de 30 caracteres').show(); 
        }
        else{
            $("#error_text").html('Estás exediendo el número de caracteres permitidos en la descripción del anuncio').show(); 
        }

			scroll_To('text_editable'); return false;

	}else $("#error_text").hide();


    if(step == 4)
        return true;



	for(var i = 0; i< extra_fields.length; i++){
		element = extra_fields[i];
		if($('#'+ element).val() != undefined){
			if($('#' + element).val() == ""){
				$('#error_' + element).show();
				scroll_To(element);
				return false;
			}
		}else
			$('#error_' + element).hide();
	}
	

	if($("#horario-final").val() == $("#horario-inicio").val()){

		error=true; 
		$("#error_horario").show(); 
		scroll_To('horario-final'); 
		return false;

	}else $("#error_horario").hide();

    if(step == 5)
        return true;

	if($("#dis").val() == 0){

		error=true; 
		$("#error_dis").show(); 
		scroll_To('dis'); 
		return false;

	}else $("#error_dis").hide();

    if(step == 6)
        return true;

	if(!checkImages())
	{
		$("#error_photo").show(); 
		return false;
	}else $("#error_photo").hide();

    if(step == 7)
        return true;

    if($("#name").val() == ""){

        error=true; 

        $("#error_name").show(); 

        scroll_To('name'); 
        return false;

    }else $("#error_name").hide();


    if(step == 8)
        return true;


    if($("#phone").val() == ""){

        error=true; 

        $("#error_phone").show(); 

        scroll_To('phone'); 
        return false;

    }else $("#error_phone").hide();

    if(step == 10)
        return true;

	if(!valSelect($("#sellerType").val())){

		error=true; 

		$("#error_sellerType").show(); 

		scroll_To('sellerType');

		return false;

	}else 
		$("#error_sellerType").hide();



	if(!terms()){

		error=true;

		$("#error_terminos").show(); 

			scroll_To('terminos'); return false;

	}else $("#error_terminos").hide();

}

function asegurarPuntoFinal(texto) {
    // Eliminar espacios al principio y al final
    texto = texto.trim();

    // Si el texto está vacío, devolver una cadena vacía
    if (texto === "") {
        return "";
    }

    // Verificar si el texto termina con un punto
    if (!texto.endsWith('.')) {
        return texto + '.';
    }

    // Si termina con un punto, asegurarse de que no haya espacios adicionales después
    return texto.replace(/\.\s*$/, '.');
}