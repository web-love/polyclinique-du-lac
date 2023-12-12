FIXHEIGHT = (function(){
	var resMax = 0
	  , expDirect = "top" 
	  , childHeight = []
	  , highestChild = 0
	  , childNum = 0
	  , childItem = $('['+ kitAttr +'fixHeight="child"]')
	  , parentItem = $('['+ kitAttr +'fixHeight="parent"]');
	
	var resizeTimeout = function(){
		var rtime; 
		var timeout = false; 
		var delta = 100;
		
		childItem.css({height: "auto"});
		rtime = new Date();
		if (timeout === false) {
				timeout = true;
				setTimeout(resizeend, delta);
		}

		function resizeend() {
			if (new Date() - rtime < delta) {
					setTimeout(resizeend, delta);

			} else {
					timeout = false;
					run();
			}               
		}
	};
	
	var run = function(){
		resMax = 0;	
		expDirect = "top"; 
		childHeight = [];
		highestChild = 0;
		childNum = 0;

		parentItem.each(function(i, obj){

			$(this).find('['+ kitAttr +'fixHeight="child"]').each(function(i, obj){
				childHeight.push($(this).height());
				childNum ++
			});
			highestChild = (Math.max.apply(Math, childHeight));

			$(this).find('['+ kitAttr +'fixHeight="child"]').each(function(i, obj){				
			
				resMax = $(this).attr(''+ kitAttr +'fixheightstopat');
				expDirect = ($(this).attr(''+ kitAttr +'fixheightorient')) ? $(this).attr(''+ kitAttr +'fixheightorient') : 'top';

				if ($(window).width() > resMax){
					checkHeight = $(this).height();
					if (checkHeight <= highestChild){
						if (expDirect == 'top') $(this).css({height: highestChild});
						if (expDirect == 'bottom') $(this).css({paddingTop: highestChild - checkHeight});
						if (expDirect == 'center') $(this).css({paddingTop: ((highestChild - checkHeight)/2), paddingBottom: ((highestChild - checkHeight)/2) });
					}
				} else {
					$(this).css({paddingBottom: '', paddingTop:'', height:'' });
				}
				resMax = 0;
			});

			childHeight = [];
			highestChild = 0;
			childNum = 0;     

		});
	};
	
	
	return{
		init: function(){
			$(window).resize(resizeTimeout);
			$(window).load(run);
		}
	};
	
	
})();
