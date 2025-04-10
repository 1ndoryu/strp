const slist = document.querySelector("#catList");

$(document).ready(()=>{
    if(slist)
      updateIndex();

    $(slist).on("dragstart", "li", function(e){
        $(this).addClass("dragging");
    });

    $(slist).on("dragover", updateList);

    $(slist).on("dragenter", e => e.preventDefault());

    $(slist).on("dragend", "li", function(e){
        $(this).removeClass("dragging");
        restablecerPosiciones();
        updateIndex();
    });

    $('#image_cat_file').change(function(){
      const file = this.files[0];
      console.log(file);
      if (file){
        let reader = new FileReader();
        reader.onload = function(event){
          console.log(event.target.result);
          $('#image_cat_preview').attr('src', event.target.result);
        }
        reader.readAsDataURL(file);
      }
    });

    $('#image_icon_file').change(function(){
      const file = this.files[0];
      console.log(file);
      if (file){
        let reader = new FileReader();
        reader.onload = function(event){
          console.log(event.target.result);
          $('#image_icon_preview').attr('src', event.target.result);
        }
        reader.readAsDataURL(file);
      }
    });

    $("#save_order").click(() => {
        const items = slist.querySelectorAll('li');
        const data = {};
        items.forEach((item) => {
            data[item.dataset.id] = item.dataset.index;
        });

        $("#save_order_data").val(JSON.stringify(data));
        $("#save_order_form").submit();
    });

});

function updateIndex()
{
    slist.querySelectorAll("li").forEach(function(item, index){
        item.setAttribute("data-index", index);
    });
}

function updateList(e)
{
  e.preventDefault();
  const dragging = slist.querySelector(".dragging");
  let nextSibling = document.elementFromPoint(e.clientX, e.clientY);

  if(nextSibling.parentElement !== slist)
  {
    if(nextSibling.parentElement.parentElement === slist)
    {
      nextSibling = nextSibling.parentElement;
    }else if(nextSibling.parentElement.parentElement.parentElement === slist)
    {
      nextSibling = nextSibling.parentElement.parentElement;
    }else{
      return;
    }
  }

  if(nextSibling === dragging)
    return;

  console.log(e.clientY, ":", nextSibling);


  if(!isBefore(dragging, nextSibling))
  {

    moverElemento(nextSibling, -50);
    setTimeout(() => {
      //moverElemento(nextSibling, 0);
      restablecerMovimiento(nextSibling);
      slist.insertBefore(dragging, nextSibling.nextSibling);
    }, 300);
  }
  else
  {
    moverElemento(nextSibling, 50);
    setTimeout(() => {
      //moverElemento(nextSibling, 0);
      restablecerMovimiento(nextSibling);
      slist.insertBefore(dragging, nextSibling);
    }, 300);
  }

}


function isBefore(a, b)
{
  if(a.parentElement == b.parentElement)
  {
    for (let cur = a.previousSibling; cur; cur = cur.previousSibling) 
    {
      if (cur === b) return true;
    }
  }

  return false
}

// Función para mover un elemento en la lista
function moverElemento(elemento, desplazamientoY) {
  // Aplica una transformación para mover el elemento suavemente
  elemento.style.transform = `translate(0, ${desplazamientoY}px)`;
}

function restablecerMovimiento(elemento) {
  // Aplica una transformación para mover el elemento suavemente
  elemento.style.transition = "none";
  elemento.style.transform = `translate(0, 0)`;
  setTimeout(() => {
    elemento.style.transition = "";
  }, 100);
}
  
  // Restablece la posición de todos los elementos cuando se completa el arrastre
function restablecerPosiciones() {
  const items = slist.querySelectorAll('li');
  items.forEach((item) => {
    item.style.transform = 'translate(0, 0)';
  });
}

function addFilter()
{
    $("#filter_modal").modal("show");
}

function editFilter(e)
{
    const data = $(e).data();
    let cats = [];
    if(Array.isArray(data.cats))
      cats = [`${data.cats[0]}`];
    else
      cats = data.cats.split(',').map(cat => cat.substring(1, cat.length - 1));
    $("#filter_name").val(data.name);
    $("#filter_words").val(data.word);
    $("#filter_id").val(data.id);
    $("#filter_category input").each(function(i, input){
      if(cats.includes(input.value))
        input.checked = true;
    });
    $("#filter_modal").modal("show");
}