
// let bdbnav = document.getElementById('bdbnav');

// bdbnav.addEventListener('click',(e)=>{
//   console.log(`bouton moins`);
//   if(solution - robotTry > 0){
//        arnaque();
//   }
//   else if(solution - robotTry === 0){
//        console.log('Le ROBOT a gagné !!! ')
//   }
//   else{
//        max =    robotTry;
//        numberTry--;
//        console.log(` decremente B`);
//   }          
//   letStart();
// });


let bdbnavHeight;
let maxcarouselItemHeight = 512;
let coefficient = 0.7;
// let maxcarouselItemHeight = 600;

function initTop(){
  console.log('initTop');
  bdbnavHeight = document.getElementById('bdbnav').offsetHeight;

  console.log( bdbnavHeight);
  document.getElementById('myCarouselInner').style.paddingTop = bdbnavHeight+'px';
  
  const carouselItem = document.querySelectorAll('.carousel-item');

  carouselItem.forEach(function(item) {
    if( window.innerWidth * coefficient < maxcarouselItemHeight){
      item.style.height = (window.innerWidth * coefficient)+'px';
      // item.style.height = '250px';
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
  console.log('DOM fully loaded and parsed hhhh');
  initTop();
});


