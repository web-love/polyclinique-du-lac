function initMap() {
	var localisation = new google.maps.LatLng(46.9362355,-71.3085442);
	var mapDiv = document.getElementById('Map-container');
	var mapOptions = {
		zoom: 15,
		center: localisation,
		scrollwheel: false,
		styles: [{"featureType":"water","elementType":"all","stylers":[{"color":"#3b5998"}]},{"featureType":"all","elementType":"geometry.fill","stylers":[{"color":"#222222"},{"lightness":1}]},{"featureType":"all","elementType":"geometry.stroke","stylers":[{"saturation":-100},{"lightness":-78}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"color":"#000000"},{"weight":3.12}]},{"featureType":"all","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"all","elementType":"geometry","stylers":[{"invert_lightness":false},{"lightness":0}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":-65}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"invert_lightness":true},{"hue":"#000000"},{"saturation":-100},{"lightness":0}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#010714"}]},{"featureType":"transit","elementType":"labels.icon","stylers":[{"invert_lightness":true},{"saturation":-100},{"lightness":25}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"invert_lightness":false},{"saturation":-100},{"lightness":-17}]},{"featureType":"road","elementType":"labels.text","stylers":[{"saturation":-100}]},{"featureType":"water","elementType":"geometry","stylers":[{"saturation":-100}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"color":"#000000"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"weight":1.75}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"lightness":50}]},{"featureType":"all","elementType":"all","stylers":[{"invert_lightness":true}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":100}]}]
	}
	var map = new google.maps.Map(document.getElementById("Map-container"), mapOptions);
	  
	var marker = new google.maps.Marker({
	    position: localisation,
		draggable: true,
		animation: google.maps.Animation.DROP,
	    map: map,
	    icon: 'https://www.polycliniquedulac.com/wp-content/themes/sudo/img/marker.svg',
	    title: 'Polyclinique du Lac'
	});  
	marker.setMap(map);
	//marker.addListener('click', toggleBounce);
	
	marker.addListener('click', function() {
	    if (marker.getAnimation() !== null) {
        	marker.setAnimation(null);
        } else {
        	marker.setAnimation(google.maps.Animation.BOUNCE);
	    }
	});
}
function toggleBounce() {
	if (marker.getAnimation() !== null) {
	marker.setAnimation(null);
	} else {
	marker.setAnimation(google.maps.Animation.BOUNCE);
	}
}
