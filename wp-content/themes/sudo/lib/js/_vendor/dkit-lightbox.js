LIGHTBOX = (function(){   
  	
	var item = $('['+ kitAttr +'lightbox^="child"]')
	  , Gallery_images = {}
	  , Total_img = 0
	  , lastHeight = 0
      , newHeight = 0
      , lastWidth = 0
      , newWidth = 0  
      , maxHeight = 0
      , resizedHeight = 0
      , heightPadding = 140 // Change the padding for top and bottom
      , isTouch = false
      , mobileDescOpen = false
      , current = 0
      , firstLoad = true;
	
	function ImageItem(url, desc, title) {
		this.url = url;
		this.desc = desc;
		this.title = title;
	}
	
	//{
	var lightboxHtml = '<div class="Lightbox">'+
    
						'<div class="Lightbox-mobile_info">'+
						  '<div class="mobile_info" '+ kitAttr +'grid="col-12">'+
							'<span class="mobile_title"></span>'+
							'<span class="mobile_desc"></span>'+
						  '</div>'+
						'</div>'+


						 '<div class="Lightbox-close"><img src="'+_ROOT+'/lib/ressources/lightbox/lightbox-close.svg" onerror="'+_ROOT+'/lib/ressources/lightbox/lightbox-close.png"></div>'+
						 '<div class="Lightbox-num"></div>'+
						  '<div class="Lightbox-wrapper" '+ kitAttr +'grid="grid-wrapper">'+
							'<div class="Lightbox-view" '+ kitAttr +'grid="col-12">'+

							  '<div class="Lightbox-info">'+
								'<div class="info_wrapper">'+
								  '<span class="info_title"></span><span class="info_desc"></span>'+
								'</div>'+
							  '</div>'+

							  '<div class="Lightbox-back"><img src="'+_ROOT+'/lib/ressources/lightbox/lightbox-back.svg" onerror="'+_ROOT+'/lib/ressources/lightbox/lightbox-back.png"></div>'+
							  '<div class="Lightbox-next"><img src="'+_ROOT+'/lib/ressources/lightbox/lightbox-next.svg" onerror="'+_ROOT+'/lib/ressources/lightbox/lightbox-next.png"></div>'+
							  '<div class="Lightbox-image"></div>'+
							'</div>'+
						  '</div>'+

						  '<div class="Lightbox-mobile" '+ kitAttr +'grid="col-12">'+
							'<div class="Lightbox-nav_mobile" '+ kitAttr +'grid="grid-wrapper">'+
							  '<div class="Lightbox-back_mobile" '+ kitAttr +'grid="col-2"><img src="'+_ROOT+'/lib/ressources/lightbox/lightbox-back.svg" onerror="'+_ROOT+'/lib/ressources/lightbox/lightbox-back.png"></div>'+
							  '<div class="Lightbox-menu" '+ kitAttr +'grid="col-8"><span id="Lightbox-descBtn" class="menu-title">Description <img class="desc" src="'+_ROOT+'/lib/ressources/lightbox/lightbox-desc.svg" onerror="'+_ROOT+'/lib/ressources/lightbox/lightbox-desc.png"></span></div>'+
							  '<div class="Lightbox-next_mobile" '+ kitAttr +'grid="col-2"><img src="'+_ROOT+'/lib/ressources/lightbox/lightbox-next.svg" onerror="'+_ROOT+'/lib/ressources/lightbox/lightbox-next.png"></div>'+
							'</div>'+
						  '</div>'+

						'</div>'
	
//}
	
	var initialize = function(){
	  
	  	// Add Lightbox HTML to page
		$('body').prepend(lightboxHtml);

		// Get clicked element data
		current = $(this).attr(''+ kitAttr +'lightboxPos');
		currentImg = $(this).attr(''+ kitAttr +'lightboxUrl');

		// Bind vars to UI elements
		$lightBox = $('.Lightbox');
		$lightBoxView = $('.Lightbox-view');
		$lightBoxNav = $('.Lightbox-back, .Lightbox-next');
		$lightBoxMobileNav = $('.Lightbox-mobile');
		$lightBoxNumDisplay = $('.Lightbox-num');
		$lightBoxCloseBtn = $('.Lightbox-close');
	  	$lightBoxImg = $('.Lightbox-image');
	  	$lightBoxTitle = $('.info_title, .mobile_title');
	  	$lightBoxDesc = $('.info_desc, .mobile_desc');
	  
		// Initialize event listeners
		next(); back(); keyNav(); screenResize();
		$lightBoxCloseBtn.on("click", close);
     
		// If click is outside bounds, close lightbox
		$lightBox.click(close).children().click(function(e) {
			return false;
		});
	  
	  	// Set the current image number ex:(1 of 8)
	  	$lightBoxNumDisplay.text((parseInt(current) + 1) + ' de ' + Total_img);
	  
	  	// Set the current image inside the view
	  	$lightBoxImg.append('<img class="image" src="'+ currentImg +'">');
	  
	  	// Set current text for image
		$lightBoxDesc.text($(this).attr(''+ kitAttr +'-lightboxDesc'));
		$lightBoxTitle.text($(this).attr(''+ kitAttr +'-lightboxTitle'));

	  	// Set body to be unscrollable
	  	$('body').css({overflow:"hidden"});
	  
	  	// Open animation
	  	open();
	  
	};
	
	var open = function(){

		$lightBox.fadeIn(300);

		$lightBoxView.find('.image').load(function(){ 
			checkViewHeight();
			centerView();
			navigationDisplay();
			descriptionDisplay();
			mobileDescriptionDisplay();
			checkEmptyDescription();
			$lightBoxView.css({opacity: 1});
			$lightBox.css({backgroundImage: "none"})
			$(this).animate({opacity: 1}, 200);
			firstLoad = false;
			$lightBoxView.find('.image').unbind('load');
		});
		
	};
  
	var navigationDisplay = function(){ 
		
		if ($(window).width() <= 1025 || LIGHTBOX.isTouch){
			$lightBoxNav.css({display: "none"});
			$lightBoxMobileNav.css({display: "block"});
		} else {
			$lightBoxMobileNav.css({display: "none"});
			view_height = $lightBoxView.height()/2;
			arrow_height = $('.Lightbox-back').outerHeight()/2;
			margin_top = view_height - arrow_height;
			$lightBoxNav.css({top: margin_top});
			$lightBoxNav.delay(300).css({display: "block"});
		}
		
	};
  
	var descriptionDisplay = function(){
		
		if ($(window).width() <= 1025 || LIGHTBOX.isTouch){
			$('.Lightbox-info').css({display:"none"});
		} else {
			$('.Lightbox-info').css({display:"block"});
		}

	};
  
	var centerView = function(){
		window_height = $(window).height()/2;
		view_height = $lightBoxView.height()/2;
		view_top = window_height - view_height;
		$('.Lightbox-wrapper').css({marginTop: view_top});
	}
  
	var checkViewHeight = function(){
		
		window_height = ($(window).height())-heightPadding;
		view_height = $lightBoxView.height();
		img_width = $lightBoxView.find('.image').width();
		$lightBoxView.find('img').css({maxHeight: window_height});
		img_width = $lightBoxView.find('.image').width();
		$('.Lightbox-wrapper').css({maxWidth: img_width}); 
		
	};
  
	var screenResize = function(){
		lastHeight = $(window).height();
		lastWidth = $(window).width();

		var resizeId;
		$(window).resize(function() { 
			clearTimeout(resizeId);
			resizeId = setTimeout(doneResizing, 100);
		});

		function doneResizing(){
			newHeight = $(window).height();
			newWidtht = $(window).width();

			if (newHeight > lastHeight){
				// bigger in height
				window_height = $(window).height()-heightPadding;
				difference = maxHeight - resizedHeight;
				$('.Lightbox-wrapper').css({maxWidth: "100%"});
				$lightBoxView.find('img').css({maxHeight: window_height});
				img_width = $lightBoxView.find('.image').width();
				$('.Lightbox-wrapper').css({maxWidth: img_width});
				centerView();
				navigationDisplay();
				descriptionDisplay();
				lastHeight = newHeight; 
			} else {
				// smaller in height
				checkViewHeight();
				centerView();
				navigationDisplay();
				descriptionDisplay();
				lastHeight = newHeight;
			}
		}
		
	};
  
	var mobileDescriptionDisplay = function(){
		
		$('#Lightbox-descBtn').on("click", function(){
			if (mobileDescOpen == false){
				$lightBoxMobileNav.css({backgroundColor: "rgba(0, 0, 0, 0.75)"});
				$('.Lightbox-mobile_info').fadeIn();
				setImgRoot();
				mobileDescOpen = true;
			} else {
				setImgRoot();
				$('.Lightbox-mobile_info').fadeOut(function(){
					$lightBoxMobileNav.css({backgroundColor: "transparent"},200);
				});
				mobileDescOpen = false;
			}
		}); 
		
	  	var setImgRoot = function(){
			$('.menu-title').find('img').attr('src', ''+_ROOT+'/lib/ressources/lightbox/lightbox-desc.svg');
			$('.menu-title').find('img').attr('onerror', ''+_ROOT+'/lib/ressources/lightbox/lightbox-desc.png');
		};
		
	};
  
	var checkEmptyDescription = function(){
		
		if ((Gallery_images[current].desc == undefined) 
		&& (Gallery_images[current].title == undefined))
		{
			$('.Lightbox-info').css({display: 'none'});
		} 
		else 
		{
			$('.Lightbox-info').css({display: 'block'});
		}

		if ((Gallery_images[current].desc == undefined) 
		&& (Gallery_images[current].title == undefined) 
		&& (mobileDescOpen == false))
		{
			$('.menu-title').css({opacity: 0.2, pointerEvents: 'none'});
		} 
		else if ((Gallery_images[current].desc == undefined) 
		&& (Gallery_images[current].title == undefined) 
		&& (mobileDescOpen == true))
		{
			$('.mobile_desc').text('Aucune description disponible pour cette photo...');
		} 
		else 
		{
			$('.menu-title').css({opacity: 1, pointerEvents: 'all'});
		}
		
	};
  
	var next = function(){
		$('.Lightbox-next, .Lightbox-next_mobile').click(function(){

		if(current < (Total_img)-1){
			current ++;
			changeImage();
		} else {
			current = 0;
			changeImage();
		}

		});
	};
  
	var back = function(){
		$('.Lightbox-back, .Lightbox-back_mobile').click(function(){

		if(current == 0) {
			current = Total_img-1;
			changeImage();
		} else {
			current--;
			changeImage();
		}

		});
	};
  
	var changeImage = function(){
		
		$lightBoxImg.animate({opacity: 0},200, function(){
			$(this).find('.image').attr('src', Gallery_images[current].url);
			$(this).find('.image').load(function(){
				$lightBoxView.find('img').css({maxHeight: "100%"});
				$('.Lightbox-wrapper').css({maxWidth: "100%"});
				$lightBoxDesc.empty();
				$lightBoxTitle.empty();
				checkViewHeight();
				centerView();
				navigationDisplay();
				checkEmptyDescription();
				$lightBoxDesc.text(Gallery_images[current].desc);
				$lightBoxTitle.text(Gallery_images[current].title);
				$lightBoxNumDisplay.text((parseInt(current) + 1) + ' de ' + Total_img);
				$lightBoxImg.animate({opacity:1}, 200);
				$lightBoxView.find('.image').unbind('load');
			});
		});
		
	};
  
	var close = function(){
		
		$lightBox.animate({opacity: 0},400, function(){
		$lightBox.remove();
		$('body').css({overflow:"auto"});
		firstLoad == false;
		$(document).unbind('keydown');
		});
		
	};
  
	var keyNav = function(){
		$(document).keydown(function(e) {
			switch(e.which) {
					
				case 37: // left
				if(current == 0) {
					current = Total_img-1;
					changeImage();
				} else {
					current--;
					changeImage();
				} 
				break;

					
				case 39: // right
				if(current < (Total_img)-1){
					current ++;
					changeImage();
				} else {
					current = 0;
					changeImage();
				}
				break;

					
				case 27: // esc
					close();
				break;

				default: return; // exit this handler for other keys
			}
			
		e.preventDefault(); // prevent the default action (scroll / move caret)
			
		});
		
	};
	  
  	return {
	  
		init: function(){

			// Check if device is touchscreen
			LIGHTBOX.isTouch = (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0));

			item.each(function(i){
				$(this).attr(''+ kitAttr +'lightboxPos', i);
				temp_url = $(this).attr(''+ kitAttr +'lightboxurl');
				temp_desc = $(this).attr(''+ kitAttr +'lightboxdesc');
				temp_title = $(this).attr(''+ kitAttr +'lightboxtitle');
				Gallery_images[i] = new ImageItem(temp_url, temp_desc, temp_title);
				Total_img++
			});
			
			// Open the lightbox animations
			item.on("click", initialize);
		
		},
	  
		ajaxInit: function(){
			$('['+ kitAttr +'lightboxPos]').removeAttr(''+ kitAttr +'lightboxPos');
			item.unbind("click", open);
			item.on("click", open);

			item.each(function(i){
				$(this).attr(''+ kitAttr +'lightboxPos', i);
				temp_url = $(this).attr(''+ kitAttr +'lightboxurl');
				temp_desc = $(this).attr(''+ kitAttr +'lightboxdesc');
				temp_title = $(this).attr(''+ kitAttr +'lightboxtitle');
				Gallery_images[i] = new ImageItem(temp_url, temp_desc, temp_title);
				Total_img++
			});
		}  
	  
	  };  
	  
})();