jQuery(document).ready(function() {
   
  var banner = jQuery('#banner');
  
  if (banner.length > 0) 
  {
    banner.slides({
			preload: true,
			play: 5000,
			pause: 2500,
			hoverPause: true
		});
  };
  
  var carousel = jQuery('#carousel');
  
  if (carousel.length > 0) 
  {
    carousel.slides({
			preload: true,
			generateNextPrev: true,
			pagination: false
		});
  };

	initstuff()
   
  
});



function initstuff()
{
  
  var supports_canvas = jQuery('html.canvas');

  if (supports_canvas)
  {
    var carousel_images = jQuery("#carousel img");

    // Fade in images so there isn't a color "pop" document load and then on window load
    carousel_images.animate({"opacity": ".5"}, 1000)

     // clone image
        // carousel_images.each(function(){     
        //   var el = jQuery(this);
        //   el.css({"position":"absolute"})
        //     .wrap("<div class='img_wrapper' style='display: inline-block'>")
        //     .clone().addClass('img_grayscale')
        //     .css({"position":"absolute","z-index":"998","opacity":"0"})
        //     .insertBefore(el)
        //     .queue(function(){
        //       var el = jQuery(this);
        //       el.parent().css({"width":this.width,"height":this.height});
        //       el.dequeue();
        //     });
        //     this.src = grayscale(this.src);
        //   });
    
       // Fade image 
       carousel_images.mouseover(function(){
         jQuery(this).parent().find('img:first').stop().animate({opacity:1}, 300);
       });
    
       carousel_images.mouseout(function(){
         jQuery(this).stop().animate({opacity:.75}, 300);
       });   

  }
  
  // Setup the category Menu
  var cm = jQuery('#categories');
  
  if (cm.length > 0) 
  {
    setup_category_menu(cm);
  };

}

/*
  This is  a horribly designed fucntion
  but I have lack of sleep and little time :P 
  
  Ill clean it up later :D
*/
function setup_category_menu(el)
{
  var $ = jQuery;
  
  // main button you mouse on
  var mb = el.find('li:first');
  var li = el.find('ul > li > ul > li');
  var ul = el.find('ul li ul');
  
  ul.css('display', 'block !important');
  
  // Get width of originals
  var o = Array();
  
  li.each(function(i, el) {
    o[i] = $(el).width();
    $(this).css({width: 0});
  });
  
  el.find('ul > li > ul').css('width', o[0] + o[1] + 10);
  
  ul.css('display', 'none !important').hide();
  
  mb.hover(function() {    
    ul.css('display', 'block !important');
    li.each(function() {
      $(this).stop().animate({width: o[0]});
    });
    
  }, function(){

    ul.fadeOut();
    li.each(function() {
      $(this).stop().animate({width: 0});
    });
    ul.css('display', 'none !important');
    
  });
  
}


// Grayscale w canvas method
function grayscale(src){
  var canvas = document.createElement('canvas');
  var ctx = canvas.getContext('2d');
  var imgObj = new Image();
  imgObj.src = src;
  canvas.width = imgObj.width;
  canvas.height = imgObj.height; 
  ctx.drawImage(imgObj, 0, 0); 
  

  var imgPixels = ctx.getImageData(0, 0, canvas.width, canvas.height);
  
  for(var y = 0; y < imgPixels.height; y++){
    for(var x = 0; x < imgPixels.width; x++){
      var i = (y * 4) * imgPixels.width + x * 4;
      var avg = (imgPixels.data[i] + imgPixels.data[i + 1] + imgPixels.data[i + 2]) / 3;
      imgPixels.data[i] = avg; 
      imgPixels.data[i + 1] = avg; 
      imgPixels.data[i + 2] = avg;
    }
  }
  ctx.putImageData(imgPixels, 0, 0, 0, 0, imgPixels.width, imgPixels.height);
  return canvas.toDataURL();
}



