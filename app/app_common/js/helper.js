'use strict';
var helper = (function () {
	return {
        isEmpty: function (obj) {
        	// null and undefined are "empty"
			if (obj == null) return true;

			// Assume if it has a length property with a non-zero value
			// that that property is correct.
			if (obj.length > 0)    return false;
			if (obj.length === 0)  return true;

			// Otherwise, does it have any properties of its own?
			// Note that this doesn't handle
			// toString and valueOf enumeration bugs in IE < 9
			for (var key in obj) {
			  if (hasOwnProperty.call(obj, key)) return false;
			}
			return true;
        },
	    scrollTo: function(idElement, deviation){
	    	deviation = deviation ? deviation : 0;
        	$( "#"+idElement ).focus();
        	$('html, body').animate({scrollTop: $( "#"+idElement ).offset().top + deviation}, 'slow');
        	/*var top = $("#odometer").offset().top;
        	$('html, body').animate({
        		 scrollTop: top
        		}, 'slow');*/
	    }
    }        

}());