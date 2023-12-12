MASONRY = (function(){
	
	// Selectors
	var parent = $('['+ kitAttr +'masonry="parent"]')
	, child = $('['+ kitAttr +'masonry^="child"]')
	, gridWidth = parent.width()

	// Evaluation tables
	, itemsWidth = []
	, itemsHeight = []
	, colTable = []
	, colTableAdd = []
 
	// User values
	, itemNum, gutterWidth, colLeftHeight, colRightHeight
	, maxWidth, colNum, childNum, resColNum, resWidth
	, rowNum, parentHeight;
	
	
	var resetValues = function(){
		
		gridWidth = parent.width();

		itemsWidth = [];
		itemsHeight = [];
		colTable = [];
		colTableAdd = [];

		itemNum = gutterWidth = colLeftHeight = colRightHeight = 
		maxWidth = colNum = childNum = resColNum = resWidth = 
		rowNum = parentHeight = 0;
		
		getValues(); // getValues
		
	}
	
	var getValues = function(){
		
		colNum = parent.attr(''+ kitAttr +'masonrynumcol');
		gutterWidth = parent.attr(''+ kitAttr +'masonrygutter');
		maxWidth = parent.attr(''+ kitAttr +'masonrystopat');
		resWidth = parent.attr(''+ kitAttr +'masonryrespat');
		resColNum = parent.attr(''+ kitAttr +'masonryrespnumcol');
		
		evaluateWidths();
	}
	
	var evaluateWidths = function(){
		
		if (($( window ).width() >= maxWidth) && ($( window ).width() >= resWidth )){
			makeGrid(colNum);

		} else if (($( window ).width() <= maxWidth) && ($( window ).width() <= resWidth ))  {
			parent.css({
				height: "auto",
				width: "auto"
			});

			child.css({
				width: "100%",
				left: "0",
				top: "0",
				position: "relative",
				opacity: "1"
			});
		} else {
			makeGrid(resColNum);
		}
		
	}

	var makeGrid = function(colnumber){
		// Set the number of required rows in array
		for(var i = 0; i < colnumber; i++){
			colTableAdd.push(0);
		}

		child.css({position: "absolute"});
		child.each(function(i,obj){
			itemWidth = ( ( (gridWidth) - ((colnumber-1) * gutterWidth) ) /colnumber);
			$(this).css("width", itemWidth);
			$(this).css("top", colTableAdd[childNum] + ( rowNum * gutterWidth ) );
			$(this).css("left", ( (itemWidth * childNum) + (gutterWidth * childNum) ) );		
			colTable[childNum] = ($(this).outerHeight());
			colTableAdd[childNum] = (colTableAdd[childNum] + colTable[childNum]);
			childNum ++;
			if (childNum == colnumber){
				childNum = 0;
				colTable= [];
				rowNum ++;
			}
		});

		parentHeight = (Math.max.apply(Math, colTableAdd) + (rowNum * gutterWidth));
		parent.css({height: parentHeight});
		colTableAdd = [];
		rowNum = 0;
	}
	
	return {
		
		init: function(){
		
			$(window).load(function(){
				resetValues();
			});

			$(window).on("resize", function(){
				resetValues();
			});
		},

		ajaxInit: function(){
			// To be coded here
		},
	}
	
})();


		

