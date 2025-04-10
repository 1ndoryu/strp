$(document).ready(function(){

	$("#butReg").click(function () {

		validRegister();

	});

    $(".register-pass i").click(function(){
        if($(this).hasClass('fa-eye')){
            $(this).removeClass('fa-eye');
            $(this).addClass('fa-eye-slash');
            $("#pass1_register").attr('type','text');
        }else{
            $(this).removeClass('fa-eye-slash');
            $(this).addClass('fa-eye');
            $("#pass1_register").attr('type','password');
        }
    });

    $("#rol_register").change(function(){

        if($(this).val() == 0){

            $("#rol_visit_info").show();
            $("#rol_register_info").hide();

        }else{
            $("#rol_visit_info").hide();
            $("#rol_register_info").show();
        }

    });

});

function validRegister(){

	hiddenError()

	if($("#name_register").val().length>=4){

		if(validMail("#mail_register")){

            if($("#pass1_register").val().length>=6){

                if(terms()){

                    checkRegisteredEmail($("#mail_register").val()).then(function(){

                        $("#register_form").submit();

                    }).catch(() => {
                        $("#error_mail2").show();
                        scroll_To("#error_mail2");
                    });

                   

                }else{

                        $("#error_terminos").show();

                        scroll_To('terminos');

                }

            }else{

                $("#error_pass1").show();

                scroll_To('pass1_register');

            }

		

		}else{

			$("#error_mail").show();

			scroll_To('mail_register');

		}

	}else{

		$("#error_name").show();

		scroll_To('name_register');

	}

}

function checkRegisteredEmail(mail)
{
    return new Promise((resolve,reject)=>{
        jQuery.ajax({
            type: "GET",
            url: site_url + "sc-includes/php/ajax/check_email.php",
            dataType: "json",
            data: {mail: mail},
            success: function (response) 
            {
                if(response.status == 1)
                    resolve();
                else
                    reject();
            },
            timeout: 3000        
        });
    });

}

function newPassword(){

	var pass = generatePassword();

	$("#pass1_register").val(pass);

    $(".register-pass i").removeClass('fa-eye');
    $(".register-pass i").addClass('fa-eye-slash');
    $("#pass1_register").attr('type','text');

}

function generatePassword(len = 8)
{
    // Definir los conjuntos de caracteres
    const minusculas = "abcdefghijklmnopqrstuvwxyz";
    const mayusculas = minusculas.toUpperCase();
    const numeros = "0123456789";
    const simbolos = "ñ@#€$%&";

    // Combinar todos los conjuntos de caracteres en uno solo
    const conjuntoCompleto = minusculas + mayusculas + numeros + simbolos;

    // Inicializar la contraseña vacía
    let contraseña = "";

    // Generar la contraseña caracter por caracter
    for (let i = 0; i < len; i++) {
        const indiceAleatorio = Math.floor(Math.random() * conjuntoCompleto.length);
        contraseña += conjuntoCompleto[indiceAleatorio];
    }

    // Devolver la contraseña generada
    return contraseña;

}
