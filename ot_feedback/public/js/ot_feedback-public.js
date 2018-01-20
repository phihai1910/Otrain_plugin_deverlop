(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	var feedback = jQuery('.ot-feedback');
	var ot_ajax = {
		'action': 'ot_ajax',
		'status': 1
		
	};
	
	feedback.on('click','a',function(){
		
		ot_ajax['status'] = jQuery(this).children('img').data('scorce');
		jQuery.post(ot.ajaxurl, ot_ajax, function(response) {
			
			jQuery.fancybox.open(response.html,{'modal':true});
			if(response.exit){
				setTimeout(function(){
					
					jQuery.fancybox.close();
					
				}, 5000);
			}
			
			
			var ot_ajax_submit = {
				'action': 'ot_ajax_submit',
				 
			};
			jQuery('#ob_form').on('reset',function(e){
					jQuery.fancybox.close();
					
					ot_ajax_submit = {
						'action': 'ot_ajax_submit',
						'n': jQuery('.n').val(),
						's': jQuery('.s').val(),
						'i': jQuery('.i').val(),
						'a': jQuery('.a').val(),
						'e': jQuery('.e').val(),
						'u': jQuery('.u').val(),
						'c': '',
					};
					jQuery.fancybox.close();
					jQuery.post(ot.ajaxurl, ot_ajax_submit, function(response) {
						jQuery.fancybox.open(response.html);
						if(response.exit){
							setTimeout(function(){
								
								jQuery.fancybox.close();
								
							}, 5000);
						}
					});
					
					
			});
			jQuery('#ob_form').submit(function(e){
				
				e.preventDefault();
				ot_ajax_submit = {
					'action': 'ot_ajax_submit',
					'n': jQuery('.n').val(),
					's': jQuery('.s').val(),
					'i': jQuery('.i').val(),
					'a': jQuery('.a').val(),
					'e': jQuery('.e').val(),
					'u': jQuery('.u').val(),
					'c': jQuery('.c').val(),
				};
				jQuery.fancybox.close();
				jQuery.post(ot.ajaxurl, ot_ajax_submit, function(response) {
					jQuery.fancybox.open(response.html);
					if(response.exit){
						setTimeout(function(){
							
							jQuery.fancybox.close();
							
						}, 5000);
					}
				});
				
				
				
			});
		});
		
	});
	
	
	
	
})( jQuery );
