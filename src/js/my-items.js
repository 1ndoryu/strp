$(document).ready(function()
{
    cleanUrlParams();

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

    $(".splide_my_item").each(function()
    {

        const slider = new Splide(this, {
            type: 'loop',
            perPage: 1,
            pagination: false,
            perMove: 1,
            arrows: true,
            autoplay: true,
            interval: 2500,
            drag: "free",
            rewind: true,
            pauseOnFocus: false,
            pauseOnHover: false,
            snap: true,
            lazyLoad: 'sequential',
        });
        
        slider.mount();
        
    });
});

function deleteDiscardMsg(id, e)
{
    //$(e).parent().remove();
    $.get("sc-includes/php/ajax/delete_discard_msg.php", {id}, function(data)
    {
        location.reload();
    });
}

function renovate(id, premium = false, hours = 24)
{
    $.get("sc-includes/php/ajax/renovate.php", {id, premium}, 
        function(data)
        {
            if(data.status)
            {
                if(data.data == 1)
                {
                    Payment.successMsg("Anuncio renovado correctamente");
                    
                }
                if(data.data == 2)
                {
                    if(hours != 24)
                        $("#limits_text").html(`Faltan ${hours} horas para poder renovar.`);
                    else
                        $("#limits_text").html("Aún no se puede renovar.");

                    $("#dialog_limits").attr("open", true);
                    $("#limits_payment_buttom")[0].onclick = () =>
                    {
                        $("#dialog_limits").attr("open", false);
                        openPayment(id);
                    }

                    $("#limits_ren_premium")[0].onclick = () =>
                    {
                        renovate(id, true);
                    }
                    
                }

                if(data.data == 3)
                {
                    Payment.errorMsg("Créditos insuficientes");
                }
            }
        },
        "json"
    );
}

function ActivateExtra(id)
{
    $.get(site_url + "sc-includes/php/ajax/activate_extra.php", {id}, function(data)    
    {
        if(data.status == 1)
        {
            Payment.successMsg("Anuncio activado correctamente");
        }
        else
        {
            Payment.errorMsg("Error al activar anuncio");
        }
    }, 'json');
}

function setDeleteAd(id)
{
    $("#delete-ad-btn")[0].onclick = () =>
    {
        location.href= site_url + lang_var[10] + "?d="+id;
    }
    $("#delete-ad").modal("show");
}

function deletePending(id)
{
    $.get("sc-includes/php/ajax/del_pending.ajax.php", {id}, function(data)
    {
        if(data.status == 1)
        {
            Payment.successMsg("Pedido eliminado correctamente");
        }
        else
        {
            $(".delete-pending-container").hide();
            closePayment();
            alert("Error al eliminar el pedido");
        }
    }, 'json');
}

function getSelectedItems()
{
    const data = $("#form_my_item").serializeArray();
    let selected = [];
    for(let i = 0; i < data.length; i++)
    {
        if(data[i].value)
        {
            selected.push(data[i].value);
        }
    }
    return selected;
}

