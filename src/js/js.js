var lang_var = ["Te hemos enviado un email con tu contraseña","Deseas eliminar tu cuenta?\n AVISO: Serán eliminados también tus anuncios.","Deseas eliminar este anuncio?","El tamaño máximo permitido es de","Sólo se permiten imágenes en formato .gif, .png, .jpg","anuncio/","anuncios/","anuncios-en-","anuncios-","mi-cuenta/","mis-anuncios/"];

var mprice0;
var mprice1;

$(function(){

	"use strict";

	$(".login").click(function () {

		popupLogin();

    });

    $("#popup_overlay").click(function () {

		popupWindowHide()
		
		$('.login_div').fadeOut("slow");

	});

	$('.filter_select option').mouseover(function () { 
		console.log("aja");
	});
	

	$("#popup_floating").on('click',"#close",function(){

       popupWindowHide()

	});

	const keyword1 = $("#keyword_search1");
	const keyword2 = $("#keyword_search2")
	
	keyword2.keyup(function(){
		keyword1.val(keyword2.val());
	})
	keyword1.keyup(function(){
		keyword2.val(keyword1.val());
	})

	$(".menu-navbar").click(function(){

		$(".navbar").addClass('opened');

	});

	$(".navbar .close-navbar, .navbar a").click(function(){

		$(".navbar").removeClass('opened');

	});

	$("#keyword_search1").keypress(function (e) {

		if (e.which == 13) {

			searchMain1();

			return false; 

		}

	});
	$("#keyword_search2").keypress(function (e) {

		if (e.which == 13) {

			searchMain2();

			return false; 

		}

	});

	$('#btn_compartir_email').on('click', function(e){
		e.preventDefault();
		hiddenError();
		const campos = ['compartir_to_name', 'compartir_to_email', 'compartir_name', 'compartir_email', 'compartir_msg'];
		var tam = campos.length;
		for(let i = 0; i < tam; i++){
			if($('#' + campos[i]).val().length == 0){
				$('#error_' + campos[i]).show();
				return 0;
			}
			if(i == 1 || i == 3){
				if(!validMail('#' + campos[i])){
					var campo = $('#error_' + campos[i]);
					campo.html('Email invalido');
					campo.show();
					return 0;
				}
			}
		}
		$('#form_compartir_mail').submit();
	});

	$("#but_search_main1").click(function(){ 

		//searchMain1();
		filterSearch();

	});	
	$("#but_search_main2").click(function(){ 

		searchMain2();

	});	

	$("#search_cat2, #region_search2").change(function(){

		searchMain2();

	});
	
	$("#Cookies_btn").click(function(e){
		e.preventDefault();
		$.post("sc-includes/php/ajax/cofig_cookies.php", $(".cookie_form").serialize(),
			function (data, textStatus, jqXHR) {
				if(textStatus == "success"){
					if(data == 1)
					$('.info_valid').show();
					setCookie('cookie_advice','1',365);
					$('.config_cookies_content').hide(0);
					$('#cookiediv').hide(0);
					$('.adv-container').show(0);
				}
			},
			"text"
		);
	});

	$("#Cookies_volver").click(function(){
		$('#cookie_main').show(0);
		$('#cookie_config').hide();
	});

	$('#reject_cookies').click(function(){
		history.back();
	});

	$("#butDataAccount").click(function () {

		dataAccountUpdate();

	});

	$("#butChangePass").click(function () {

		passAccountUpdate();

	});

	$(".filter .open_filter").click(function () {

		$(".filter_options").slideToggle();
		$(this).toggleClass('opened');

	});
	$("#filterbutton").click(function () {

		$(".filter").slideToggle();
		$(this).find('i').toggleClass('fa-chevron-up fa-chevron-down');

	});

	$("body").on('keypress','.number',function(){

			return onlyNumber(event);

	});

	

	$(".numeric").numeric({ decimal : ".",  negative : false, scale: 3 });

    $('.select2_custom').select2({
	    minimumResultsForSearch: Infinity
	});

	$('.select2_custom').on('select2:open', function (e) {
		setTimeout(function(){ 
			$('.select2-results__group').each(function() {
		    	if($(this).siblings().find('.select2-results__option--selected').length > 0){
		    		$(this).toggleClass('openedGroup');
		    	}
			});
		}, 100);
	});
	$("body").on('click', '.select2-results__group', function() {
		if($(this).hasClass('openedGroup')){
			$(this).toggleClass('openedGroup');
		}else{		
			$('.select2-results__group').removeClass('openedGroup');
			$(this).toggleClass('openedGroup');
		}

	})
	/* $('.phone_number').mask('000.000.000');
	 $('#phone_account').mask('000.000.000');
	 $('#phone_register').mask('000.000.000');*/

	 //$('input[type="number"]').val($('input[type="number"]').val().replace(',', '.'));



	/*$(".phone_number").keypress(function(){

			return phoneChars(event);

	});*/

	$(".fav").click(function(){

		addFav($(this).data('id').split('-')[1]);

	});

		var minval = 100;
		var maxval = 100000;

		if($('#price_0').val() != "")
			minval = $('#price_0').val();

		if($('#price_1').val() != "")
			maxval = $('#price_1').val();

	$( "#slider-range" ).slider({
		range: true,
		min: 100,
		max: 100000,
		step: 100,
		values: [ minval, maxval ],
		slide: function( event, ui ) {
			$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
			$("#price_0").val(ui.values[0]);
			$("#price_1").val(ui.values[1]);
		}
	});		
	$( "#amount" ).val( "€" + $( "#slider-range" ).slider( "values", 0 ) +
      " - €" + $( "#slider-range" ).slider( "values", 1 ) );

	$("#region_").change(function(){

		$("#city_").load(site_url + "sc-includes/php/ajax/load_city_seo.php", {region: $("#region_").val()},function(response,status){

	      //if(status=="success")	filterUpdate();

		});

	});	

	updateprice();

	
	$("#search_filter").click(function(){ 
		filterSearch();
	 });
	
	$(".sel").change(function(){
		console.log('aqui')
		filterUpdate($(this).attr('id'));

	});


	/*
	$("#order_list").change(function(){

		location.href=$(this).val();

	});

	$("#seller_list").change(function(){

		location.href=$(this).val();

	});
	*/
	// Access
	$('#phone').click(function(){
		const phone = $('#phone1');
		const a = $(this.children[1].children[0]);
		if(phone.is(':visible')){
			phone.hide();
			a.removeClass('fa-chevron-up');
			a.addClass('fa-chevron-down');
		}else{
			phone.show();
			a.removeClass('fa-chevron-down');
			a.addClass('fa-chevron-up');
		}
	});

	$(".login_div").on('click',"#recover_pass",function(){

		const login = $('.login_div');
		const width = login[0].clientWidth;
		$("#response_login").hide();
		$("#formLogin").addClass('hidden');

		$("#forgot").removeClass('hidden');

		login.css('width', width);
		
	});

	$('.login_div').on('click', '#close',   function(){
		$("#popup_overlay").fadeOut("slow");
		$('.login_div').fadeOut("slow", function(){

			$("#formLogin").removeClass('hidden');
	
			$("#forgot").addClass('hidden');

			$('.login_div').css('width', 'auto');
		});


	});

	$(".login_div").on('click',"#recPass",function(){

		jQuery.ajax({

			  type: "POST",

			  url: site_url + "sc-includes/php/ajax/recover_pass.php",

			  dataType: "text",

			  data: {ma: $("#email_rec").val()},

			  success: function (response) {

				  	if(response!= 1)

					$("#response_recover").html(response).addClass('error_recover_response');

					else{

					$("#response_recover").html(lang_var[0]).addClass('success_recover_response');

					$("#formRecover").hide();

					}

			  }

		});

	});	

	$(".login_div").on('keyup',"#mail_login, #pass_login",function(e){
		if (e.key === 'Enter')
		{
			do_login();
		}
	});

	$(".login_div").on('click',"#do_login",function(){

		do_login();

	});

	$("#popup_floating").on('click',"#do_reg",function(){

			location.href=site_url+"crear-cuenta/";

	});

	$("#contact-product").click(function(event) {

		event.preventDefault();

		$("#popup_floating").load(site_url + "sc-includes/contact_item.php",{id_product: $(this).attr('data-id')});

		$("#popup_overlay").fadeTo("fast", 0.8).fadeIn(100).css({width: $(document).width(),height: $(document).height()});

		$("#popup_floating").fadeTo("fast", 1).fadeIn(100).center();

	});

	$(".contact_phone").click(function(){

		var p_var=$(this).attr('id').split('_')[1]

			jQuery.ajax({

			  type: "GET",

			  url: site_url + "sc-includes/php/ajax/load_phone.php",

			  dataType: "text",

			  data: {p: p_var},

			  success: function (response) {

				$("#phone_"+p_var).html(response);	

			  }

			});

	});

	$("#contact_item_btn").click(function(){

		hiddenError();

		if($("#c_name").val()!==""){

			if(validMail("#c_mail")){

				if($("#msg_c").val().length>10){

					jQuery.ajax({

						type: "POST",

						url: site_url + "sc-includes/php/ajax/contact_item.php",

						dataType: "text",

						data: {n: $("#c_name").val(), m: $("#c_mail").val(), p: $("#c_phone").val(), msg: $("#msg_c").val(),i: $("#id_ad_contact").val()},

						success: function (response) {

							$("#contactEmail").html(response);

							$(".contact-product-form").hide();

						}

					});

				}else $("#error_msg_c").show();

			}else $("#error_c_mail").show();

		}else $("#error_c_name").show();

	});

	$(".delete_account a").click(function () {

		if(confirm(lang_var[1])){

			location.href= site_url + lang_var[9] + "?del_account=true";

		}

	});


	$("#cont_web").click(function(){ contact_web() });

	$("#paypal_1").click(function(){ 
	    
	    var value = document.getElementById('premium1_select').value;
	    console.log(value);
        if (value > 0){
            var date = new Date().getTime() + (value*1000);
            var new_date = new Date(date);
            var conf = confirm('SU ANUNCIO ESTARÁ DESTACADO HASTA EL ' + new_date.toLocaleString() + '\n\nNo podrá modificar su anuncio hasta que finalice el periodo premium\n¿Está seguro que desea continuar?' );
            if (conf)
                $("#paypal_PREM").submit();
            
        }
        else {
            alert('DEBE SELECCIONAR EL TIEMPO DE DURACIÓN');
        }
	    
	});	

	$("#paypal_2").click(function(){ 
	    
	    var file = document.getElementById('banner_img').files[0];
	    
	    if (!file){
	        alert('DEBE ELEGIR UN ARCHIVO DE IMAGEN VÁLIDO PARA EL BANNER\n');
	        return false;
	    }
	    
	    var duracion = document.getElementById('banner_selector').value;
	    
	    console.log(duracion);
	    
	    if (duracion === '0'){
	        alert('DEBE ELEGIR LA DURACIÓN DEL BANNER\n');
	        return false;
	    }
	    
	    var value = document.getElementById('banner_selector').value;
	    console.log(value);
        if (value > 0){
            var date = new Date().getTime() + (value*1000);
            var new_date = new Date(date);
	        var conf = confirm('Tu banner estará publicado hasta el ' + new_date.toLocaleString() );
            if (conf)
                document.getElementById('paypal_BAN').submit();
        }
	    
	});
	
	$("#paypal_3").click(function(){ $("#paypal_CRED").submit(); });	

	$("#agree_cookies").click(function(){ 
		addCookie(); 
		$('.adv-container').show(0);
	});

	$("#confi_cookies").click(function(){ 
		$('#cookie_config').show(0);
		$('#cookie_main').hide(0);
	 });


	if(getCookie('cookie_advice')!="1"){

		$("#cookiediv").show();

	}

	$(".photo_banner").on('change','.photoFile', function(){

		uploadPictureUser();

	});

	$(".photo_banner").on('click','.removeImg',function(){

		var container = "photo_banner_user";

		$.ajax({

				url: site_url + "sc-includes/php/ajax/delete_user_picture.php",

				type: "POST",

				dataType: "text",

				data:  {name_image : $("#photo_banner_user input[type=hidden]").val()},

				success: function(data){

					if(data=="1"){

					$("#" + container).addClass('free');

					$("#" + container).html('<input name="userBanner" id="userBanner" type="file" class="photoFile" />');

					}

				},

				timeout: 3000        

		});		

	});

	$(".photo_list").on('change','.photoFile', function(){

		var id_photo = $(this).attr('id').split('-')[1];

		$("#error_photo").hide();

		uploadPicturePost(id_photo);

	});

	$(document).on('click','.photo_list .removeImg',function(){

		var container = $(this).parents().attr('id');

		var photo_id = container.split('-')[1];


		$.ajax({

				url: site_url + "sc-includes/php/ajax/delete_picture.php",

				type: "POST",

				dataType: "text",

				data:  {name_image : $("#"+container+" input[type=hidden]").val()},

				success: function(data){

					if(data==1){
						if(typeof DATAJSON !== 'undefined' && !DATAJSON['edit'])
						{
							removePhotoBox(photo_id);
							updateBoxButtons();
							$(".photos-button").removeClass('disabled');
							$("#post_photo").removeAttr('disabled');
						}else
						{
							$("#" + container).addClass('free');
		
							$("#" + container).html('<input name="userImage[]" id="photo-'+photo_id+'" type="file" class="photoFile" />');

						}

					}

				},

				timeout: 3000        

		});		

	});

	$("#region").change(function(){

	$("#city").load(site_url + "sc-includes/php/ajax/load_city.php", {region: $("#region").val()});

	});

	$('#text').keypress(function(){

		return textMaxLength(this);

	});

	$("#butPub").click(function(){

		validate_form();

	});

	$("#editPub").click(function(){

		validate_form_edit();

	});

	$("#category").change(function(){

		$("#extra_fields").load(site_url + "sc-includes/php/ajax/load_extra_fields.php", {cat: $(this).val()});
		loadFilter(this.value);
		filterFieldset(this.value);
	});

	$("#tit").keyup(function(){
		var value=$("#tit").val();
		$('#nro-car-tit').html(value.length);
	});	

	$("#tit").mouseleave(function(){
		var value=$("#tit").val();
		$('#nro-car-tit').html(value.length);
	});

	if($("#text_editable").length > 0)
	{

		$("#text_editable").keyup(function(){
			const value=$(this).text();
			$('#nro-car-text').html(value.length);
		});	
	
		$("#text_editable").mouseleave(function(){
			const value=$(this).text();
			$('#nro-car-text').html(value.length);
		});
	}else
	{
		$("#text").keyup(function(){
			const value=$(this).val();
			$('#nro-car-text').html(value.length);
		});	
	
		$("#text").mouseleave(function(){
			const value=$(this).val();
			$('#nro-car-text').html(value.length);
		});
	}

	$('#new_item_post select, #new_item_post input, #new_item_post textarea').change(function(){
		if(this.value != 0 && this.value != ''){
			$('#error_' + this.id).hide();
		}

	});
	
	$("#search-simple-btn").click(function(){
		searchSimple();
	});

});

// ************************************************************************* //
function searchSimple()
{
	var url=site_url;
	if($("#search-simple").val()!=""){
		url+="anuncios/?q="+$("#search-simple").val() + "&busq";
		location.href=url;
	}
}

function searchMain1(){

	var url=site_url;
	
	if($("#region_search1").val()!="") url+="anuncios-"+$("#region_search1").val()+"/";

	if($("#search_cat1").val()!="") url+=$("#search_cat1").val()+"/";

	if($("#keyword_search1").val()!=""){

		if(url===site_url){

			url+="anuncios/?q="+$("#keyword_search1").val()+"&busq";

		}else{

			url+="?q="+$("#keyword_search1").val()+"&busq";

		}

	}

	location.href=url;

}

//close advert to childs


function loadpay(i, target){
	if(i == 0){
		switch (target) {
			case 'premium':
				alertify.alert('Advertencia', 'Tu anuncio ya esta destacado premium');
				break;
			case 'banner':
				alertify.alert('Advertencia', 'Tu anuncio ya tiene un banner');
				break;
			case 'premium3':
				alertify.alert('Advertencia', 'Tu anuncio ya esta destacado');
				break;
			default:
				break;
		}
		return false;
	}

	const premium = $('#pay-premium');

	$.post("sc-includes/php/ajax/load_pay.php", {i : i, target: target},
	function (data, textStatus, jqXHR) {
			premium.html(data).promise().done(function(){
				premium.fadeIn(100);
			});
		},
		"html"
	);

}

function launchPay(cat, idad){

	if(cat == 0){
		alertify.alert('Advertencia', 'Tu anuncio esta subido al listado');
		return false;
	}

	const listado = $('#pay-listado');
	const url = site_url + "destacar-anuncio/" + idad + "/";
	const credits = $('#listing-credits');

	if(cat == 109){
		credits.val(credits.data('adult'));
		$('#show-credits').text(credits.data('adult'));
	}else{

		credits.val(credits.data('normal'));
		$('#show-credits').text(credits.data('normal'));
	}

	$('#pay-listado form')[0].action = url;

	listado.fadeIn(100);

}

function deleteListing(idad){
	alertify.confirm("Desactivar Listado", "Desactivar este anuncio del listado",
		function(){
			window.location.href = "mis-anuncios?delist="+ idad;
		},
		function(){});
}

function searchMain2(){

	var url=site_url;
	if($("#search_cat2").val()=="")
		return;
	if($("#search_cat2").val()!="") url+= $("#search_cat2").val()

	if($("#region_search2").val()!="") url+="-en-"+$("#region_search2").val()+"/";

	if($("#keyword_search2").val()!=""){

		if(url===site_url){

			url+="anuncios/?q="+$("#keyword_search2").val()+"&busq";

		}else{

			url+="?q="+$("#keyword_search2").val()+"&busq";

		}

	}

	location.href=url;

}

function searchItems(s){
	const list = $('.my_items_list li.item');
	const exp = new RegExp(s,'gi')
	list.hide();
	if(s !== ''){

		list.each(function(i , val){
			const elements = val.getElementsByClassName('searchable');
			if(elements[0].innerText.match(exp) ||
				elements[1].innerText.match(exp) ||
				elements[2].innerText.match(exp)
			){
				$(val).show();
			}
		});

	}else{
		list.show();
	}
	

}

function filterUpdate(activador){

	console.log(activador);
	if(activador == 'Maincategory'){
		var name = $('#'+activador).val();
		var price = 0; 
		name = name.slice(0, -1);
		$.get("sc-includes/php/ajax/load_filter_list.php", {'target': 'category', 'name': name},
		function (data, textStatus, jqXHR) {
			console.log(data);
			$('#category').html(data)
			$('#filter_details').html('');
			if(name == 'motor')
				price = 1;
			else if(name == 'inmobiliaria')
				price = 2;
			else if(name == 'empleo' || name == 'aficiones-y-ocio' || name == 'comunidad')
				price = 3;
		
			$.get("sc-includes/php/ajax/load_filter_list.php", {'price': price},
			function(data){
				console.log(data);
				$('#price_container').html(data).promise().done(function(){
					removeSelect();
					activeCustomSelect();
					updateprice();
				});
			}, 'html');
		},
		"html"
		);


	}else if(activador == 'category'){
		var element = document.getElementById(activador);
		var name = element.value;
		var rep = element.children[1].dataset.pub;
		const filter = $('#filter_details');
		name = name.replace(rep, '');
		name = name.slice(0, -1);

		var target = '0';

		$.get("sc-includes/php/ajax/load_filter_list.php", {'se': name},
			function (data, textStatus, jqXHR) {
				console.log(data);
				filter.html(data);
				return true;
			},
			"text"
		);
		filter.html('');

	}
	

	/*
		var uri = site_url;

		var query="";

		if($("#city_").val()!="") query+=lang_var[7] + $("#city_").val()+"/";

		else if($("#region_").val()!="") query+= lang_var[8] + $("#region_").val()+"/";
		if($("#category").val()!="" && activador != 'Maincategory'){
			query+=$("#category").val();
		} 
		else if($("#Maincategory").val()!=""){
			query+=$("#Maincategory").val();
		} 
		

		if(query=="") query+= lang_var[6];

		if($("#keyword_search1").val() != ""){
			query+="?q="+$("#keyword_search1").val();
			location.href=query;
			return false;
		}else if($("#keyword_search2").val() != ""){
			query+="?q="+$("#keyword_search1").val();
			location.href=query;
			return false;
		}else{
			query+="?q=";
			location.href=query;
			return false;
		}
	*/

}

function filterSearch(){

	var uri = site_url;

	if($("#Maincategory").val() != "") uri += $("#Maincategory").val();

	if($("#region_").val() != "") uri += "-en-" + $("#region_").val();

	uri += "/";

	let query = {};
	if($("#keyword_search1").val() != "") query["q"] = $("#keyword_search1").val();
	if($("#keyword_search2").val() != "") query["q"] = $("#keyword_search2").val();
	
	if($("#dis").val() != "0") query["dis"] = $("#dis").val();
	const checkboxes = document.querySelectorAll('input[name="lang"]:checked');
	const values = Array.from(checkboxes).map(checkbox => checkbox.value);
            console.log(values);
	//if($("#lang").val() != "0") query["lang"] = $("#lang").val();
	if(values.length > 0) query["lang"] = values;
	if($("#out").val() != "0") query["out"] = $("#out").val();
	if($("#horario_inicio").val() != "") query["hor_start"] = $("#horario_inicio").val();
	if($("#horario_final").val() != "") query["hor_end"] = $("#horario_final").val();
	if(Object.keys(query).length > 0) uri += "?" + $.param(query);
	
	location.href=uri;

}

function addFav(id){

	jQuery.ajax({

		type: "GET",

		url: site_url + "sc-includes/php/ajax/add_fav.php",

		dataType: "text",

		data: {i: id},

		success: function (response) {
			updateFav(id,response);

		}

	});

}

function updateFav(id,res){

	if(res==1){
		 $("[data-id=fav-"+id+"]").addClass('on');
		 //$("#fav-"+id).addClass('fas');
		 //$("#fav-"+id).removeClass('far');

	}	
	if(res==2)
	{
		$("[data-id=fav-"+id+"]").removeClass('on');
		//$("#fav-"+id).addClass('far');
		//$("#fav-"+id).removeClass('fas');
	}

	jQuery.ajax({

		type: "GET",

		url: site_url + "sc-includes/php/ajax/up_fav.php",

		dataType: "text",

		data: {i: id},

		success: function (response) {

			$("#tot_fav").html(response);

		}

	});

}

function contact_web(){

	hiddenError()

	if($("#contact_name").val().length!=0){
		if($('#contact_sub').val().length!=0){
			if(validMail("#contact_mail")){

				if($('#mens').val().length>0){

					if(($('#cprivacidad').is(':checked')) == true)
						submitFormCaptchaContact();
					else
						$("#error_privacity").show();

				}else $("#error_mens").show();

			}else $("#error_contact_mail").show();

		}else $('#error_contact_sub').show();
	}else $("#error_contact_name").show();

}


function dataAccountUpdate(){

	hiddenError()	

	if($("#name_account").val().length>=4){

		if(validPhone("#phone_account")){

			if(validMail("#mail_account")){
				$("#data_account").submit();
			}else{
				$('#error_mail').show();
				scroll_To('mail_account');
			}

		}else{

			$("#error_phone").show();

			scroll_To('phone_account');

		}

	}else{

		$("#error_name").show();

		scroll_To('name_account');

	}

}

function passAccountUpdate(){

	hiddenError()

	// if($("#pass_current").val().length>=6){

	if($("#pass1_change").val().length>=6){

		if($("#pass2_change").val()==$("#pass1_change").val()){

			$("#pass_account").submit();

		}else $("#error_pass2_change").show();

	}else $("#error_pass1_change").show();

	// }else $("#error_pass_current").show();

}

function hiddenError(){

	$(".error_msg").hide();

}

function terms(){

	return $('#terminos').is(':checked');

}

function onlyNumber(evt){

	if(window.event){

		keynum=evt.keyCode;

	}else{

		keynum=evt.which;

	}

if(keynum>47&&keynum<58){

	return true;

	}else{

	return false;

	}

}

function phoneChars(evt){

	if(window.event){

		keynum=evt.keyCode;

	}else{

		keynum=evt.which;

	}

	if( (keynum>47&&keynum<58) || keynum==32  || keynum==40  || keynum==41){

		return true;

		}else{

		return false;

		}

	}

function req(val){

	if(val!="") return true;

	else return false;

}

function delFav(id)
{
	$.get(site_url + "sc-includes/php/ajax/add_fav.php", {i: id}, function(data)
	{
		if(data==1 || data==2)
		{
			window.location.reload();
		}	

	}, 'text');
}

function valSelect(val){

	if(val!="0") return true;

	else return false;

}

function textMaxLength(Object)

{return(Object.value.length<=1200);}

function validMail(element){

	var reg=/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

	if(reg.test($(element).val())) return true;

	else return false;

}

function validPhone(element){

	num=$(element).val();

	if(num.length>=6) return true;

	else return false;

}

function popupLogin(){

	$("#popup_overlay").fadeTo("fast", 0.8).fadeIn(100).css({width: $(document).width(),height: $(document).height()});
	$(".login_div").fadeTo("fast", 1).fadeIn(100).center();

}

function popupWindow(content){

	$("#popup_floating").load(content);

    $("#popup_overlay").fadeTo("fast", 0.8).fadeIn(100).css({width: $(document).width(),height: $(document).height()});

    $("#popup_floating").fadeTo("fast", 1).fadeIn(100).center();

}

function popupWindowHide(){

	$("#popup_floating").fadeOut("slow");

    $("#popup_overlay").fadeOut("slow");

	$("#popup_floating").html('');

}

jQuery.fn.center = function () {

    this.css("position","absolute");

    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + 

                                                $(window).scrollTop()) + "px");

    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + 

                                                $(window).scrollLeft()) + "px");

    return this;

} 

function getCookie(c_name){

	var c_value = document.cookie;

	var c_start = c_value.indexOf(" " + c_name + "=");

	if (c_start == -1){

	c_start = c_value.indexOf(c_name + "=");

	}

	if (c_start == -1){

	c_value = null;

	}else{

	c_start = c_value.indexOf("=", c_start) + 1;

	var c_end = c_value.indexOf(";", c_start);

	if (c_end == -1){

	c_end = c_value.length;

	}

	c_value = unescape(c_value.substring(c_start,c_end));

	}

	return c_value;

}

function setCookie(c_name,value,exdays){

	var exdate=new Date();

	exdate.setDate(exdate.getDate() + exdays);

	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());

	document.cookie=c_name + "=" + c_value + ";path=/";

}

function addCookie(){

	setCookie('cookie_advice','1',365);

	$(document).find(".config_cookies_content").hide();

}

function scroll_To(id){

	$("html, body").animate({ scrollTop: $("#"+id).offset().top-90 }, 500);

}


function validate_form(){

	hiddenError()

	sel=['category','region', 'ad_type'];

	error=false;

	extra_fields=['km_car', 'date_car', 'date_car', 'fuel_car', 'room', 'bathroom', 'area'];

	if($("#tit").hasClass('error'))
	{
		error=true;
		$("#error_tit1").show();
		scroll_To('tit');
		return false;
	}

	if($("#text").hasClass('error'))
	{
		error=true;
		$("#error_text1").show();
		scroll_To('text');
		return false;
	}

	for(var i=0; i< sel.length; i++){

		if(!valSelect($("#"+sel[i]).val())){

			error=true; 

			$("#error_"+sel[i]).show(); 

			scroll_To(sel[i]);

			return false;

		}else 
			$("#error_"+sel[i]).hide();

	}

	var tit=$("#tit").val();
	if(!req($("#tit").val()) || (tit.length<10 || tit.length>50)){

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


	var text=$("#text").val();
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

			scroll_To('text'); return false;

	}else $("#error_text").hide()

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
	

	/*if(!req($("#city").val())){

		error=true; $("#error_city").show(); 

			scroll_To('city'); return false;

	}else $("#error_city").hide()*/

	/*if(!req($("#precio").val())){

		error=true; $("#error_price").show(); 

			scroll_To('precio'); return false;

	}else $("#error_price").hide()*/

	/*if($("input[name='photo_name[]']").size() <= 0 && $("input[name='photo_name_current[]']").size() <= 0){

		error=true; $("#error_photo").show(); 

			scroll_To('title_photos_list'); return false;

	}else $("#error_photo").hide();*/

	/*if(!req($("#name").val())){

		error=true; $("#error_name").show(); 

			scroll_To('name'); return false;

	}else $("#error_name").hide()*/

	if($("#horario-final").val() == $("#horario-inicio").val()){

		error=true; 
		$("#error_horario").show(); 
		scroll_To('horario-final'); 
		return false;

	}else $("#error_horario").hide();

	if($("#dis").val() == 0){

		error=true; 
		$("#error_dis").show(); 
		scroll_To('dis'); 
		return false;

	}else $("#error_dis").hide();

	if(!checkImages())
	{
		$("#error_photo").show(); 
		return false;
	}else $("#error_photo").hide();

	if(!validMail("#email")){

		error=true; $("#error_email").show(); 

		return false;

	}else $("#error_email").hide()

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

	if(!error){
		if($("#new_order").val() != 0)
		{
			submitFormCaptcha();
		}else
		{
			checkLimits().then(function(){
				submitFormCaptcha();
			})
		}
	} 

}

function addStyle(style)
{
	const link = document.createElement('link');
	link.rel = 'stylesheet'; // Especificar que es una hoja de estilo
	link.type = 'text/css';
	link.href = `/src/css/${style}`; // Ruta de tu archivo CSS
	
	// Añadir el <link> al <head>
	document.head.appendChild(link);
}

function updateprice(){
	mprice0 = $('#Mprice_0');
	mprice1 = $('#Mprice_1');
	mprice0.change(function(){
		$('#price_0').val(mprice0.val());
	});
	mprice1.change(function(){
		$('#price_1').val(mprice1.val());
	});
}

function validate_form_edit(){

	hiddenError()
	

	sel=['region', 'ad_type', 'sellerType'];

	error=false;

	for(var i=0; i< sel.length; i++){

		if(!valSelect($("#"+sel[i]).val())){

			error=true; $("#error_"+sel[i]).show(); 

			scroll_To(sel[i]);

			return false;

		}else $("#error_"+sel[i]).hide()

	}

	var tit=$("#tit").val();
	if(!req($("#tit").val()) || (tit.length<10 || tit.length>50)){

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

		scroll_To('tit'); return false;

	}else $("#error_tit").hide()


	var text=$("#text").val();
	if(!req($("#text").val()) || (text.length<30 || text.length>500)){
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
		error=true; 

			scroll_To('text'); return false;

	}else $("#error_text").hide()

	/*if(!req($("#precio").val())){

		error=true; $("#error_price").show(); 

			scroll_To('precio'); return false;

	}else $("#error_price").hide()*/

	/*if($("input[name='photo_name[]']").size() <= 0 && $("input[name='photo_name_current[]']").size() <= 0){

		error=true; $("#error_photo").show(); 

			scroll_To('title_photos_list'); return false;

	}else $("#error_photo").hide();*/

	if(!req($("#name").val())){

		error=true; $("#error_name").show(); 

			scroll_To('name'); return false;

	}else $("#error_name").hide()

	if($("#dis").val() == 0){

		error=true; $("#error_dis").show(); 

			scroll_To('dis'); return false;

	}else $("#error_dis").hide();

	if(!checkImagesEdit())
	{
		$("#error_photo").show(); 
		scroll_To('error_photo');
		return false;
	}else $("#error_photo").hide();

	/*if(!terms()){

		error=true; $("#error_terminos").show(); 

			scroll_To('terminos'); return false;

	}else $("#error_terminos").hide()*/

	if(!error) $("#new_item_post").submit();

}



// IMAGES UPLOAD

var max_file_size=2; // Mb



function uploadPicturePost(id_photo){

	$("#error_photos").hide();

		if($(this).length>0){

			if($('input[id="photo-'+id_photo+'"]')[0].files[0].type.match('image/jpeg')

			|| $('input[id="photo-'+id_photo+'"]')[0].files[0].type.match('image/png') ||

			$('input[id="photo-'+id_photo+'"]')[0].files[0].type.match('image/gif')){

				if($('input[id="photo-'+id_photo+'"]')[0].files[0].size <= max_file_size * 1000 * 1000){

				$("#photo_container-" + id_photo).addClass('loading');

				$("#photo_container-" + id_photo).removeClass('free');

				$(this).css('visibility','hidden');

				var formData = new FormData();

				formData.append('userImage', $('input[id="photo-'+id_photo+'"]')[0].files[0]);
				$.ajax({

					url: site_url + "sc-includes/php/ajax/upload_picture.php",

					type: "POST",

					data:  formData,

					contentType: false,

					cache: false,

					processData:false,

					success: function(data_){

						$("#photo_container-" + id_photo).removeClass('loading');

						$("#photo_container-" + id_photo).html(data_);

						$("#photo_container-" + id_photo + " .edit-photo-icon")[0].onclick = function(){
							editImage(id_photo);
						};


						/*if($('.sortable.photos_list').length>0){
							console.log('entro');
							$( ".sortable.photos_list" ).sortable( "refresh" );
						}*/

					},

					timeout: 30000        

				});

				}else $("#error_photos").html(lang_var[3] + " " + max_file_size + " Mb").show();

			}else $("#error_photos").html(lang_var[4]).show();

		}
}

function mapa(provincia) {
	const region1 = $('#region_search1');
	const region2 = $("#region_search2");

	index = SearchByMap(region1, provincia);
	
	if(index !== false){
		unselected(region1);
		region1[0].children[index].selected = true;
	}else
		return false;

	$('#select2-region_search2-container').html( region1[0].children[index].innerText );

	index = SearchByMap(region2, provincia);
	
	if(index !== false){
		unselected(region2);
		region2[0].children[index].selected = true;
	}else
		return false;

	
}
function unselected(region){
	length = region[0].children.length;
	element = region[0].children;
	for(let i = 0; i < length; i++){
		if(element[i].selected === true)
			element[i].selected = false;
	}
}

function do_login()
{
	jQuery.ajax({

		type: "POST",

		url: site_url + "sc-includes/php/ajax/do_login.php",

		dataType: "text",

		data: {m: $("#mail_login").val(),p: $("#pass_login").val(), r: $('#do_remember').is(':checked')},

		success: function (response) {
		
		if(response!=1){
			console.log(response);
			$("#response_login").html(response).addClass('error_login_response');
			//location.href = site_url;
			return false;

		}else
			location.href = site_url + "mis-anuncios/?menuopen";

		}

	});
}

function SearchByMap(region, provincia){

	length = region[0].children.length;
	element = region[0].children;

	for(let i = 0; i < length; i++){
		if(element[i].value == provincia)
			return i;
	}

	return false;
	
}

function uploadPictureUser(){

	$("#error_photos").hide();

		if($(this).length>0){

			if($('input[id="userBanner"]')[0].files[0].type.match('image/jpeg')

			|| $('input[id="userBanner"]')[0].files[0].type.match('image/png') ||

			$('input[id="userBanner"]')[0].files[0].type.match('image/gif')){

				if($('input[id="userBanner"]')[0].files[0].size <= max_file_size * 1000 * 1000){

				$("#photo_banner_user").addClass('loading');

				$("#photo_banner_user").removeClass('free');

				$(this).css('visibility','hidden');

				var formData = new FormData();

				formData.append('userImage', $('input[id="userBanner"]')[0].files[0]);

				$.ajax({

					url: site_url + "sc-includes/php/ajax/upload_user_banner.php",

					type: "POST",

					data:  formData,

					contentType: false,

					cache: false,

					processData:false,

					success: function(data_){

						$("#photo_banner_user").removeClass('loading');

						$("#photo_banner_user").html(data_);

					},

					timeout: 30000        

				});

				}else $("#error_photos").html(lang_var[3] + max_file_size + " Mb").show();

			}else $("#error_photos").html(lang_var[4]).show();

		}

}

function cleanQuerySearch(val){

	return val.toLowerCase().replace('ñ','n').replace(/ /g,'-').replace(/[^\w-]+/g,'');

}

function readImg(file)
{
	return new Promise((resolve, reject) => {
		const reader = new FileReader();
		reader.onload = function() {
			resolve(reader.result);
		};
		reader.onerror = function() {
			reject(reader.error);
		};
		if (file && file.type.startsWith('image/')) {
			reader.readAsDataURL(file);
		}else{
			reject('No es un archivo de imagen');
		}
	});
}

function openMoreInfo(a)
{
	if($(".more-info").hasClass("active"))
	{
		$(".more-info").removeClass("active");
		$(a).html("Sobre nosotros <span>clica aquí</span>");
	}else
	{
		$(".more-info").addClass("active");
		$(a).html("Ocultar información");
	}

}

// Función para limpiar los parámetros de la URL
function cleanUrlParams() {
    // Obtener la URL actual
    let currentUrl = window.location.href;
    
    // Crear un objeto URL
    let url = new URL(currentUrl);
    
    // Obtener solo la parte del path sin los parámetros
    let cleanPath = url.origin + url.pathname;
    
    // Actualizar la URL en el navegador sin recargar la página
    window.history.replaceState({}, document.title, cleanPath);
}

function getNextPage(){
    const list = $('#list_items');
    search_params.pag++;
	if(search_params.pag == search_params.tot_pag)
	{
		$('#next_button').hide();
		$('#last_page_button').hide();
	}
	if(search_params.pag > 1)
	{
		$('#prev_button').show();
		$('#first_page_button').show();
	}
	$("#page_current").html(search_params.pag);

	scrollToTop();
	
    $.get(site_url + "/sc-includes/php/ajax/list_items.php", search_params,
        function (data, textStatus, jqXHR) {
            list.html(data);
        },
        "html"
    );
}
function scrollToTop(smooth = false)
{

	if(!smooth)
		window.scroll(0, 0);
	else
		$("body, html").animate({ scrollTop: 0 }, "slow");
}

function getPrevPage(){
    const list = $('#list_items');
    search_params.pag--;
	$('#next_button').show();
	$('#last_page_button').show();

	if(search_params.pag == 1)
	{
		$('#prev_button').hide();
		$('#first_page_button').hide();
	}

	$("#page_current").html(search_params.pag);

	scrollToTop();

    $.get(site_url + "/sc-includes/php/ajax/list_items.php", search_params,
        function (data, textStatus, jqXHR) {
            list.html(data);
        },
        "html"
    );
}
function showText(t)
{
	if($(t).parent().find('.short').hasClass('hidden'))
	{
		$(t).parent().find('.short').removeClass('hidden');
		$(t).parent().find('.open').addClass('hidden');
		t.innerHTML = 'ver más';
	}else
	{
		$(t).parent().find('.short').addClass('hidden');
		$(t).parent().find('.open').removeClass('hidden');
		t.innerHTML = 'ver menos';
	}
}
function getLastPage(){
    const list = $('#list_items');
    search_params.pag = search_params.tot_pag;
	$('#next_button').hide();
	$('#last_page_button').hide();


	$('#prev_button').show();
	$('#first_page_button').show();
	

	$("#page_current").html(search_params.pag);

	scrollToTop();
    $.get(site_url + "/sc-includes/php/ajax/list_items.php", search_params,
        function (data, textStatus, jqXHR) {
            list.html(data);
        },
        "html"
    );
}

function getFirstPage(){
    const list = $('#list_items');
    search_params.pag = 1;
	$('#next_button').show();
	$('#last_page_button').show();

	$('#prev_button').hide();
	$('#first_page_button').hide();

	$("#page_current").html(search_params.pag);

	scrollToTop();
    $.get(site_url + "/sc-includes/php/ajax/list_items.php", search_params,
        function (data, textStatus, jqXHR) {
            list.html(data);
        },
        "html"
    );
}
function loadCSS(url) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = url;
    link.type = 'text/css';
    link.media = 'all';

    document.head.appendChild(link);
}

function checkEvents(id_user)
{
    setInterval(function()
    {
        $.get(site_url + "sc-includes/php/ajax/check_user_event.php", {id: id_user}, function(data)
        {
            if(data.status == 1)
            {
               window.location.reload();
            }
        }, 'json');
    }, 5000);

}