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
						content += '<th><div class="input-field"><input class="uid" type="text" placeholder="'+el.uid+'" value="'+el.uid+'"></div></th>';
						content += '<th><div class="input-field"><input class="pwd" type="password" placeholder="******************"></div></th>';
						content += '<th><div class="switch"><label>deop<input class="user_isop" '+(( el.isop == 1 ) ? 'checked' : '' )+' type="checkbox"><span class="lever"></span>op</label></div></th>';
						content += '<th class="actionBtns">';
							content += '<button class="waves-effect waves-light btn-small red dropUserBtn"><i class="material-icons left">delete</i></button>';
						content += '</th>';
					content += '</tr>';

				}

				console.log(['Release id: '+el.id, el, loopDat]);

			});

			content += '<tr id="user_new" user="new">';
				content += '<th>#</th>';
				content += '<th><div class="input-field"><input class="newuser_uid" type="text" placeholder="Username"></div></th>';
				content += '<th><div class="input-field"><input class="newuser_pwd" type="password" placeholder="******************"></div></th>';
				content += '<th><div class="switch"><label>deop<input class="newuser_isop" type="checkbox"><span class="lever"></span>op</label></div></th>';
				content += '<th class="actionBtns">';
					content += '<button class="waves-effect waves-light btn-small green newUserBtn"><i class="material-icons left">person_add</i></button>';
				content += '</th>';
			content += '</tr>';

			content += '</tbody></table>';
			maindiv.append(content);
		}

		{

			$('#users').find('button.dropUserBtn').click(function(event) {
				let btn = $(this);
				let id = btn.parents('tr').attr('user');
				let selState = btn.hasClass('selectedYes');

				if ( selState ){
					console.log("***************************************************");

					btn.html('<i class="material-icons left">delete</i>');
					btn.attr('disabled', true);
					btn.addClass('selectedNo');
					btn.removeClass('selectedYes');

					$.post(
						'/ajax/panel.user.delete.php',
						{
							u_id: id
						},
						function(json, textStatus) {

						}
					).done(function(data){
						console.log("Delete user - post success");
						console.log(data);

						if (data.is_success.is_deleted == true){
							M.toast({html: '<i class="material-icons">warning</i> User was successfully deleted', classes: 'green'});
							renderUserTable();
						} else {
							if (data.debug.deleting_self == true){
								M.toast({html: '<i class="material-icons">warning</i> You can not delete yourself', classes: 'red'});
							} else {
								M.toast({html: '<i class="material-icons">warning</i> User could not be deleted'});
							}
						}

					}).fail(function(data){
						console.log("Delete user - post fail");
						console.log(data);
						M.toast({html: '<i class="material-icons">warning</i>Unable to query data from the server'});

					}).always(function(data){
						console.log('|_ Messages:');
						data.debug.messages.forEach(function(el){
							console.log('  |_ '+el);
						});
						setTimeout(function(){
							btn.attr('disabled', false);
						}, 2500);
						console.log("Delete user - process completed");
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
					if (data.is_success.is_edited == false){
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

			$('#users').find('input.pwd').on('focusout', function(event) {
				event.preventDefault();

				let $this = $(this);
				let $pwd = $this.val();
				let id = $this.parents('tr').attr('user');

				if ($pwd.length != 0){
					if ($pwd.length >= 5){

						$.post(
							'/ajax/panel.user.edit.php',
							{
								u_id: id,
								u_pwd: $pwd,
								edit: 'pwd'
							},
							function(data, textStatus, xhr){}
						).done(function(data){
							console.log("***************************************************");
							console.log("Edit user - post success");
							if (data.is_success.is_edited == false){
								M.toast({html: '<i class="material-icons">warning</i>User was not edited (more info in console)'});
								$this.removeClass('valid').addClass('invalid');
							} else {
								M.toast({html: '<i class="material-icons">check</i>Updated user data',classes:'green'});
								$this.removeClass('invalid').addClass('valid').attr('disabled', true);
								setTimeout(function(){ $this.removeClass('valid').val(null).attr('disabled', false); }, 2500);
							}
							console.log(data);

						}).fail(function( data ) {
							console.log("***************************************************");
							console.log("Edit user - fail");
							M.toast({html: '<i class="material-icons">warning</i>Unable to query data from the server'});
						}).always(function( data ) {
							console.log('|_ Messages:');
							data.debug.messages.forEach(function(el){
								console.log('  |_ '+el);
							});
							console.log("Edit user - process completed");
						});

					} else {
						M.toast({html: '<i class="material-icons">warning</i> Password must be 5+ characters long'});
					}
				}

			});

			$('#users').find('input.uid').on('focusout', function(event) {
				event.preventDefault();

				let $this = $(this);
				let $uid = $this.val();
				let id = $this.parents('tr').attr('user');

				if ($uid != $this.attr('placeholder')){
					if ( ($uid.length >= 4) ){

						$.post(
							'/ajax/panel.user.edit.php',
							{
								u_id: id,
								u_uid: $uid,
								edit: 'uid'
							},
							function(data, textStatus, xhr){}
						).done(function(data){
							console.log("***************************************************");
							console.log("Edit user - post success");
							if (data.is_success.is_edited == false){
								if (data.debug.uid_uidisused == true){
									M.toast({html: '<i class="material-icons">warning</i>Username already in use'});
								} else {
									M.toast({html: '<i class="material-icons">warning</i>User was not edited (more info in console)'});
								}
								$this.removeClass('valid').addClass('invalid');
							} else {
								M.toast({html: '<i class="material-icons">check</i>Updated user data',classes:'green'});
								$this.removeClass('invalid').addClass('valid').attr('disabled', true).attr('placeholder', $uid);
								setTimeout(function(){ $this.removeClass('valid').attr('disabled', false); }, 2500);
							}
							console.log(data);

						}).fail(function( data ) {
							console.log("***************************************************");
							console.log("Edit user - fail");
							M.toast({html: '<i class="material-icons">warning</i>Unable to query data from the server'});
						}).always(function( data ) {
							console.log('|_ Messages:');
							data.debug.messages.forEach(function(el){
								console.log('  |_ '+el);
							});
							console.log("Edit user - process completed");
						});

					} else {
						M.toast({html: '<i class="material-icons">warning</i> Username must be 4+ characters long'});
					}
				}

			});

			$('#users').find('button.newUserBtn').on('click', function(event) {
				event.preventDefault();

				let $btn = $(this);

				let $el = $btn.parents('tr');
				let $eluid = $el.find('input.newuser_uid');
				let $valuid = $eluid.val();
				let $elpwd = $el.find('input.newuser_pwd');
				let $valpwd = $elpwd.val();
				let $elisop = $el.find('input.newuser_isop');
				let $valisop = ( $elisop.prop('checked') ) ? 1 : 0;



				let stat = {
					uid: false,
					pwd: false,
					isop: false
				};

				if ( $valuid.length >= 4 ){
					stat.uid = true;
				} else {
					$eluid.addClass('invalid');
					setTimeout(function(){
						$eluid.removeClass('invalid');
					}, 2500);
					M.toast({html: '<i class="material-icons">warning</i> Username must be 4+ characters long'});
				}
				if ( $valpwd.length >= 5 ){
					stat.pwd = true;
				} else {
					$elpwd.addClass('invalid');
					setTimeout(function(){
						$elpwd.removeClass('invalid');
					}, 2500);
					M.toast({html: '<i class="material-icons">warning</i> Password must be 5+ characters long'});
				}
				if ( ($valisop == 1) || ($valisop == 0) ){
					stat.isop = true;
				} else {
					M.toast({html: '<i class="material-icons">warning</i> Woops "isop" is not boolean <button class="btn-flat toast-action" onClick="window.location.reload()">Refresh page</button>', displayLength: 15000, activationPercent: 0.9});
				}

				if ( stat.uid==true && stat.pwd==true && stat.isop==true ){

					$eluid.prop('disabled', true);
					$elpwd.prop('disabled', true);
					$elisop.prop('disabled', true);
					$btn.prop('disabled', true);

					$.post(
						'/ajax/panel.user.create.php',
						{
							u_uid: $valuid,
							u_pwd: $valpwd,
							u_isop: $valisop
						},
						function(data, textStatus, xhr){}
					).done(function(data){
						console.log("***************************************************");
						console.log("Create user - post success");
						if (data.is_success.is_created == false){
							console.log('|_ User was not created');
							if (data.debug.is_uidused == true){
								console.log('|_ Username is already in use');
								M.toast({html: '<i class="material-icons">warning</i>Username is already in use'});
								$eluid.addClass('invalid');
								setTimeout(function(){
									$eluid.removeClass('invalid');
								}, 2500);
							}
							console.log('|_ Removed "disabled" prop from all inputs:');
							setTimeout(function(){
								$eluid.prop('disabled', false);
								$elpwd.prop('disabled', false);
								$elisop.prop('disabled', false);
								$btn.prop('disabled', false);
							}, 2500);
						} else {
							console.log('|_ User was created');
							M.toast({html: '<i class="material-icons">warning</i>User successfully created', classes: 'green'});
							setTimeout(function(){
								console.log('|_ Re-rendering users table');
								renderUserTable();
							}, 2500);
						}
						console.log(data);

					}).fail(function( data ) {
						console.log("***************************************************");
						console.log("Create user - fail");
						M.toast({html: '<i class="material-icons">warning</i>Unable to query data from the server (more info in console)'});
					}).always(function( data ) {
						console.log('|_ Messages:');
						data.debug.messages.forEach(function(el){
							console.log('  |_ '+el);
						});

						console.log("Create user - process completed");
					});
				}

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
