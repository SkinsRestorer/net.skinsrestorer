<?php
if ($_SESSION['u_isop'] != true){
	require_once __DIR__ . '/error-401.php';
} else {
?>


<div class="card">
	<div class="row">
		<div class="col s12 m12 l12" id="users">
			<center><h5>Loading . . .</h5></center>
		</div>
		<div class="col s12 m12 l12" id="newUser">
		</div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function() {
	renderUserTable();
});

function renderUserTable(){
	$.post(
		'/api/get/panel/users',
		{

		},
		function(data, textStatus, xhr) {
			/*optional stuff to do after success */
		}
	).done(function( data ) {
		console.log("***************************************************");
		console.log("Users - success");
		console.log("|_ Cleared main #users div");
		$('#users').html(null);
		console.log(data);
		{
			let maindiv = $('#users');

			let content = '';
			content += '<table class="table"><thead><tr><th scope="col">ID</th><th scope="col">Username</th><th scope="col">Password</th><th scope="col">isOP</th><th scope="col">Actions</th></tr></thead><tbody>';

			data.data.forEach(function(el) {
				let loopDat = [];
				{

					content += '<tr id="user_'+el.id+'" user="'+el.id+'">';
						content += '<th>'+el.id+'</th>';
						content += '<th><div class="input-field"><input type="text" placeholder="'+el.uid+'" value="'+el.uid+'"></div></th>';
						content += '<th><div class="input-field"><input type="password" placeholder="******************"></div></th>';
						content += '<th><div class="switch"><label>deop<input class="user_isop" '+(( el.isop == 1 ) ? 'checked' : '' )+' type="checkbox"><span class="lever"></span>op</label></div></th>';
						content += '<th class="actionBtns">';
							content += '<button class="waves-effect waves-light btn-small red dropUserBtn"><i class="material-icons left">delete</i></button>';
						content += '</th>';
					content += '</tr>';

				}

				console.log(['Release id: '+el.id, el, loopDat]);

			});

			content += '</tbody></table>';
			maindiv.append(content);
		}


		{
				$('button.dropUserBtn').click(function(event) {
					let btn = $(this);
					let id = btn.parents('tr').attr('id');
					let selState = btn.hasClass('selectedYes');

					if ( selState ){

						btn.html('<i class="material-icons left">delete</i>');
						btn.attr('disabled', true);
						btn.addClass('selectedNo');
						btn.removeClass('selectedYes');

						$.post(
							'/ajax/panel.user.delete.php',
							{
								id: id
							},
							function(json, textStatus) {

							}
						).done(function(data){
							console.log("***************************************************");
							console.log("Delete user - post success");
							console.log(data);
							renderUserTable();

						}).fail(function(data){
							console.log("***************************************************");
							console.log("Delete user - post fail");
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

				$('input.user_isop').change(function(event){

					let el = $(this);
					let id = el.parents('tr').attr('user');
					let isop = ( el.prop('checked') ) ? 1 : 0;

					$.post(
						'/ajax/panel.user.edit.php',
						{
							u_id: id,
							u_isop: isop,
							edit: 'isop'
						},
						function(json, textStatus) {

						}
					).done(function(data){
						console.log("***************************************************");
						console.log("Edit user - post success");
						if (data.is_edited == false){
							M.toast({html: '<i class="material-icons">warning</i>User was not edited (more info in console)'});
							setTimeout(function(){ (isop == true) ? el.prop('checked', false) : el.prop('checked', true); }, 220);
						} else {
							M.toast({html: '<i class="material-icons">check</i>Updated user data',classes:'green'});
						}
						console.log(data);

					}).fail(function(data){
						console.log("***************************************************");
						console.log("Edit user - post fail");
						console.log("|_ Setting toggle element to previous state");
						setTimeout(function(){
							(isop == true) ? el.prop('checked', false) : el.prop('checked', true) ;
						}, 220);
						console.log(data);
						M.toast({html: '<i class="material-icons">warning</i>Unable to query data from the server'});

					}).always(function(data){
						console.log('|_ Messages:');
						data.debug.messages.forEach(function(el){
							console.log('  |_ '+el);
						});
						console.log("Edit user - process completed");
					});
				});

		}

	}).fail(function( data ) {
		console.log("***************************************************");
		console.log("Users - fail");
		M.toast({html: '<i class="material-icons">warning</i>Unable to query data from the server'});
	}).always(function( data ) {
		console.log('|_ Messages:');
		data.debug.messages.forEach(function(el){
			console.log('  |_ '+el);
		});
		console.log("Users - process completed");
	});

}

</script>

<style media="all">
	#users {
		overflow-x: scroll;
	}
	#users table {
		background-color: #fff;
		overflow-x: scroll;
	}
	#users table tr > th {
		font-weight: normal;
	}
	#users table tr > th .input-field {
		margin: 0;
		width: 95%;
	}
	#users table tr > th .input-field > input {
		margin: 0;
		height: unset;
		background-color: rgba(0,0,0,0.03);
	}
	#users table tr > th button.btn-small {
		padding: 0 9px;
		margin-left: 2px;
	}
	#users table tr > th button.btn-small > i {
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
