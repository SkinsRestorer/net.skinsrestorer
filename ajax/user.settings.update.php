<?php

require_once __DIR__ . '/../inc/util/firstload.php';
require_once __DIR__ . '/../inc/util/database.php';
require_once __DIR__ . '/../inc/util/user.php';

$dbutil = new dbManipulate();
$dbConn = new dbconn();
$userUtil = new user();
$ipUtil = new getUserIp();

$conn = $dbConn->oopmysqli();

$statusArr = array(

	// 'POST' => $_POST,

	'is_set' => array(
		'pwdNow' => false,
		'pwd1' => false,
		'pwd2' => false
	),
	'is_correct' => array(
		'pwdNow' => false,
		'pwd1' => false,
		'pwd2' => false
	),
	'proceed' => array(
		'is_set' => false,
		'is_correct' => false
	),
	'is_success' => array(
		'dbselect_user' => false,
		'dbupdate_newpwd' => false,
		'dbinsert_newpwdLog' => false
	),
	'is_updated' => false,
	'redirect' => '/ajax/user.logout.php',
	'is_activesession' => $userUtil->isLogedIn()
);

if ( $userUtil->isLogedIn() ){
	{
			if ( isset($_POST['pwdNow']) && !empty($_POST['pwdNow']) ){
				$statusArr['is_set']['pwdNow'] = true;
			} else {
				// 400
				// pwdNow was not provided
			}
			if ( isset($_POST['pwd1']) && !empty($_POST['pwd1']) ){
				$statusArr['is_set']['pwd1'] = true;
			} else {
				// 400
				// pwd1 was not provided
			}
			if ( isset($_POST['pwd2']) && !empty($_POST['pwd2']) ){
				$statusArr['is_set']['pwd2'] = true;
			} else {
				// 400
				// pwd2 was not provided
			}
	}
	{
			if ( !in_array(false, $statusArr['is_set']) ){

					if ( $stmt = $conn->prepare("SELECT `user_id`, `user_pwd` FROM `users` WHERE user_id = ?") ){
						// Prepared statement is valid
						$stmt->bind_param("s", $_SESSION['u_id']);
						$stmt->execute();
						$stmt->store_result();
						if ($stmt->num_rows == 1){
							// User was found in the database
							$statusArr['is_success']['dbselect_user'] = true;
							$stmt->bind_result($u_id, $u_pwd);
							$stmt->fetch();

							$actionsarr = array(
								'action_action' => 'update_password',
								'action_time' => time(),
								'action_ip' => $ipUtil->getIpAddress(),
								'action_user_id' => $_SESSION['u_id'],
								'action_was_success' => null
							);

							if ( password_verify($_POST['pwdNow'], $u_pwd) ){
								// Provided pwdNow is correct
								$statusArr['is_correct']['pwdNow'] = true;
								$actionsarr['action_was_success'] = 1;
							} else {
								// Provided pwdNow is not correct
								$actionsarr['action_was_success'] = 0;
							}

							if ( $dbutil->insert($actionsarr, 'users_actions') ){
								$statusArr['is_success']['dbinsert_newpwdLog'] = true;

							} else {
								// 500
								// Prepared statement failed
								Header('Content-type: application/json');
								die(json_encode(array(
									'error'=>'prepared statement failed at user.settings.update.php -> insert($actionsarr, "update_password") -> statement->prepare ! Please inform the administrator.'
								), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
							}

						} else {
							// Something went very wrong! user ID in user's SESSION can not be found in the database
							$statusArr['is_success']['dbselect_user'] = false;
							$statusArr['redirect'] = '/ajax/user.logout.php';
						}
					} else {
						// Prepared statement failed...
						$statusArr['is_success']['dbselect_user'] = false;
					}

					if (
						($_POST['pwd1'] === $_POST['pwd2']) &&												// pwd1 and pwd2 must match each other
						(!strlen($_POST['pwd1'])<8 || !strlen($_POST['pwd1'])>64) &&						// Password length check
						(!preg_match("([^a-zA-Z0-9<>,.?!£$%^&*()_+={};:@#~\[\]\-\/\\\|])", $_POST['pwd1']))	// Password character check
					){
						$statusArr['is_correct']['pwd1'] = true;
						$statusArr['is_correct']['pwd2'] = true;
					}

			}
	}
	{
			if ( !in_array(false, $statusArr['is_correct'])	){
				$statusArr['proceed']['is_correct'] = true;
			} else {
				$statusArr['proceed']['is_correct'] = true;
				// 400
				// One of the required datas was not provided
			}
	}
	{
			if ( $statusArr['proceed']['is_correct'] === true ){
				$id = $_SESSION['u_id'];
				$pwd = password_hash($_POST['pwd1'], PASSWORD_DEFAULT);

				if ( $stmt = $conn->prepare("UPDATE `users` SET `user_pwd`=? WHERE user_id = ?") ){

					$stmt->bind_param("ss", $pwd, $_SESSION['u_id']);
					if ( $stmt->execute() ){
						// The password was successfully updated
						$statusArr['is_success']['dbupdate_newpwd'] = true;
						$statusArr['is_updated'] = true;

					} else {
						// 500
						// Execution of the prepared statement failed
						Header('Content-type: application/json');
						die(json_encode(array(
							'error'=>'Execution of the prepared statement failed at user.settings.update.php -> prepare("UPDATE user") -> statement->execute ! Please inform an administrator.'
						), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
					}

				} else {
					// 500
					// Prepared statement failed
					Header('Content-type: application/json');
					die(json_encode(array(
						'error'=>'prepared statement failed at user.settings.update.php -> prepare("UPDATE user") -> statement->prepare ! Please inform an administrator.'
					), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				}

				$stmt->close();
			}
	}
} else {
	// 400
	// User is not loged in
	$statusArr['is_activesession'] = false;
}

$conn->close();

Header('Content-type: application/json');
echo json_encode($statusArr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
