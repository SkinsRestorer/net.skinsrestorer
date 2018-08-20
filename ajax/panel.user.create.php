<?php
require_once __DIR__ . '/../inc/util/firstload.php';
require_once __DIR__ . '/../inc/util/database.php';
require_once __DIR__ . '/../inc/util/user.php';

$dbUtil = new dbManipulate();
$dbConn = new dbconn();
$userUtil = new user();
$ipUtil = new getUserIp();

$conn = $dbConn->oopmysqli();

$statusArr = array(
	'is_set' => array(
		'get' => false,
		'post' => false
	),
	'is_success' => array(
		'dbinsert_editLog' => false,
		'is_created' => false
	),
	'vals' => array(
		'u_uid' => null,
		'u_pwd' => null,
		'u_isop' => null
	),
	'debug' => array(
		'get' => null,
		'post' => null,
		'messages' => array(),
		'is_activesession' => $userUtil->isLogedIn(),
		'is_op' => null,
		'is_uidused' => null
	)
);

if (isset($_POST) && !empty($_POST)){
	$statusArr['is_set']['post'] = true;
	$statusArr['debug']['post'] = $_POST;
	foreach ($_POST as $key => $value) {
		$statusArr['vals'][$key] = $value;
	}
}
if (isset($_GET) && !empty($_GET)){
	$statusArr['is_set']['get'] = true;
	$statusArr['debug']['get'] = $_GET;
	foreach ($_GET as $key => $value) {
		$statusArr['vals'][$key] = $value;
	}
}

if ($userUtil->isLogedIn()){
	array_push($statusArr['debug']['messages'], 'You are loged in');
	if ($_SESSION['u_isop'] == true){
		array_push($statusArr['debug']['messages'], 'You do have permission to edit users');
		$statusArr['debug']['is_op'] = true;

		if (isset($_POST['u_uid'])&&(strlen($_POST['u_uid']) >= 4)){
			array_push($statusArr['debug']['messages'], 'User uid is provided: "'.$_POST['u_uid'].'"');
			if (isset($_POST['u_pwd'])&&(strlen($_POST['u_pwd']) >= 5)){
				array_push($statusArr['debug']['messages'], 'User pwd is provided: "'.$_POST['u_pwd'].'"');
				if (isset($_POST['u_isop'])&&($_POST['u_isop']==1||$_POST['u_isop']==0)){
					array_push($statusArr['debug']['messages'], 'User isop is provided: "'.$_POST['u_isop'].'"');

					if ( $stmt = $conn->prepare("SELECT `user_id` FROM `users` WHERE user_uid = ?") ){
						array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.edit.php -> SELECT user FROM users WHERE uid');
						$stmt->bind_param("s", $_POST['u_uid']);
						if ( $stmt->execute() ){
							array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.edit.php -> SELECT user FROM users WHERE uid');
							$stmt->store_result();
							if ($stmt->num_rows == 0){
								array_push($statusArr['debug']['messages'], 'Specified UID is not in use');


//
// START USER INSERT
//

								if ( $stmt = $conn->prepare("INSERT INTO `users` (`user_uid`, `user_pwd`, `user_is_op`) VALUES (?, ?, ?);")){
									array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.create.php -> INSERT INTO users');

									$temp1 = password_hash($_POST['u_pwd'], PASSWORD_DEFAULT);
									$temp2 = ($_POST['u_isop']==true) ? 1 : 0 ;

									$stmt->bind_param("sss", $_POST['u_uid'], $temp1, $temp2);

									if ($stmt->execute()){
										array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.create.php -> INSERT INTO users');
										$statusArr['is_success']['is_created'] = true;
									} else {
										array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/panel.user.create.php -> INSERT INTO users');
										(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
									}
								} else {
									array_push($statusArr['debug']['messages'], '/!\ Statement failed to prepare @ /ajax/panel.user.create.php -> INSERT INTO users');
									(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
								}

//
// END USER INSERT
//
//
// START LOG USER ACTION
//
								if ( $stmt = $conn->prepare("INSERT INTO `users_actions` (`action_user_id`, `action_action`, `action_time`, `action_ip`, `action_was_success`) VALUES (?, ?, ?, ?, ?);")){
									array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.edit.php -> INSERT INTO users_actions');

									$temp2 = 'panel_user_create';
									$temp3 = time();
									$temp4 = $ipUtil->getIpAddress();
									$temp5 = ($statusArr['is_success']['is_created']==true) ? 1 : 0 ;

									$stmt->bind_param("sssss", $_SESSION['u_id'], $temp2, $temp3, $temp4, $temp5);

									if ($stmt->execute()){
										array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.edit.php -> INSERT INTO users_actions');
										$statusArr['is_success']['dbinsert_editLog'] = true;
									} else {
										array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/panel.user.edit.php -> INSERT INTO users_actions');
										(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
									}
								} else {
									array_push($statusArr['debug']['messages'], '/!\ Statement failed to prepare @ /ajax/panel.user.edit.php -> INSERT INTO users_actions');
									(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
								}
//
// END LOG USER ACTION
//
							} else {
								array_push($statusArr['debug']['messages'], '/!\ Specified UID is already in use!');
							}
						} else {
							array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/panel.user.edit.php -> SELECT user FROM users WHERE id');
							(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
						}
					} else {
						array_push($statusArr['debug']['messages'], '/!\ Prepared statement failed @ /ajax/panel.user.edit.php -> SELECT user FROM users WHERE id');
						(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
					}

				} else {
					array_push($statusArr['debug']['messages'], '/!\ User isop is not correctly provided');
				}
			} else {
				array_push($statusArr['debug']['messages'], '/!\ User pwd is not correctly provided');
			}
		} else {
			array_push($statusArr['debug']['messages'], '/!\ User uid is not correctly provided');
		}

	} else {
		array_push($statusArr['debug']['messages'], '/!\ You do not have permission to edit users');
	}
} else {
	array_push($statusArr['debug']['messages'], '/!\ You are not loged in');
}
$conn->close();

Header('Content-type: application/json');
echo json_encode($statusArr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
