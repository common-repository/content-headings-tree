jQuery(document).ready(function() {
 
	var stickyTop = jQuery('.content-headings-tree-sticky').offset().top; // returns number   
 
	jQuery(window).scroll(function(){ // scroll event  
 
		var windowTop = jQuery(window).scrollTop(); // returns number
 
		if (stickyTop < windowTop) {
			jQuery('.content-headings-tree-sticky').css({
				position: 'fixed', 
				top: 0
			});
		}
		else {
			jQuery('.content-headings-tree-sticky').css('position','static');
		}
 
	});
 
});