<?php
if ($_SESSION['u_isop'] != true){
	require_once __DIR__ . '/error-401.php';
} else {
?>
<div class="card">
	<div class="row">
		<div class="col s12 m12 l12 right-align" style="padding: .75rem .75rem;">
			<button class="waves-effect waves-light btn-small" onclick="$('#publishNewReleaseModal').modal('open');" id="publishNewReleaseBtn"><i class="material-icons left">publish</i> Publish new release</button>
		</div>
		<div class="col s12 m12 l12" id="releases">
			<center><h5>Loading . . .</h5></center>
		</div>
	</div>
</div>
<div id="publishNewReleaseModal" class="modal">
	<div class="modal-content">
	  	<h4>Publish new release</h4>
	  	<div class="row">
			<form id="rel_form" method="POST" action="/ajax/panel.release.post.php">
				<div class="input-field col s12 m6 l4">
					<input id="rel_title" type="text">
	          		<label for="rel_title">Release title</label>
			  	</div>
				<div class="input-field col s12 m6 l4">
					<input id="rel_version" type="text">
	          		<label for="rel_version">Release version</label>
			  	</div>
				<div class="input-field col s12 m6 l4">
					<select id="rel_type">
					  	<option value="dev" selected>Development</option>
					  	<option value="alpha">Alpha</option>
					  	<option value="beta">Beta</option>
					  	<option value="stable">Stable</option>
					  	<option value="official">Official</option>
					</select>
					<label>Type of release</label>
			  	</div>
				<div class="file-field input-field col s12 m6 l6">
	      			<div class="btn-small">
				        <span>File</span>
				        <input type="file" id="rel_file" name="rel_file">
	      			</div>
	  				<div class="file-path-wrapper">
	        			<input class="file-path" type="text">
			      	</div>
				</div>
				<div class="col s12">
	          		<textarea id="rel_content" name="rel_content"></textarea>
	        	</div>
				<!-- <div class="input-field col s12">
	          		<textarea id="rel_content" class="materialize-textarea"></textarea>
	          		<label for="rel_content">Release content</label>
	        	</div> -->
				<div class="col s12 m12 l12 right-align customFooter">
				  	<button class="waves-effect waves-white green btn-flat white-text" submit="release_post" id="publishNewReleaseModalBtn">Publish</button>
				</div>
			</form>
	  	</div>
	</div>
</div>
<script type="text/javascript">



$(document).ready(function() {
	$('#publishNewReleaseModal').modal({
		'startingTop': '1%',
		'endingTop': '2%'
	});
	$('#rel_type').formSelect();

	var textarea = document.getElementById('rel_content');
	sceditor.create(textarea, {
		format: 'bbcode',
		width: '100%',
		height: '100%',
		resizeEnabled: true,
		autoExpand: true,
		autoUpdate: true,
		id: 'rel_content_editor',
		bbcodeTrim: true,

		emoticons: {
				// Emoticons to be included in the dropdown
			    dropdown: {
			        ':smile:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/smile.png',
					':wink:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/wink.png',
					':tongue:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/tongue.png',
					':angry:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/angry.png',
					':cheerful:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/cheerful.png',
					':sad:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/sad.png',
					':shocked:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/shocked.png',
					':sick:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/sick.png',
					':sleeping:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/sleeping.png',
					':unsure:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/unsure.png',
					':w00t:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/w00t.png',
					':wassat:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/wassat.png',
					':laughing:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/laughing.png',
					':grin:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/grin.png'
			    },
			    // Emoticons to be included in the more section
			    more: {
			        ':alien:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/alien.png',
			        ':blink:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/blink.png',
					':wub:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/wub.png',
					':angel:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/angel.png',
					':blush:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/blush.png',
					':cool:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/cool.png',
					':ninja:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/ninja.png',
					':pinch:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/pinch.png',
					':pouty:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/pouty.png',
					':sideways:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/sideways.png',
					':silly:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/silly.png',
					':whistling:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/whistling.png',
					':heart:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/heart.png',
					':happy:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/happy.png',
					':getlost:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/getlost.png',
					':ermm:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/ermm.png',
					':dizzy:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/dizzy.png',
					':devil:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/devil.png',
					':cwy:': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/cwy.png'
			    },
			    // Emoticons that are not shown in the dropdown but will still
			    // be converted. Can be used for things like aliases
			    hidden: {
					'>:(': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/angry.png',
					'>:)': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/devil.png',
			        ':)': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/smile.png',
			        ';)': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/wink.png',
					':P': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/tongue.png',
					':D': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/grin.png',
					':(': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/sad.png',
					':O': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/shocked.png',
					':o': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/shocked.png',
					'<3': 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/emoticons/heart.png'
			    }
			},
		toolbar: 'bold,italic,underline,strike|subscript,superscript|left,center,right,justify|font,size,color,removeformat,emoticon|bulletlist,orderedlist|image,youtube,email,link,unlink|source',

		icons: 'material',
		style: 'https://static.aljaxus.eu/lib/jquery-sceditor/sceditor-2.1.3/minified/themes/defaultdark.min.css'
	});

	sceditor.instance(textarea).focus();

	loadReleases();
});
/*
///////////////////////////////////////////////////////////
///////////////////// RELEASES LOADER /////////////////////
///////////////////////////////////////////////////////////
*/
function loadReleases() {
	$.post(
		'/api/get/releases',
		{

		},
		function(data, textStatus, xhr) {
			/*optional stuff to do after success */
		}
	).done(function( data ) {
		console.log("***************************************************");
		console.log("Releases - success");
		console.log("|_ Cleared main #releases div");
		$('#releases').html(null);
		console.log(data);
		{
			let maindiv = $('#releases');

			let content = '';
			content += '<table class="table"><thead><tr><th scope="col">ID</th><th scope="col">Title</th><th scope="col">Version</th><th scope="col">Type</th><th scope="col">Downloads</th><th scope="col">Size</th><th scope="col">Time</th><th scope="col">Actions</th></tr></thead><tbody>';

			data.data.releases.forEach(function(el) {
				let loopDat = [];
				{
					let a = date = new Date(el.time*1000);

					let months = ['Jan','Feb','Mar','Apr','Maj','Jun','Jul','Avg','Sep','Okt','Nov','Dec'];

					let year = a.getFullYear();
					let month = months[a.getMonth()];
					let day = a.getDate();
					let hour = a.getHours();
					let min = a.getMinutes() < 10 ? '0' + a.getMinutes() : a.getMinutes();
					let sec = a.getSeconds() < 10 ? '0' + a.getSeconds() : a.getSeconds();
					let time = day + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
					let time_nosec = day + ' ' + month + ' ' + year + ' ' + hour + ':' + min ;

					loopDat.timeFormat = time;
					loopDat.timeNoSec = time_nosec;
				}
				{

					let title = el.title.replace(/(\s|\-)+/g, '-');

					let url = 'https://skinsrestorer.net/r/'+title+'.'+el.id;

					loopDat.urlRelease = url.toLowerCase();
				}
				{
					let temp1 = el.content;
					let exp = /((https?:\/\/)?([a-z0-9\.\-]*)\.([a-z0-9]{2,9})((\s)|((\/|\?)([a-z0-9\/<>!\?\=\&]*))))/ig;
					loopDat.content = temp1.replace(exp, '<a href="$1" target="_blank">$2$3.$4</a>');

					loopDat.urlDownload = 'https://skinsrestorer.net/get/'+el.id;
				}
				{

					content += '<tr id="release_'+el.id+'" release="'+el.id+'">';
						content += '<th>'+el.id+'</th>';
						content += '<th><a href="'+loopDat.urlRelease+'" target="_blank">'+el.title+'</a></th>';
						content += '<th>'+el.ver+'</th>';
						content += '<th>'+el.type+'</th>';
						content += '<th>'+el.downloads+'</th>';
						content += '<th>'+el.size+'</th>';
						content += '<th>'+loopDat.timeFormat+'</th>';
						content += '<th class="actionBtns">';
							content += '<button class="waves-effect waves-light btn-small red dropReleaseBtn"><i class="material-icons left">delete</i></button>';
							content += '<a class="waves-effect waves-light btn-small red darken-1 downloadReleaseBtn" href="'+loopDat.urlDownload+'"><i class="material-icons left">get_app</i></a>';
						content += '</th>';
					content += '</tr>';

				}

				console.log(['Release id: '+el.id, el, loopDat]);

			});

			content += '</tbody></table>';
			maindiv.append(content);
		}


		$('button.dropReleaseBtn').click(function(event) {
			let btn = $(this);
			let tr = $(this).parents('tr');
			let id = tr.attr('release');
			let selState = btn.hasClass('selectedYes');

			if ( selState ){

				btn.html('<i class="material-icons left">delete</i>');
				btn.attr('disabled', true);
				btn.addClass('selectedNo');
				btn.removeClass('selectedYes');

				$.post(
					'/ajax/panel.release.delete.php',
					{
						id: id
					},
					function(json, textStatus) {

					}
				).done(function(data){
					console.log("***************************************************");
					console.log("Delete release - success");
					console.log(data);
					loadReleases();

				}).fail(function(data){
					console.log("***************************************************");
					console.log("Delete release - fail");
					console.log(data);
					M.toast({html: '<i class="material-icons">warning</i>Unable to query data from the server'});

				}).always(function(data){
					btn.attr('disabled', false);
				});

			} else if ( !selState ){

				btn.removeClass('selectedNo');
				btn.addClass('selectedYes');
				btn.html('DELETE');


			} else {
				console.log("/!\\ Error on selState - expected boolean");
				console.log(selState);
			}


		});

	}).fail(function( data ) {
		console.log("***************************************************");
		console.log("Releases - fail");
		M.toast({html: '<i class="material-icons">warning</i>Unable to query data from the server'});
	}).always(function( data ) {
		console.log("Releases - process completed");

	});

}
</script>
<script>
$('#rel_form').submit(function(event) {

	event.preventDefault();


	let rel_formData = new FormData(this);

	rel_formData.append( 'rel_title', $('#rel_title').val() );
	rel_formData.append( 'rel_version', $('#rel_version').val() );
	rel_formData.append( 'rel_type', $('#rel_type').val() );

	$.ajax({
		url: '/ajax/panel.release.post.php',
		// url: '/devbox/debug.php',
		type: 'POST',
		data: rel_formData,
        processData: false,
        contentType: false,
        cache: false
	})
	.done(function( data ) {
		console.log("***************************************************");

		if ( data.is_posted == true || data.is_posted == 'true' ) {
			console.log("Release post - success");
			console.log("|_ Triggered the success-toast render");
			console.log("|_ Force-closing the modal");
			console.log("|_ Re-rendering releases table");
			console.log("|_ Cleared inputs in #publishNewReleaseModal modal");

			M.toast({html: 'Successfully published new release', classes: 'green'});
			$('#publishNewReleaseModal').modal('close');
			$('#rel_title').val(null);
			$('#rel_version').val(null);
			$('#rel_type').val(null);
			$('#rel_file').val(null);
			$('#rel_content').val(null);
			loadReleases();
		} else if ( data.is_posted == false || data.is_posted == 'false' ) {
			console.log("Release post - user error");
			console.log('|_ Testing "is_correct" for all inputs');
			console.log('|_ Setting error to ( "is_correct" = false ) inputs');

			(data.is_correct.rel_title || data.is_correct.rel_title == 'true' ) 	? $('#rel_title').addClass('valid').removeClass('invalid') : $('#rel_title').addClass('invalid').removeClass('valid');
			(data.is_correct.rel_version || data.is_correct.rel_version == 'true' ) ? $('#rel_version').addClass('valid').removeClass('invalid') : $('#rel_version').addClass('invalid').removeClass('valid');
			(data.is_correct.rel_type || data.is_correct.rel_type == 'true' ) 		? $('#rel_type').siblings('input').addClass('valid').removeClass('invalid') : $('#rel_type').siblings('input').addClass('invalid').removeClass('valid');
			(data.is_correct.rel_content || data.is_correct.rel_content == 'true' ) ? $('#rel_content').addClass('valid').removeClass('invalid') : $('#rel_content').addClass('invalid').removeClass('valid');
			(data.is_correct.rel_file || data.is_correct.rel_file == 'true' ) ? $('#rel_file').parents('.file-field.input-field').find('input.file-path').addClass('valid').removeClass('invalid') : $('#rel_file').parents('.file-field.input-field').find('input.file-path').addClass('invalid').removeClass('valid');
		} else {
			console.log("Release post - system error");
			console.log('|_ Expected data was not found');
		}

		console.log(data);

	})
	.fail(function( data ) {
		console.log("***************************************************");
		console.log("Release post - fail");
		console.log(data);
		M.toast({html: '<i class="material-icons">warning</i> Unable to send data to server'});
	})
	.always(function( data ) {
		console.log("Release post - process completed");
	});

});

</script>
<style media="all">
	#releases {
		overflow-x: scroll;
	}
	#releases table {
		background-color: #fff;
		overflow-x: scroll;
	}
	#releases table tr > th {
		font-weight: normal;
	}
	#releases table tr > th .btn-small {
		padding: 0 9px;
		margin-left: 2px;
	}
	#releases table tr > th .btn-small > i {
		margin: 0;
	}

	#publishNewReleaseModal {
		max-height: unset;
	}
	#publishNewReleaseModal > .modal-content {
		padding: 12px 24px 0 24px;
	}
	#publishNewReleaseModal .file-field > .file-path-wrapper,
	#publishNewReleaseModal .file-field > .btn-small,
	#publishNewReleaseModal .file-field > .btn-small > span,
	#publishNewReleaseModal .file-field > .btn-small > input {
		height: 2rem;
	}
	#publishNewReleaseModal .file-field > .btn-small {
		line-height: 32.4px;
	}
	#publishNewReleaseModal .file-field > .file-path-wrapper > input {
		height: 1.9rem;
	}
	#publishNewReleaseModal .input-field > #rel_content {
		transition: height .1s ease-in-out;
	}
	#publishNewReleaseModal .customFooter {
		margin-top: 7px;
	}

</style>
<?php } ?>
