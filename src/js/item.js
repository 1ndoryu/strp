$(document).ready(function(){
    $("#text_more").click(function(){
        if($("#text_long").is(":visible")){
            $("#text_long").hide();
            $("#text_short").show();
            $("#text_more").text("mostrar más");
        }else{
            $("#text_long").show();
            $("#text_short").hide();
            $("#text_more").text("mostrar menos");
        }

    });

});



import PhotoSwipeLightbox from '/src/js/photoswipe-lightbox.esm.min.js'
const lightbox = new PhotoSwipeLightbox({
    // may select multiple "galleries"
    gallery: '.item-lightbox',
  
    // Elements within gallery (slides)
    children: 'a',
  
    // setup PhotoSwipe Core dynamic import
    pswpModule: () => import('/src/js/photoswipe.esm.min.js')
});

lightbox.init();

