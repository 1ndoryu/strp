	
	$(document).ready(function () {
		$('#user_action').change(function(){
			if(this.value == 1){
				const credits = document.createElement('input');
				credits.type = "text";
				credits.placeholder = "Créditos";
				credits.name = "credits";
				const button = $('#actions_users').remove();
				$('#user_action_form').append(credits);
				$('#user_action_form').append(button[0]);

			}
		});

		$(document).on('click', '#actions_users', function(){
			switch ($('#user_action').val()) {
				case "1":
				case "2":
					const users = $('.user_check');
					const data_users = [];
					for(i = 0; i < users.length ;i++){
						if(users[i].checked)
							data_users.push(users[i].value);
					}
					console.log(data_users);
					$("#user_data").val(JSON.stringify(data_users));
					$('#user_action_form').submit();
					break;
			
				default:
					break;
			}
		});
	});
	
	function toggle(user_id){
		//toggle_display("reg_" + user_id);
		$("#reg_" + user_id).attr('style', 'cursor: pointer');
		$("#adult_" + user_id).attr('style', 'cursor: pointer');
		$('#adult_'+user_id).removeClass('selected');
		$('#reg_'+user_id).removeClass('selected');

		//toggle_display("adult_" + user_id);
		toggle_display("hidden_menu_" + user_id);
	}

	function show_input(elem){
        var id_array = elem.id.split('_');
		var user_id = id_array[id_array.length - 1];
		//console.log(id_array[0]);
		$(elem).addClass('selected');
		
		//toggle_display("reg_" + user_id);
		//toggle_display("adult_" + user_id);
		toggle_display("hidden_menu_" + user_id);
		
		if (id_array[0] == 'reg'){
		    console.log('reg');
		    document.getElementById("credit_type_" + user_id).value = 'credits';
		} 
		
		if (id_array[0] == 'adult'){
		    console.log('adult');
		     document.getElementById("credit_type_" + user_id).value = 'credits_adult';
		} 
		
		
    }

    function toggle_display(elemid){

	    if (document.getElementById(elemid).style.display === 'none'){
	        document.getElementById(elemid).style.display = 'block';
	    } else {
	        document.getElementById(elemid).style.display = 'none';
	    }
    }


	function setUserModal(id_user, credits, name, mail, pass, rol, limit)
	{
		$("#adminedit-id").val(id_user);
		$("#adminedit-nombre").val(name);
		$("#adminedit-mail").val(mail);
		$("#adminedit-pass").val(pass);
		$("#adminedit-credits").val(credits);
		$("#adminedit-rol").val(rol);
		$("#adminedit-limit").val(limit);
		$("#modal-edituser").modal('show');
	}

	function setUser(){
		$.post("inc/ajax/setuser.php", $('#edituser-form').serialize(),
			function (data, textStatus, jqXHR) {
				if(textStatus == "success"){
					if(data == 1){
						$(".info_valid span").html("Usuario Editado");
						$(".info_valid").show();
						$("#modal-edituser").modal('hide');
						location.href = site_url + "sc-admin/index.php?id=manage_users&c=1";
					}
				}
			},
			"text"
		);
	}

	function setBanModal(id_user, motivo, date)
	{
		$("#ban-id").val(id_user);
		$("#ban-motivo").val(motivo);

		if(motivo == "0")
		{
			$("#ban-date-div").hide();
			$("#ban-action").val("0");
			$("#ban-motivo").val("2");
			$("#ban-motivo").attr('disabled', false);
			$("#ban-btn").html("Bloquear");
			$("#ban_title").html("Bloqueo");
		}else
		{
			$("#ban-date").val(date);
			$("#ban-date-div").show();
			$("#ban-action").val("1");
			$("#ban-motivo").attr('disabled', 'disabled');
			$("#ban-btn").html("Desbloquear");
			$("#ban_title").html("Desbloqueo");
		}
		
		$("#modal-ban").modal('show');
	}

	function setCreditModal(id_user)
	{
		$("#add-credit-id").val(id_user);
		$("#modal-credit").modal('show');
	}

	function newUser()
	{
		$("#modal-newuser").modal('show');
	}

	
// 	function set_href(elem){
// 	    var credits = elem.parentNode.getElementsByTagName("input")[0].value;
// 	    console.log(elem.href + credits);
// 	    elem.href += credits;
// 	}

    