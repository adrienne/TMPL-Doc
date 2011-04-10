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
   
   
   $('li.top > a').click(function(event) {
      
      var templateList = $(this).parent().find('ul');
      
      if (templateList.find('li').length > 0) {
         
         if (templateList.is(":visible")) {
            templateList.hide();
         } else {
            templateList.show();
         }
      }
      
      event.preventDefault();
   });
   
   var templates = $('.template');
   
   $('li.top ul li a').click(function(event) {
      
      var id = $(this).attr('href');
      
      templates.hide();
      
      $('div#'+id).show();
      
      event.preventDefault();
   });
   
   $('form#sites-form select').change(function() {
      if ($(this).val() !== '' && $(this).val() !== 'Select One') {
         $('form#sites-form').submit();
      }
   });
		
});