
$(document).ready(function(){
	
	// === jQuery Peity === //
	$.fn.peity.defaults.line = {
		strokeWidth: 1,
		delimeter: ",",
		height: 24,
		max: null,
		min: 0,
		width: 50
	};
	$.fn.peity.defaults.bar = {
		delimeter: ",",
		height: 24,
		max: null,
		min: 0,
		width: 50
	};
	$(".peity_line_good span").peity("line", {
		colour: "#B1FFA9",
		strokeColour: "#459D1C"
	});
	$(".peity_line_bad span").peity("line", {
		colour: "#FFC4C7",
		strokeColour: "#BA1E20"
	});	
	$(".peity_line_neutral span").peity("line", {
		colour: "#CCCCCC",
		strokeColour: "#757575"
	});
	$(".peity_bar_good span").peity("bar", {
		colour: "#459D1C"
	});
	$(".peity_bar_bad span").peity("bar", {
		colour: "#BA1E20"
	});	
	$(".peity_bar_neutral span").peity("bar", {
		colour: "#757575"
	});
	
	// === jQeury Gritter, a growl-like notifications === //
	$.gritter.options = {
		position: 'center-center',
		fade_out_speed: 1000,
		time: 3000
	}
	$.gritter.add({
		// title:	'操作失败',
		text:	'密码不能为空',
		// image: 	'img/demo/envelope.png',
		sticky: true
	});	
	$('#gritter-notify .normal').click(function(){
		$.gritter.add({
			title:	'Normal notification',
			text:	'This is a normal notification',
			class_name: 'error',
			sticky: true
		});		
	});
	
	$('#gritter-notify .sticky').click(function(){
		$.gritter.add({
			title:	'Sticky notification',
			text:	'This is a sticky notification',
            class_name: 'light',
			sticky: true
		});		
	});
	
	$('#gritter-notify .image').click(function(){
		var imgsrc = $(this).attr('data-image');
		$.gritter.add({
			title:	'Important Unread messages',
			text:	'You have 12 unread messages.',
			class_name: 'success',
			image: imgsrc,
			sticky: true
		});		
	});
});
