import PhotoSwipeLightbox from '/src/js/photoswipe-lightbox.esm.min.js'
const lightbox = new PhotoSwipeLightbox({
    // may select multiple "galleries"
    gallery: '.min-img-item',
  
    // Elements within gallery (slides)
    children: 'a',
  
    // setup PhotoSwipe Core dynamic import
    pswpModule: () => import('/src/js/photoswipe.esm.min.js')
});

lightbox.init();
