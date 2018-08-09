/*   ___       __             __       ___      ___   ___  __    __       _______.    _______  __    __
    /   \     |  |           |  |     /   \     \  \ /  / |  |  |  |     /       |   |   ____||  |  |  |
   /  ^  \    |  |           |  |    /  ^  \     \  V  /  |  |  |  |    |   (----`   |  |__   |  |  |  |
  /  /_\  \   |  |     .--.  |  |   /  /_\  \     >   <   |  |  |  |     \   \       |   __|  |  |  |  |
 /  _____  \  |  `----.|  `--'  |  /  _____  \   /  .  \  |  `--'  | .----)   |    __|  |____ |  `--'  |
/__/     \__\ |_______| \______/  /__/     \__\ /__/ \__\  \______/  |_______/    (__)_______| \______/
*/
/*
///////////////////////////////////////////////////////////
///////////////////////// PRELOADER ///////////////////////
///////////////////////////////////////////////////////////
*/
// {
// 	$(document).ready(function() {
// 		$('#loader-wrapper').html(null);
// 		console.log('-------------------------------');
// 		$('body').addClass('loaded');
// 		console.log('Disabled preloader');
// 		console.log('-------------------------------');
// 	});
// }


{
$(document).ready(function() {
	console.log('***************************************************');
	console.log('Preloader - start');
	let status = Cookies.get('preloader');
	let input = $('#footer').find('.togglePreloader').find('input');

	console.log('Preloader cookie = '+status);
	if (status == 'false') {
		console.log('|_ Removing preloader wrapper');
		console.log('|_ Skipping preloader element');
		console.log('|_ Set preloader switch to "false"');
		$('#loader-wrapper').remove();
		input.prop('checked', false);

	} else if (status == 'undefined' || status == undefined) {
		console.log('|_ Adding preloader element');
		console.log('|_ Set "preloader" cookie to "true"');
		console.log('|_ Set preloader switch to "true"');
		$('#loader-wrapper').append('<div id="loader"></div><div class="loader-section section-left"></div><div class="loader-section section-right"></div>');
		Cookies.set('preloader', true, { expires: 365 });
		input.prop('checked', true);

	} else if (status == 'true'){
		console.log('|_ Adding preloader element');
		console.log('|_ Set preloader switch to "true"');
		$('#loader-wrapper').append('<div id="loader"></div><div class="loader-section section-left"></div><div class="loader-section section-right"></div>');
		input.prop('checked', true);


	} else {
		console.log('Preloader -> ERROR -> Cookies.get(\'preloader\') == '+status);
	}

	$('body').css('opacity', '1');
	console.log('Preloader - removed body opacity');
	console.log('Preloader - process completed');
});
$(window).on("load", function(){
	$('body').addClass('loaded');
	console.log('***************************************************');
	console.log('Preloader - all content is loaded!');
});
$(document).ready(function() {
	let input = $('#footer').find('.togglePreloader').find('input');
	input.change(function() {
    	if ($(this).is(":checked")){
	      	console.log('Preloader - toggle to "true"');
			Cookies.set('preloader', true, { expires: 365 });
	    } else {
	      	console.log('Preloader - toggle to "false"');
			Cookies.set('preloader', false, { expires: 365 });
	    }
  	});
});
}
