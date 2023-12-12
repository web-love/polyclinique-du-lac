function msieversion(){
	var ua = window.navigator.userAgent
	var msie = ua.indexOf ( "MSIE " )

	if ( msie > 0 ) {  
		return parseInt (ua.substring (msie+5, ua.indexOf (".", msie )));
		ieFlag = true;
	}
	else {                 
		return ieDebug
	}
}

function checkIeSupport(){
	version = msieversion();
	if (version != 0){
		window.kitAttr = 'data-';
		$models.each(function(){
			var attrArray = [], attrValArray = [], attrOldArray = [], attrs = [];
			attrs = this.attributes;
			for (var i=0; i<attrs.length; i++) {
					if (attrs[i].name.indexOf(kitAttrs)==0){
						attrOldArray[i] = (attrs[i].name);
						attrArray[i] = 'data-' + attrOldArray[i].split('-')[1]; // attr name
						attrValArray[i] = $(this).attr(attrs[i].name);
					}
			}
			for (var i=0; i<attrArray.length; i++) {
				$(this).attr(attrArray[i], attrValArray[i]);
				$(this).removeAttr(attrOldArray[i]);
			}
		});
	}
}
 
function getRootURL(){
	if (_ROOT == '') window._ROOT = $('['+ kitAttr +'root]').attr(kitAttr +'root');
}

function checkTouchPatch(){
	var isTouch = (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0));

	if (isTouch){
		$("<link/>", {
		   rel: "stylesheet",
		   type: "text/css",
		   href: "patch/patch-touch.min.css"
		}).appendTo("head");
	}
}
