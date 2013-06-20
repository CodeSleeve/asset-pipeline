/**
 * @author: Kelt Dockins <kelt@dockins.org>
 *
 * You can call this whenever you want to run code conditionally based 
 * on if an element(s) exist or not.
 *
 * @code example:
 * 
 *  $.bootstrap('html.ReportsController.create', function(element) {
 *  	// this is only ran when <html class="ReportsController create"> exists
 *  	// and element will be the html element (that we passed in above)
 *  });
 *
 * This means if you write all your javascript in 'modules' then you can bootstrap
 * these modules only when you are on a certain page
 *
 * As a best practice I usually do in all my Laravel layouts
 * 
 * 	!DOCTYPE html>
 *  <html lang="en" class="<?= explode('@', Route::currentRouteAction())[0] ?> <?= explode('@', Route::currentRouteAction())[1] ?>">
 *
 * This gives me the ability to access all elements under this namespace.
 */
(function($) {
	$.bootstrap = function(selector, closure) {
		$(document).ready(function() {
			if ($(selector).length && typeof closure !== 'undefined') {
				closure($(selector));
			}
		});
	};	
})(jQuery);