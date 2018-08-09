<div class="row" style="margin-top: 10vh;">
	<div class="card col s12 m9 l8 offset-s0 offset-m1 offset-l2">
		<div class="row">
			<div class="col s12 m12 l6 xl6" id="updatePwdForm">
				<div class="input-field col s12">
					<input id="pwdNow" type="password">
					<label for="pwdNow">Current password</label>
					<span class="helper-text" data-error="Rejected" data-success="Accepted"></span>
				</div>
				<div class="input-field col s12 m12 l6">
					<input id="pwd1" type="password">
					<label for="pwd1">New password</label>
					<span class="helper-text" data-error="Rejected" data-success="Accepted"></span>
				</div>
				<div class="input-field col s12 m12 l6">
					<input id="pwd2" type="password">
					<label for="pwd2">Confirm new password</label>
					<span class="helper-text" data-error="Rejected" data-success="Accepted"></span>
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
	$.post(
		'/ajax/user.settings.update.php',
		{
			pwdNow: pwdNow,
			pwd1: pwd1,
			pwd2: pwd2
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
			let pwdNow = $('#pwdNow').parent('div');
			let pwd1 = $('#pwd1').parent('div');
			let pwd2 = $('#pwd2').parent('div');
			{
				let check1 = data.is_set;
				let check2 = data.is_correct;

				if (check1.pwdNow){
					pwdNow.children('input').removeClass('invalid');
					pwdNow.children('input').addClass('valid');
					pwdNow.children('span.helper-text').attr('data-success', 'Current password is valid');
				} else {
					pwdNow.children('input').removeClass('valid');
					pwdNow.children('input').addClass('invalid');
					pwdNow.children('span.helper-text').attr('data-error', 'Current password is not valid');
				}
				if (check1.pwd1){
					pwd1.children('input').removeClass('invalid');
					pwd1.children('input').addClass('valid');
					pwd1.children('span.helper-text').attr('data-success', 'New password is valid');
				} else {
					pwd1.children('input').removeClass('valid');
					pwd1.children('input').addClass('invalid');
					pwd1.children('span.helper-text').attr('data-error', 'New password is not valid');
				}
				if (check1.pwd2){
					pwd2.children('input').removeClass('invalid');
					pwd2.children('input').addClass('valid');
					pwd2.children('span.helper-text').attr('data-success', 'New password confirmation is valid');
				} else {
					pwd2.children('input').removeClass('valid');
					pwd2.children('input').addClass('invalid');
					pwd2.children('span.helper-text').attr('data-error', 'New password confirmation is not valid');
				}

				if (data.is_updated){
					pwd1.children('input').removeClass('invalid');
					pwd2.children('input').removeClass('invalid');
					pwd1.children('input').addClass('valid');
					pwd2.children('input').addClass('valid');
					pwd1.children('span.helper-text').attr('data-success', 'Password is matching');
					pwd2.children('span.helper-text').attr('data-success', 'Password successfully changed');
					{
						M.toast({html: '<i class="material-icons left">check</i> Password successfully changed',activationPercent:0.7,classes:"green"});
						let htmldata = '<div class="card-content"><div class="s12 center-align"><h4>Password successfully changed</h4><p>Please relog...</p></div></div>';
						$('#updatePwdForm').html(htmldata);
						setTimeout(function(){
							window.location.replace(data.redirect);
						}, 3000);
					}
				} else {
					pwd1.children('input').addClass('invalid');
					pwd2.children('input').addClass('invalid');
					pwd1.children('input').removeClass('valid');
					pwd2.children('input').removeClass('valid');
					pwd1.children('span.helper-text').attr('data-success', 'Password was not changed');
					pwd2.children('span.helper-text').attr('data-success', 'Password was not changed');
					M.toast({html: 'Password was not changed',activationPercent:0.7,classes:"red"});
				}

			}
		}

	});

}

</script>

<style media="all">

#updatePwdForm {
	padding: 24px;
}

</style>
