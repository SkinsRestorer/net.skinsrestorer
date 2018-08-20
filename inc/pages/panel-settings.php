<div class="row" style="margin-top: 10vh;">
	<div class="card col s12 m9 l8 offset-s0 offset-m1 offset-l2">
		<div class="row">
			<div class="col s12 m12 l6 xl6" id="updatePwdForm">
				<div class="input-field col s12">
					<input id="pwdNow" type="password">
					<label for="pwdNow">Current password</label>
				</div>
				<div class="input-field col s12 m12 l6">
					<input id="pwd1" type="password">
					<label for="pwd1">New password</label>
				</div>
				<div class="input-field col s12 m12 l6">
					<input id="pwd2" type="password">
					<label for="pwd2">Confirm new password</label>
				</div>
				<div class="col s12 m12 l12 right-align">
					<button class="waves-effect waves-light btn green" id="updatePwd-btn">Update password</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

	$('#updatePwd-btn').on('click', function(event) {
		event.preventDefault();

		$(this).attr('disabled', true);
		setTimeout(function(){
			$('#updatePwd-btn').removeAttr('disabled');
		}, 3000);

		if ( cookieCheckTerms() ){
			updatePwd();
		}
	});

	$("input").prop('required',true);
	$("input").attr('oncopy', 'clipboardEvent(); return false;');
    $("input").attr('onpaste', 'clipboardEvent(); return false;');
    $("input").attr('oncut', 'clipboardEvent(); return false;');
    $("input").attr('onfocusout', "this.setAttribute('readonly', '');");
    $("input").attr('onfocus', "this.removeAttribute('readonly');");
});

function clipboardEvent() {
	M.toast({html: 'Cipboard usage is disabled'});
}
function updatePwd(){

	let pwdNow = $('#pwdNow').val();
	let pwd1 = $('#pwd1').val();
	let pwd2 = $('#pwd2').val();
	if (pwd1.length >= 5){
		$.post(
			'/ajax/user.settings.update.php',
			{
				pwdNow: pwdNow,
				pwd1: pwd1,
				pwd2: pwd2,
				update: 'password'
			}
		).done(function( data ) {
			// M.toast({html: '<i class="material-icons left">check</i> login triggered',activationPercent:0.7});
		}).fail(function( data ) {
			M.toast({
				html: '<i class="material-icons">warning</i> Unable to reach the server'
			});
		}).always(function( data ) {
			console.log("***************************************************");
			console.log(data);
			{
				let pwdNow = $('#pwdNow');
				let pwd1 = $('#pwd1');
				let pwd2 = $('#pwd2');
				{
					let check = data.debug;

					if (check.pwdNow){
						pwdNow.removeClass('invalid').addClass('valid');
					} else {
						pwdNow.removeClass('valid').addClass('invalid');
					}
					if (check.pwd1){
						pwd1.removeClass('invalid').addClass('valid');
					} else {
						pwd1.removeClass('valid').addClass('invalid');
					}
					if (check.pwd2){
						pwd2.removeClass('invalid').addClass('valid');
					} else {
						pwd2.removeClass('valid').addClass('invalid');
					}

					if (data.is_success.dbupdate_newpwd){
						pwdNow.removeClass('invalid').addClass('valid');
						pwd1.removeClass('invalid').addClass('valid');
						pwd2.removeClass('invalid').addClass('valid');
						{
							M.toast({html: '<i class="material-icons left">check</i> Password successfully changed',activationPercent:0.7,classes:"green"});
							let htmldata = '<div class="card-content"><div class="s12 center-align"><h4>Password successfully changed</h4><p>Please relog...</p></div></div>';
							$('#updatePwdForm').html(htmldata);
							setTimeout(function(){
								window.location.replace(data.redirect);
							}, 3000);
						}
					} else {
						if (check.pwdNow){
							M.toast({html: 'Currect password is not correct',activationPercent:0.7,classes:"red"});
						} else {
							M.toast({html: 'Currect password is not correct',activationPercent:0.7,classes:"red"});
						}
						pwdNow.addClass('invalid').removeClass('valid');
						pwd1.addClass('invalid').removeClass('valid');
						pwd2.addClass('invalid').removeClass('valid');
					}

				}
			}
		});
	} else {
		M.toast({html: '<i class="material-icons left">warning</i> Password must have 5+ characters'});
	}

}

</script>

<style media="all">

#updatePwdForm {
	padding: 24px;
}

</style>
