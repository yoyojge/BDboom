
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

function initTop(){

  bdbnavHeight = document.getElementById('bdbnav').offsetHeight;
  document.getElementById('myCarouselInner').style.marginTop = bdbnavHeight+'px';
  
  const carouselItem = document.querySelectorAll('.carousel-item');

  carouselItem.forEach(function(item) {
    if( window.innerWidth*0.52 < 512){
      item.style.height = (window.innerWidth*0.52)+'px';
    }
    else{
      item.style.height = '512px';
    }
  });

}

addEventListener('resize', (event) => {  
  initTop();
});

initTop();