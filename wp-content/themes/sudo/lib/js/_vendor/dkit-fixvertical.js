FIXVERTICAL = (function(){
	
	var parentNumber = 0 
	  , childNumber = 0
	  , parentArray = []
	  , childArray = []
	  , parentItem = $('['+ kitAttr +'vertical="parent"]')
	  , childItem = $('['+ kitAttr +'vertical="child"]');
	
	var run = function(){
		parentNumber = 0; 
		childNumber = 0;
		parentArray = [];
		childArray = [];
		
		parentItem.each(function(i, obj) {
			parentArray.push($(this).height());
			parentNumber = parentNumber + 1;
		});
		
		childItem.each(function(i, obj) {
			var maxCenter = $(this).attr(''+ kitAttr +'verticalstopat');
			var windowWidth = $(window).width();

			if (maxCenter != null && maxCenter <= windowWidth){
				setVertical($(this));
			} else if (maxCenter == null) {
				setVertical($(this));
			} else {
				resetVertical($(this));
			}
		});
	};
	
	var resetVertical = function(target){
		childItem.each(function(i, obj) {
			$(target).css( {'margin-top' : 0 } );
		});
	};
	
	var setVertical = function(target){
		childArray.push($(target).height());
		$(target).css( {'margin-top' : ( parentArray[childNumber] / 2 ) - ( childArray[childNumber] / 2 ) } );
		childNumber = childNumber + 1;
	}
	
	return{
		init: function(){
			$(window).load(function(){
				run();
			});

			$(window).on("resize", function(){
				run();
			});
		}
	};
	
})();

