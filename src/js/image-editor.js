var cropper;
var img_index = 0;
const editor_img = document.getElementById('editor_img');

function editImage(index)
{
    if(cropper)
        cropper.destroy();

    const img = $(`#photo_container-${index} img`)[0];
    $(editor_img).attr('src', img.src);
    img_index = index;

    cropper = new Cropper(editor_img, {
        viewMode: 1,
        aspectRatio: 11 / 13
    });

    $('#editor_modal').modal('show');
}

function saveImage()
{
    const canvas = cropper.getCroppedCanvas();
    const dataImagen = canvas.toDataURL('image/jpeg', 1);
    const data = {
        'image' : dataImagen,
        'name' : $(`#photo_container-${img_index} input`).val()
    }
    $.post(site_url + "sc-includes/php/ajax/save_image.php", data, function(data_){
        $("#photo_container-" + img_index).html(data_);
        $("#photo_container-" + img_index + " .edit-photo-icon")[0].onclick = function(){
            editImage(img_index);
        };
        $('#editor_modal').modal('hide');
        cropper.destroy();
        cropper = null;
        $("#imageEdited").val(1);
    });
    
}

