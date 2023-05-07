
let bdbnavHeight;
let maxcarouselItemHeight = 512;
//ratio hauteur/largeur
let coefficient = 0.6;

function initTop(){
  console.log('initTop2');
  bdbnavHeight = document.getElementById('bdbnav').offsetHeight;

  console.log( bdbnavHeight);
  document.getElementById('myCarouselInner').style.paddingTop = bdbnavHeight+'px';
  
  const carouselItem = document.querySelectorAll('.carousel-item');

  carouselItem.forEach(function(item) {
    if( window.innerWidth * coefficient < maxcarouselItemHeight){
      item.style.height = (window.innerWidth * coefficient)+'px';
    }
    else{
      item.style.height = maxcarouselItemHeight+'px';
    }
  });

}

addEventListener('resize', (event) => {  

  initTop();
});

window.addEventListener('load', (event) => {
  console.log('DOM fully loaded and parsed');
  initTop();
});


