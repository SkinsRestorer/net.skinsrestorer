<div class="card">
	<div class="row">
		<div class="col s12 m12 l12" id="releases">
			<center><h5>Loading . . .</h5></center>
		</div>
	</div>
</div>
<script type="text/javascript">


/*
///////////////////////////////////////////////////////////
///////////////////// RELEASES LOADER /////////////////////
///////////////////////////////////////////////////////////
*/
$(document).ready(function() {
	loadReleases();
});
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

			data.data.releases.forEach(function(el) {
				let loopDat = [];
				let content = "";
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

					loopDat.url = url.toLowerCase();
				}
				{
					switch (el.type) {
						case 'dev':
							loopDat.getBtnColor = 'blue darken-3';
							break;
						case 'alpha':
							loopDat.getBtnColor = 'blue darken-2';
							break;
						case 'beta':
							loopDat.getBtnColor = 'blue darken-1';
							break;
						case 'stable':
							loopDat.getBtnColor = 'green';
							break;
						case 'official':
							loopDat.getBtnColor = 'green';
							break;
						default:
							loopDat.getBtnColor = 'grey';
					}
				}
				{


					content += '<div class="release" id="release_'+el.id+'">';
						content += '<div class="release-header row">';
							content += '<div class="release-title col s12 m8 l9 xl9"><a href="'+loopDat.url+'">'+el.title+'</a></div>';
							content += '<div class="release-downloadBtn right-align col s6 m4 l3 xl3 push-s6">';
								content += '<a href="https://skinsrestorer.net/get/'+el.id+'" class="waves-effect waves-light btn-small '+loopDat.getBtnColor+'"><i class="material-icons left" style="margin-right:4px;">get_app</i>'+el.type+'</a>';
							content += '</div>';
							content += '<div class="col s6 m4 l8 xl9 pull-s6">';
								content += '<span class="release-time">'+loopDat.timeNoSec+'</span><br>';
							content += '</div>';
							content += '<div class="col s12 m8 l4 xl3 right-align">';
								content += el.downloads+' downloads - Size: '+el.size;
							content += '</div>';
						content += '</div>';


						content += '<div class="release-post">';
							content += '<div class="release-content">';
								content += bbcode2html(el.content);
							content += '</div>';
							content += '<a class="waves-effect waves-light btn-small release-btn-heightToggle" href="'+loopDat.url+'">Read more..</a>';
						content += '</div>';
					content += "</div>";
				}

				console.log(['Release id: '+el.id, el, loopDat]);
				maindiv.append(content);
			});
		}
		$(".release-prettytime").prettydate({
			autoUpdate: true,
			duration: 5000 // milliseconds
		});

		// $('.release-btn-heightToggle').click(function() {
		// 	console.log('Toggle button clicked');
		//
		// 	let toggleBtn = $(this);
		// 	let contentDiv = toggleBtn.parents('.release-post').children('.release-content');
		// 	let maxHeight = contentDiv.prop('scrollHeight');
		//
		// 	if (toggleBtn.attr('state') == 'showmore'){
		// 		contentDiv.css('max-height', maxHeight);
		// 		toggleBtn.attr('state', 'showless');
		// 		toggleBtn.html('Show less...');
		// 	} else {
		// 		contentDiv.css('max-height', '');
		// 		toggleBtn.attr('state', 'showmore');
		// 		toggleBtn.html('Show more..');
		// 	}
		//
		// });

	}).fail(function( data ) {
		console.log("***************************************************");
		console.log("Releases - fail");
		M.toast({html: '<i class="material-icons">warning</i>Unable to query data from the server'});
	}).always(function( data ) {
		console.log("Releases - process completed");

	});

}


</script>
<style media="screen">
	#releases .release-content {
		max-height: 200px;
		overflow-y: hidden;
		-webkit-transition: max-height 0.8s;
		-moz-transition: max-height 0.8s;
		transition: max-height 0.8s;
	}
	#releases .release-post .release-btn-heightToggle {
		float: right;
    	margin-top: -24px;
	}
</style>
<link rel="stylesheet" href="/src/_custom/index.releases.css">
