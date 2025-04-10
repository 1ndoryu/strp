$(document).ready(function () {
    $('#search_ad').keyup(function(){
        if(this.value != '')
            $.get("inc/ajax/search_ads.php", {s : this.value},
            function (data, textStatus, jqXHR) {
                    console.log(data);
                    const options = $('.search_options');
                    options.html('');

                    data.forEach(element => {
                        const option = document.createElement('div');
                        const st = document.createElement('strong');
                        st.innerText = "ID: " + element.ID_ad;
                        const sp = document.createElement('span');
                        sp.innerText = " | " + element.title + " | " + element.cat;
                        option.append(st);
                        option.append(sp);
                        option.dataset["idad"] = element.ID_ad;
                        option.dataset['cat'] = element.parent_cat;
                        option.dataset['title'] = element.title;

                        
                            options[0].append(option);
                    });

                    options.show();

                }, 'json');
        else
            $('.search_options').hide();
    }); 

    $("#header_banner").change(function(){
        if(this.files.length == 0)
            return false;
        var data = new FormData();
        data.append("banner", this.files[0]);
        const img = this.parentNode.children[0];
        $.ajax({
            url: "inc/ajax/upload_header_banner.php",
            type: "POST",
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data){
                console.log(data);
                img.src = data.url + "?t=" + (new Date()).getTime();
            }
        });
    });
    $("#header_banner_r").change(function(){
        if(this.files.length == 0)
            return false;
        var data = new FormData();
        data.append("banner", this.files[0]);
        data.append("responsive", true);
        const img = this.parentNode.children[0];
        $.ajax({
            url: "inc/ajax/upload_header_banner.php",
            type: "POST",
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data){
                console.log(data);
                img.src = data.url + "?t=" + (new Date()).getTime();
            }
        });
    });
    
    $(document).on('change', '#banner_img', function()
    {
        const archivo = this.files[0];
        readImg(archivo).then(function(result)
        {
            $("#banner_preview img").attr("src", result);
            $("#banner_preview").show();
            $("#pay-premium").hide();
        });
    });

    $(document).on('click','.search_options div', function(){
        const data = $(this).data();
        $('#category').val(data.cat);
        $('#idad').val(data.idad);
        $('#search_ad').val(this.innerText);
        $('.search_options').hide();
    });

    $('#banenr_type').change(function(){
        if($(this).val() == '1'){
           $("#new_duration").hide();
           $("#new_ad").hide();
           $("#new_category").show();
        }else{
            $("#new_duration").show();
            $("#new_ad").show();
            $("#new_category").hide();
        }
    });
 
});


function setExtender(banner){
    $("#modal-extender").modal('show');
    $("#form-extender-banner").val(banner);
}

function updateHeaderBanner()
{
    $(".img-input-box img").each(function(i, img){
        img.src = img.src.split('?t=')[0] + "?t=" + (new Date()).getTime();
    });
}