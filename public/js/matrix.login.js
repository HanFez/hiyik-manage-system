
function matrixLogin() {
	var login = $('#login-form');
	var recover = $('#recover-form');
	var speed = 400;

	$('#to-recover').click(function(){
		
		$("#login-form").slideUp();
		$("#recover-form").fadeIn();
	});
	$('#to-login').click(function(){
		
		$("#recover-form").hide();
		$("#login-form").fadeIn();
	});
	
    
    if($.browser.msie == true && $.browser.version.slice(0,3) < 10) {
        $('input[placeholder]').each(function(){ 
       
        var input = $(this);       
       
        $(input).val(input.attr('placeholder'));
               
        $(input).focus(function(){
             if (input.val() == input.attr('placeholder')) {
                 input.val('');
             }
        });
       
        $(input).blur(function(){
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.val(input.attr('placeholder'));
            }
        });
    });

        
        
    }
}