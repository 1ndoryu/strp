$(document).ready(function(){

    glide = new Splide(document.querySelectorAll(".splide")[0], {
      type: 'loop',
      perPage: 5,
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
      breakpoints: {
        850: {
          arrows: false,
          perPage: 3
        }
      }
    });

    if(premium_count > 4 || window.innerWidth < 768){
      glide.mount();
      $('.splide__slide > div').addClass('w-100');
    }
});