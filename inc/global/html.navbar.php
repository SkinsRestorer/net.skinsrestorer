<!--
///////////////////////////////////////////////////////////
/////////////////////// NAVBAR LOADER /////////////////////
///////////////////////////////////////////////////////////
-->
<div class="navbar-fixed">
	<nav id="navigacija-navbar">
	  	<div class="nav-wrapper">
			<a href="#" data-target="slide-out" class="sidenav-trigger right"><i class="material-icons">menu</i></a>
	    	<ul class="right hide-on-med-and-down">
	      		<li><a>Loading ...</a></li>
	    	</ul>
	  	</div>
	</nav>
</div>
<ul id="slide-out" class="sidenav">
    <li><a>Loading ...</a></li>
</ul>
<script>
$(document).ready(function() {
	$('.sidenav').sidenav({
		draggable: true,
		preventScrolling: true,
		inDuration: 350,
		outDuration: 400,
		edge: 'right'
	});
	$.getJSON(
		'/api/get/user/navbar'
	).done(function( requestdata ) {
		console.log("***************************************************");
    	console.log( "navbar - success" );
		console.log( requestdata );
		{
			let userdata = requestdata.data.userdata;
			let tabs = requestdata.data.tabs;
			let navbar = $('#navigacija-navbar').children('div.nav-wrapper').children('ul');
			let sidebar = $('#slide-out');

			navbar.html(null);
			sidebar.html(null);

			if (true){

				function element(el) {
					tabs.forEach(function(el) {
						let eld = {
							icon: null,
							target: null,
							extraClass: null,
							dropdownCode: null
						}

						// ICON PROCESSING
						{
							if ('materialicon' in el){
								eld.icon = '<i class="material-icons left" style="margin-right:15px;">'+el.materialicon+'</i>';
							} else if ('imgicon' in el) {
								eld.icon = '<img src="'+el.imgicon+'" style="height:22px;width:auto;margin-bottom:-7px;margin-right:15px;"/>';
							} else if ('faicon' in el) {
								eld.icon = '<i class="'+el.faicon+'"></i>';
							} else {
								console.log('|_ Element has invalid icon value');
								console.log(el);
								eld.icon = '';
							}
						}

						// TARGET PROCESSING
						{
							eld.target = ('target' in el ? el.target : '_top');
							eld.extraClass = ('class' in el ? ' ' + el.class : null);
						}

						// EXTRACLASS PROCESSING
						{
							eld.extraClass = ('class' in el ? el.class : '');
						}

						// ELEMENT CONSTRUCTING
						{
							if (el.element == 'a') {
								navbar.append('<li><a href="'+el.href+'" target="'+eld.target+'" class="'+eld.extraClass+'">'+eld.icon+'<span>'+el.text+'</span></a></li>');
								sidebar.append('<li><a href="'+el.href+'" target="'+eld.target+'" class="'+eld.extraClass+'">'+eld.icon+'<span>'+el.text+'</span></a></li>');

							} else if (el.element == 'dropdown') {

								let dropdownmenu = '<ul id="'+el.id+'navbar" class="dropdown-content">';
								el.items.forEach(function(temp1){
									dropdownmenu += subElement(temp1);
								});
								dropdownmenu += '</ul>';


								let dropdownbtn = '<li><a class="dropdown-trigger '+eld.extraClass+'" data-target="'+el.id+'navbar">'+el.text+eld.icon+'</a></li>';

								navbar.before(dropdownmenu);
								navbar.append(dropdownbtn);
								navbar.find('.dropdown-trigger').dropdown({
									'autoTrigger': true,
									'coverTrigger': false,
									'constrainWidth': false,
									'hover': false,
									'inDuration': 230,
									'outDuration': 300
								});
								{
									let extraClass = null;
									let icon = '';
									if ('class' in el){
										extraClass = ' ' + el.class;
									}
									if ('materialicon' in el){
										icon = '<i class="material-icons left" style="margin-right:15px;">'+el.materialicon+'</i>';
									} else if ('imgicon' in el) {
										icon = '<img src="'+el.imgicon+'" style="height:22px;width:auto;margin-bottom:-7px;margin-right:15px;"/>';
									} else if ('faicon' in el) {
										icon = '<i class="'+el.faicon+'"></i>';
									} else {
										console.log('|_ Element has invalid icon value');
										console.log(el);
										icon = '';
									}
									let collapsible = '<li class="no-padding"><ul id="'+el.id+'sidebarbtn" class="collapsible collapsible-accordion '+extraClass+'"><li><a class="collapsible-header"><i class="material-icons left">'+icon+'</i>'+el.text+'<i class="material-icons right">arrow_drop_down</i></a><div class="collapsible-body"><ul>';

									el.items.forEach(function(el2){
										let extraClass2 = '';
										let icon2 = '';
										if ('class' in el2){
											extraClass = ' ' + el2.class;
										}
										if ('materialicon' in el2){
											icon2 = '<i class="material-icons left" style="margin-right:15px;">'+el2.materialicon+'</i>';
										} else if ('imgicon' in el2) {
											icon2 = '<img src="'+el2.imgicon+'" style="height:22px;width:auto;margin-bottom:-7px;margin-right:15px;"/>';
										} else if ('faicon' in el2) {
											icon2 = '<i class="'+el2.faicon+'"></i>';
										} else {
											console.log('|_ Element has invalid icon value');
											console.log(el);
											icon2 = '';
										}
										collapsible += '<li class="'+extraClass2+'"><a href="'+el2.href+'">'+icon2+el2.text+'</a></li>';
									});
									collapsible += '</ul></div></li></ul></li>';
									sidebar.append(collapsible);
									$('#'+el.id+'sidebarbtn').collapsible({

									});

								}
							} else {
								console.log('|_ Element has invalid el.element value');
								console.log(el);
							}
						}

					});
				}
				function subElement(el) {
					let eld = {
						icon: null,
						target: null,
						extraClass: null,
						dropdownCode: ''
					}
					eld.target = ('target' in el ? el.target : '_top');
					eld.extraClass = ('class' in el ? ' ' + el.class : null);

					if ('materialicon' in el){
						eld.icon = '<i class="material-icons left" style="margin-right:15px;">'+el.materialicon+'</i>';
					} else if ('imgicon' in el) {
						eld.icon = '<img src="'+el.imgicon+'" style="height:22px;width:auto;margin-bottom:-7px;margin-right:15px;"/>';
					} else if ('faicon' in el) {
						eld.icon = '<i class="'+el.faicon+'"></i>';
					} else {
						eld.icon = '';
					}
					eld.dropdownCode += '<li><a href="'+el.href+'" target="'+eld.target+'" class="'+eld.extraClass+'">'+eld.icon+el.text+'</a></li>';
					return eld.dropdownCode;
				}

				element(requestdata);
			}
		}
  	}).fail(function() {
		console.log("***************************************************");
	    console.log( "navbar - error" );
		M.toast({
			html: '<i class="material-icons">warning</i> We could not access navbar data',
			displayLength: 10000,
			inDuration: 500,
			outDuration: 500,
			activationPercent: 1
		});
	}).always(function( data ) {
    	console.log( "navbar - process completed" );
	});
});
</script>
