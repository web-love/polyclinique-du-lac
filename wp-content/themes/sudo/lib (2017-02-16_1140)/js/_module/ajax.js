$(function () {
var $mainContent = $('.Blog_roll'),
	$cat_links = $('.categories a');
	
	$cat_links.on('click', function (e) {
		e.preventDefault();
		$el = $(this);
		var value = $el.attr("href");
		$mainContent.animate({opacity: "0.5"}); 
		$mainContent.load(value + " .Blog_roll", function(){
			$mainContent.animate({opacity: "1"});
			
		}); 
	}); 
});