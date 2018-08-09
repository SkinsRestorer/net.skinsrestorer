$(document).ready(function() {
	$('.fontSelector').children('a').dropdown({
		'constrainWidth': false,
		'coverTrigger': false
	});

	let el = $('#footer_fontSelector_dropdown');
	let option = $('#footer_fontSelector_dropdown').find('a');

	option.click(function() {
		let font = $(this).html();

		console.log('Font selector - '+font);
		Cookies.set('fontSelector', font, { expires: 365 });
		$('body').css('font-family', font);
	});

	{
		console.log('***************************************************');
		console.log('Font selector - start');
		let status = Cookies.get('fontSelector');
		let body = $('body');

		console.log('Font selector = '+status);
		if (status == 'undefined' || status == undefined) {
			console.log('|_ Set body css font-family to Sunflower');
			console.log('|_ Set "fontSelector" cookie to "Sunflower"');
			Cookies.set('fontSelector', 'Sunflower', { expires: 365 });

		} else if (status == 'Sunflower'){
			console.log('|_ Set body css font-family to Sunflower');
			console.log('|_ Set "fontSelector" cookie to "Sunflower"');
			Cookies.set('fontSelector', 'Sunflower', { expires: 365 });
			body.css('font-family', 'Sunflower');

		} else if (status == 'Source Sans Pro'){
			console.log('|_ Set body css font-family to Source Sans Pro');
			console.log('|_ Set "fontSelector" cookie to "Source Sans Pro"');
			Cookies.set('fontSelector', 'Source Sans Pro', { expires: 365 });
			body.css('font-family', 'Source Sans Pro');

		} else if (status == 'Tillana'){
			console.log('|_ Set body css font-family to Tillana');
			console.log('|_ Set "fontSelector" cookie to "Tillana"');
			Cookies.set('fontSelector', 'Tillana', { expires: 365 });
			body.css('font-family', 'Tillana');

		} else if (status == 'Roboto'){
			console.log('|_ Set body css font-family to Roboto');
			console.log('|_ Set "fontSelector" cookie to "Roboto"');
			Cookies.set('fontSelector', 'Roboto', { expires: 365 });
			body.css('font-family', 'Roboto');

		} else {
			console.log('Font selector -> ERROR -> Cookies.get(\'fontSelector\') == '+status);
		}
	}

});
