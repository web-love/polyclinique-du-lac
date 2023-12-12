// DEVKIT 2.0.0

window.ieDebug = 0; 
window.ieFlag = false;
window.kitAttr  = 'dkit-';
window.kitAttrs = kitAttr;  
window._ROOT = '';       

var $models = $('div, pre, blockquote, dl, dt, dd, p, ol, ul, li, br, hr, figcaption, figure, body, aside, adress, h1, h2, h3, h4, h5, h6, nav, main, section, header, article, footer, col, colgroup, caption, table, tr, td, th, tbody, thead, tfoot, img, area, map, embed, object, param, source, iframe, canvas, track, audio, video, strong');

$(document).ready(function(){
	
	checkIeSupport();
	getRootURL();
	checkTouchPatch();
	
});

