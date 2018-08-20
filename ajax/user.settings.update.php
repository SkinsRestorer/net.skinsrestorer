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
		'update' => false,
		'pwdNow' => false,
		'pwd1' => false,
		'pwd2' => false
	),
	'is_success' => array(
		'dbselect_user' => false,
		'dbupdate_newpwd' => false,
		'dbinsert_newpwdLog' => false
	),
	'debug' => array(
		'messages' => array()
	),
	'is_updated' => false,
	'redirect' => '/ajax/user.logout.php',
	'is_activesession' => $userUtil->isLogedIn()
);

if ( $userUtil->isLogedIn() ){
	if (isset($_POST['update']) && !empty($_POST['update'])){
		$statusArr['is_set']['update'] = true;
		if ($_POST['update']=='password'){

			$statusArr['debug']['isok_pwdNow'] = false;
			$statusArr['debug']['isok_pwd1'] = false;
			$statusArr['debug']['isok_pwd2'] = false;

			if ( isset($_POST['pwdNow']) && !empty($_POST['pwdNow']) ){
				$statusArr['is_set']['pwdNow'] = true;
				array_push($statusArr['debug']['messages'], '"pwdNow" was correctly provided');
			} else {
				// 400
				// pwdNow was not provided
				array_push($statusArr['debug']['messages'], '/!\ "pwdNow" was not correctly provided');
			}
			if ( isset($_POST['pwd1']) && !empty($_POST['pwd1']) ){
				$statusArr['is_set']['pwd1'] = true;
				array_push($statusArr['debug']['messages'], '"pwd1" was correctly provided');
			} else {
				// 400
				// pwd1 was not provided
				array_push($statusArr['debug']['messages'], '/!\ "pwd1" was not correctly provided');
			}
			if ( isset($_POST['pwd2']) && !empty($_POST['pwd2']) ){
				$statusArr['is_set']['pwd2'] = true;
				array_push($statusArr['debug']['messages'], '"pwd2" was correctly provided');
			} else {
				// 400
				// pwd2 was not provided
				array_push($statusArr['debug']['messages'], '/!\ "pwd2" was not correctly provided');
			}

			if (
				$statusArr['is_set']['pwdNow'] == true &&
				$statusArr['is_set']['pwd1'] == true &&
				$statusArr['is_set']['pwd2'] == true
			){
				array_push($statusArr['debug']['messages'], 'All required variables correctly provided');
				if ( $stmt = $conn->prepare("SELECT `user_id`, `user_pwd` FROM `users` WHERE user_id = ?") ){
					array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/user.settings.update.php -> SELECT FROM users WHERE id');
					$stmt->bind_param("s", $_SESSION['u_id']);
					if ($stmt->execute()){
						array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/user.settings.update.php -> SELECT FROM users WHERE id');
						$stmt->store_result();
						if ($stmt->num_rows == 1){
							array_push($statusArr['debug']['messages'], 'User exists in the database');
							// User was found in the database
							$statusArr['is_success']['dbselect_user'] = true;
							$stmt->bind_result($u_id, $u_pwd);
							$stmt->fetch();

							$actionsarr = array(
								'action_action' => 'settings_update_password',
								'action_time' => time(),
								'action_ip' => $ipUtil->getIpAddress(),
								'action_user_id' => $_SESSION['u_id'],
								'action_was_success' => null
							);

							if ( password_verify($_POST['pwdNow'], $u_pwd) ){
								// Provided pwdNow is correct
								$actionsarr['action_was_success'] = 1;
								$statusArr['debug']['isok_pwdNow'] = true;
								array_push($statusArr['debug']['messages'], 'pwdNow is correct');

								if ($_POST['pwd1'] === $_POST['pwd2']){
									if (strlen($_POST['pwd1']) >= 5){

										array_push($statusArr['debug']['messages'], 'New password is long enough');
										$statusArr['debug']['isok_pwd1'] = true;
										$statusArr['debug']['isok_pwd2'] = true;
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
												array_push($statusArr['debug']['messages'], '/!\ Execution of the prepared statement failed at user.settings.update.php -> prepare("UPDATE user") -> statement->execute');
												(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
											}
										} else {
											// 500
											// Prepared statement failed
											array_push($statusArr['debug']['messages'], '/!\ prepared statement failed at user.settings.update.php -> prepare("UPDATE user") -> statement->prepare');
											(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
										}
										$stmt->close();
									} else {
										array_push($statusArr['debug']['messages'], 'New password is too short');
									}
								} else {
									array_push($statusArr['debug']['messages'], 'pwd1 and pwd2 do not match each other');
								}
							} else {
								// Provided pwdNow is not correct
								$actionsarr['action_was_success'] = 0;
								array_push($statusArr['debug']['messages'], 'pwdNow is not correct');
							}

							if ( $dbutil->insert($actionsarr, 'users_actions') ){
								$statusArr['is_success']['dbinsert_newpwdLog'] = true;
								array_push($statusArr['debug']['messages'], 'Successfully inserted data into user activity log');
							} else {
								// 500
								// Prepared statement failed
								array_push($statusArr['debug']['messages'], '/!\ prepared statement failed at user.settings.update.php -> insert($actionsarr, "update_password") -> statement->prepare!');
							}

						} else {
							// Something went very wrong! user ID in user's SESSION can not be found in the database
							array_push($statusArr['debug']['messages'], '/!\ Something went very wrong! User ID in user\' SESSION can not be found in the database');
							$statusArr['is_success']['dbselect_user'] = false;
							$statusArr['redirect'] = '/ajax/user.logout.php';
						}
					} else {
						array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/user.settings.update.php -> SELECT FROM users WHERE id');
						(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
					}
				} else {
					// Prepared statement failed...
					$statusArr['is_success']['dbselect_user'] = false;
					array_push($statusArr['debug']['messages'], '/!\ Statement failed to prepare @ /ajax/user.settings.update.php -> SELECT FROM users WHERE id');
					(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
				}
			} else {
				array_push($statusArr['debug']['messages'], '/!\ Now all required variables correctly provided');
			}
		} else {
			// $_POST['update'] != 'password'
		}
	} else {
		array_push($statusArr['debug']['messages'], '/!\ "update" variable was not correctly provided');
	}
} else {
	array_push($statusArr['debug']['messages'], '/!\ You are not loged in');
}

$conn->close();

Header('Content-type: application/json');
echo json_encode($statusArr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
