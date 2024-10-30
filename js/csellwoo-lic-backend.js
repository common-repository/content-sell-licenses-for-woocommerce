//CSELL plugin with AGRWC_VARS array parameter passed 
jQuery(document).ready(function() {

	   jQuery(".csellstatuschange").on('change', function(e) {
  var datax = new Object();
     datax['action'] = 'csellstatus_post';
	  datax['status'] = jQuery('option:selected', this).val();
	 datax['lcode'] = jQuery(this).attr('lcode');

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, datax, function(response) {
     // alert(response);

            });
           return false;
  
	
	   });
	   
	


	   jQuery("#csellwoo-lic-stats").on('click', function(e) {
  var datax = new Object();
     datax['action'] = 'csellstats_post';
	 datax['lcode'] = jQuery(this).attr('lcode');

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, datax, function(response) {
     	 //alert(response['notexp']);
		 var res=JSON.parse(response);
		 
	 jQuery("#csell_lic_stats").html("Total Purchase: "+res['cost']+"<br>Total In Active: "+res['notexp']);
	 
	  
	  //show Total number/cost of purchases, Total Active Licenses based on expiry date
            });
           return false;
  
	
	   });

	   
	   
jQuery(function($){
	// simple multiple select
	$('#csell_posts').selectWoo();

	// multiple select with AJAX search
	$('#csell_posts').selectWoo({
  		ajax: {
    			url: ajaxurl, // AJAX URL is predefined in WordPress admin
    			dataType: 'json',
    			delay: 250, // delay in ms while typing when to perform a AJAX search
    			data: function (params) {
      				return {
        				q: params.term, // search query
        				action: 'csell_ajax_post_search' // AJAX action for admin-ajax.php
      				};
    			},
    			processResults: function( data ) {
				var options = [];
				if ( data ) {
			
					// data is the array of arrays, and each of them contains ID and the Label of the option
					$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
						options.push( { id: text[0], text: text[1]  } );
					});
				
				}
				return {
					results: options
				};
			},
			cache: true
		},
		minimumInputLength: 3 // the minimum of symbols to input before perform a search
	});
	
	
		// multiple select with AJAX search
	$('#csell_pages').selectWoo({
  		ajax: {
    			url: ajaxurl, // AJAX URL is predefined in WordPress admin
    			dataType: 'json',
    			delay: 250, // delay in ms while typing when to perform a AJAX search
    			data: function (params) {
      				return {
        				q: params.term, // search query
        				action: 'csell_ajax_page_search' // AJAX action for admin-ajax.php
      				};
    			},
    			processResults: function( data ) {
				var options = [];
				if ( data ) {
			
					// data is the array of arrays, and each of them contains ID and the Label of the option
					$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
						options.push( { id: text[0], text: text[1]  } );
					});
				
				}
				return {
					results: options
				};
			},
			cache: true
		},
		minimumInputLength: 3 // the minimum of symbols to input before perform a search
	});
	
	
	
});

});