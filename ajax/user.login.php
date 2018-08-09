<?php

require_once __DIR__ . '/../inc/util/firstload.php';
require_once __DIR__ . '/../inc/util/database.php';
require_once __DIR__ . '/../inc/util/user.php';

$dbutil = new dbManipulate();
$dbConn = new dbconn();
$ipUtil = new getUserIp();

$conn = $dbConn->oopmysqli();

$statusArr = array(

	// 'POST' => $_POST,

	'is_set' => array(
		'uid' => false,
		'pwd' => false
	),
	'is_correct' => array(
		'credentials' => false
	),
	'proceed' => array(
		'is_set' => true
	),
	'is_success' => array(
		'dbselect_user' => false,
		'dbinsert_loginlog' => false
	),
	'login' => false,
	'redirect' => '/panel/',
	'is_activesession' => false
);

if ( !isset($_SESSION['u_id']) ){
	{
			if ( isset($_POST['uid']) && !empty($_POST['uid']) ){
				$statusArr['is_set']['uid'] = true;
			} else {
				// 400
				// Geslo ni bilo podano
			}
			if ( isset($_POST['pwd']) && !empty($_POST['pwd']) ){
				$statusArr['is_set']['pwd'] = true;
			} else {
				// 400
				// Uporabniško ime ni bilo podano
			}
	}
	{
			if ( !in_array(false, $statusArr['is_set']) ){
				$statusArr['proceed']['is_set'] = true;
			} else {
				// 400
				// Ena od podanih informacij ni bila podana - preglej konzolo
			}
	}
	{
			if ( $statusArr['proceed']['is_set'] === true ){
				$uid = $_POST['uid'];
				$pwd = $_POST['pwd'];

				$actionsarr = array(
					'action_action' => 'login',
					'action_time' => time(),
					'action_ip' => $ipUtil->getIpAddress(),
					'action_user_id' => null,
					'action_was_success' => null
				);
				if ( $stmt = $conn->prepare("SELECT `user_id`, `user_uid`, `user_pwd` FROM `users` WHERE user_uid = ?") ){

					$stmt->bind_param("s", $uid);
					$stmt->execute();
					$stmt->store_result();
					if ($stmt->num_rows == 1){
						$statusArr['is_success']['dbselect_user'] = true;
						$stmt->bind_result($u_id, $u_uid, $u_pwd);
						$stmt->fetch();
						if ( password_verify($pwd, $u_pwd) ){


							$actionsarr['action_user_id'] = $u_id;
							$actionsarr['action_was_success'] = 1;
							if ( $dbutil->insert($actionsarr, 'users_actions') ){
								$statusArr['is_success']['dbinsert_loginlog'] = true;

								$_SESSION['u_id'] = $u_id;
								$_SESSION['u_uid'] = $u_uid;


								$statusArr['is_correct']['credentials'] = true;
								$statusArr['login'] = true;
							} else {
								// 500
								// Prepared statement failed
								Header('Content-type: application/json');
								die(json_encode(array(
									'error'=>'prepared statement failed at user.login.php -> insert($actionsarr, "users_actions") -> statement->prepare ! Please inform the administrator.'
								), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
							}

						} else {
							$actionsarr['action_user_id'] = $u_id;
							$actionsarr['action_was_success'] = 0;
							$dbutil->insert($actionsarr, 'users_actions');

							$statusArr['is_correct']['credentials'] = false;
						}
					} else {
						$statusArr['is_correct']['credentials'] = false;
					}
				} else {
					// 500
					// Prepared statement failed
					Header('Content-type: application/json');
					die(json_encode(array(
						'error'=>'prepared statement failed at user.login.php -> prepare("SELECT user") -> statement->prepare ! Prosimo obvestite administratorja.'
					), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				}
				$stmt->close();
			}
	}
} else {
	// 400
	// Uporabnik ima že aktivno sejo
	$statusArr['is_activesession'] = true;
}

$conn->close();

Header('Content-type: application/json');
echo json_encode($statusArr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
