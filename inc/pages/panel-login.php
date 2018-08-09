<div class="row">
	<div class="col s12 m9 l8 offset-s0 offset-m1 offset-l2">
		<div class="card row" id="loginForm">
			<div class="input-field col s12">
	          <input id="uid" type="text" autofocus>
	          <label for="uid">Username</label>
	        </div>
			<div class="input-field col s12">
	          <input id="pwd" type="password">
	          <label for="pwd">Password</label>
	        </div>
			<div class="col s12 m12 l12 right-align">
				<button class="waves-effect waves-light btn green" id="login-btn">Login</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$(document).keypress(function(e){if(e.which == 13) {
		login();
    }});

	$('#login-btn').on('click', function(event) {
		event.preventDefault();

		$(this).attr('disabled', true);
		setTimeout(function(){
			$('#login-btn').removeAttr('disabled');
		}, 3000);

		if ( cookieCheckTerms() ){
			login();
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
function login(){

	let uid = $('#uid').val();
	let pwd = $('#pwd').val();
	$.post(
		'/ajax/user.login.php',
		{
			uid: uid,
			pwd: pwd
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
			let uid = $('#uid').parent('div');
			let pwd = $('#pwd').parent('div');
			{
				let check1 = data.is_set;
				let check2 = data.is_correct;

				if (check1.uid){
					uid.children('input').removeClass('invalid');
					uid.children('input').addClass('valid');
					uid.children('span.helper-text').attr('data-success', 'Username is valid');
				} else {
					uid.children('input').removeClass('valid');
					uid.children('input').addClass('invalid');
					uid.children('span.helper-text').attr('data-error', 'Username is required');
				}
				if (check1.pwd){
					pwd.children('input').removeClass('invalid');
					pwd.children('input').addClass('valid');
					pwd.children('span.helper-text').attr('data-success', 'Password is valid');
				} else {
					pwd.children('input').removeClass('valid');
					pwd.children('input').addClass('invalid');
					pwd.children('span.helper-text').attr('data-error', 'Password is invalid');
				}
				if (check2.credentials && data.login){
					pwd.children('input').removeClass('invalid');
					pwd.children('input').addClass('valid');
					pwd.children('span.helper-text').attr('data-success', 'Password is valid');
					{
						M.toast({html: '<i class="material-icons left">check</i> Successful login',activationPercent:0.7,classes:"green"});
						let htmldata = '<div class="card-content"><div class="s12 center-align"><h4>Successful login</h4><p>You will be redirected soon...</p></div></div>';
						$('#loginForm').html(htmldata);
						setTimeout(function(){
							window.location.replace(data.redirect);
						}, 3000);
					}
				} else {
					pwd.children('input').removeClass('valid');
					pwd.children('input').addClass('invalid');
					pwd.children('span.helper-text').attr('data-error', 'Credentials do not match');
					uid.children('input').removeClass('valid');
					uid.children('input').addClass('invalid');
					uid.children('span.helper-text').attr('data-error', 'Credentials do not match');
					M.toast({html: '<i class="material-icons left">cancel</i> Credentials do not match',activationPercent:0.7,classes:"red"});
				}
			}
		}

	});

}

</script>

<style media="all">

#loginForm {
	padding: 24px;
	margin-top: 10vh;
}

</style>
