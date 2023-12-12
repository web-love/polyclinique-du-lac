AJAXIFY = (function(){   
	
	var getQuery = function(target){
		
		// Check for support
		('history' in window && 'pushState' in history) ? event.preventDefault() : false;
		
		// Get the query from dkit-ajax in dom and prefix with .
		var query = target.attr('dkit-ajax').split(' ');
		for(var i=0;i<query.length;i++){ query[i]="."+query[i]};
		
		getRequest(target, query);
		
	};
	
	var setState = function(url){			
		window.history.pushState(url, "devkitAjax", url);
	};
	
	
	
	var getRequest = function(request, targets){

		var url = request.attr("href");
      	var thePage = request.attr("pagename");	
		var response = [];
		
		current = window.location.href;

		if ( current != url ){
			$.ajax( url, {
				type: "GET",
				cache: false,
				dataType: 'html',
				success: function(data){ 
					temp = jQuery(jQuery.trim(data));

					jQuery.each(targets, function(index) {
						response.push(temp.filter(targets[index]).html());
					});
				},
				error: function(){
					return undefined;
				},
				complete: function(){ 
					console.log(response);
					setState(url);
					return response;
				}
			});	
		}
		
	};
	
	
	return {
		
		init: function(){
			$('['+ kitAttr +'ajax]').on('click', function(){
				getQuery($(this));
			});
		}
		
	}
	
})();
	
















