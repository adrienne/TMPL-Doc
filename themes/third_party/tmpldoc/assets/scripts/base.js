$(function(){
	
   $('.collapsable-handle').click(function(event) {
      
      var handle = $(this),
          div = handle.next('div.collapsable');
            
      if (div.is(':visible')) {
         handle.find('span').html('[+]');
         div.slideUp();
      } else {
         handle.find('span').html('[-]');
         div.slideDown();
      }
            
      event.preventDefault();
   });
   

   var toggleNav = function(templateList) {

      if (templateList.find('li').length > 0) {
         
         if (templateList.is(":visible")) {
            templateList.hide();
         } else {
            templateList.show();
         }
      }
	};

   $('li.top > a').click(function(event) {
	
		event.preventDefault();
	
		var templateList = $(this).parent().find('ul');
	
		toggleNav(templateList); 

   });
   
	var showTemplate = function(id) {
		templates.hide();
      $('div#'+id).show();
	};

   var templates = $('.template');
   
   $('li.top ul li a').click(function(event) {
      
      var id = $(this).attr('href');
      
		showTemplate(id);
		
		document.location.hash = 'template-'+id;
     
      event.preventDefault();

   });

   $('form#sites-form select').change(function() {
      if ($(this).val() !== '' && $(this).val() !== 'Select One') {
         $('form#sites-form').submit();
      }
   });

	if (window.location.hash !== '') {
		
		var id = window.location.hash.toString().replace('#template-', '');
		
		showTemplate(id);
		
		var href = $('a[href="'+id+'"]');
		
		if (href.length !== 0) {
			
			var templateList = href.parent().parent('ul')
			
			toggleNav(templateList);
		}
	
	}
		
});