/*   ___       __             __       ___      ___   ___  __    __       _______.    _______  __    __
    /   \     |  |           |  |     /   \     \  \ /  / |  |  |  |     /       |   |   ____||  |  |  |
   /  ^  \    |  |           |  |    /  ^  \     \  V  /  |  |  |  |    |   (----`   |  |__   |  |  |  |
  /  /_\  \   |  |     .--.  |  |   /  /_\  \     >   <   |  |  |  |     \   \       |   __|  |  |  |  |
 /  _____  \  |  `----.|  `--'  |  /  _____  \   /  .  \  |  `--'  | .----)   |    __|  |____ |  `--'  |
/__/     \__\ |_______| \______/  /__/     \__\ /__/ \__\  \______/  |_______/    (__)_______| \______/
*/
///////////////////////////////////////////////////////////
// CHECK FOR USER'S CONNECTION TO THE APPLICATION SERVER //
///////////////////////////////////////////////////////////
{
	let showToast = false;
	let runCheckStatus = true;
	setInterval(function() {
		$.get(
			"/api/get/user/connection"
		).fail(function() {
			showToast = true;
		}).done(function( dat ) {
			if ((dat.debug.error == true)||(dat.debug.error == 'true')||(dat.debug.error == 'TRUE')){
				showToast = true;
			} else {
				if ((typeof dat.data.connection !== 'undefined')&&((dat.data.connection == true)||(dat.data.connection == 'true')||(dat.data.connection == 'TRUE'))){
					showToast = false;
				} else {
					showToast = true;
				}
			}
		});
	}, 4000);
	setInterval(function(){
		if (runCheckStatus){
			checkStatus();
		}
	}, 5000);
	function checkStatus(){
		if (showToast){
			M.toast({
				html: '<i class="material-icons">warning</i>Webserver could not be reached',
				displayLength: 10000,
				inDuration: 500,
				outDuration: 500,
				activationPercent: 1,
				classes: 'red',
				completeCallback: function(){checkStatus();}
			});
			runCheckStatus = false;
		} else {
			srunCheckStatus = true;
		}
	}
}
