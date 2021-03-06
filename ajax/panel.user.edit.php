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
		'is_edited' => false
	),
	'vals' => array(),
	'debug' => array(
		'get' => null,
		'post' => null,
		'messages' => array(),
		'is_activesession' => $userUtil->isLogedIn(),
		'is_op' => null,
		'uid_uidisused' => null
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
	if ($_SESSION['u_isop']){
		array_push($statusArr['debug']['messages'], 'You do have permission to edit users');
		$statusArr['debug']['is_op'] = true;
		if (isset($_POST['edit'])&&!empty($_POST['edit'])){
			array_push($statusArr['debug']['messages'], 'Edit variable is provided: "'.$_POST['edit'].'"');
			if (isset($_POST['u_id'])&&is_numeric($_POST['u_id'])){
				array_push($statusArr['debug']['messages'], 'User id is provided: "'.$_POST['u_id'].'"');
				if ( $stmt = $conn->prepare("SELECT `user_id` FROM `users` WHERE user_id = ?") ){
					array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.edit.php -> SELECT user FROM users WHERE id');
					$stmt->bind_param("s", $_POST['u_id']);
					if ( $stmt->execute() ){
						array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.edit.php -> SELECT user FROM users WHERE id');
						$stmt->store_result();
						if ($stmt->num_rows == 1){
							array_push($statusArr['debug']['messages'], 'User with specified ID exists');
//
// START EDITING SELECTION
//
// START EDITING ISOP
//
							if ($_POST['edit'] == 'isop'){
								array_push($statusArr['debug']['messages'], 'Editing: isop');
								{
									if ( $stmt = $conn->prepare("UPDATE `users` SET `user_is_op` = ? WHERE user_id = ?") ){
										array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.edit.php -> UPDATE user SET user_is_op WHERE id');
										$stmt->bind_param("is", $_POST['u_isop'], $_POST['u_id']);
										if ( $stmt->execute() ){
											array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.edit.php -> UPDATE user SET user_is_op WHERE id');
											$statusArr['is_success']['is_edited'] = true;
										} else {
											array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/panel.user.edit.php -> UPDATE user SET user_is_op WHERE id');
											(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
										}
									} else {
										array_push($statusArr['debug']['messages'], '/!\ Prepared statement failed @ /ajax/panel.user.edit.php -> UPDATE user SET user_is_op WHERE id');
										(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
									}
								}
//
// END EDITING ISOP
// START EDITING UID
//
							} elseif ( $_POST['edit'] == 'uid' ) {
								array_push($statusArr['debug']['messages'], 'Editing: uid');
								{

									if ( $stmt = $conn->prepare("SELECT `user_id` FROM `users` WHERE user_uid = ?") ){
										array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.edit.php -> SELECT user FROM users WHERE uid');
										$stmt->bind_param("s", $_POST['u_uid']);
										if ( $stmt->execute() ){
											array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.edit.php -> SELECT user FROM users WHERE uid');
											$stmt->store_result();
											if ($stmt->num_rows == 0){
												array_push($statusArr['debug']['messages'], 'Specified UID is not in use');
												$statusArr['debug']['uid_uidisused'] = false;

												if ( $stmt = $conn->prepare("UPDATE `users` SET `user_uid` = ? WHERE user_id = ?") ){
													array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.edit.php -> UPDATE user SET user_uid WHERE id');
													$stmt->bind_param("ss", $_POST['u_uid'], $_POST['u_id']);
													if ( $stmt->execute() ){
														array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.edit.php -> UPDATE user SET user_uid WHERE id');
														$statusArr['is_success']['is_edited'] = true;
													} else {
														array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/panel.user.edit.php -> UPDATE user SET user_uid WHERE id');
														(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
													}
												} else {
													array_push($statusArr['debug']['messages'], '/!\ Prepared statement failed @ /ajax/panel.user.edit.php -> UPDATE user SET user_uid WHERE id');
													(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
												}

											} else {
												array_push($statusArr['debug']['messages'], '/!\ Specified UID is already in use');
												$statusArr['debug']['uid_uidisused'] = true;
											}
										} else {
											array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/panel.user.edit.php -> SELECT user FROM users WHERE uid');
											(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
										}
									} else {
										array_push($statusArr['debug']['messages'], '/!\ Prepared statement failed @ /ajax/panel.user.edit.php -> SELECT user FROM users WHERE uid');
										(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
									}
								}
//
// END EDITING UID
// START EDITING PWD
//
							} elseif ( $_POST['edit'] == 'pwd' ) {
								array_push($statusArr['debug']['messages'], 'Editing: pwd');
								{
									$hashpwd = password_hash($_POST['u_pwd'], PASSWORD_DEFAULT);

									if ( $stmt = $conn->prepare("UPDATE `users` SET `user_pwd` = ? WHERE user_id = ?") ){
										array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.edit.php -> UPDATE user SET user_pwd WHERE id');
										$stmt->bind_param("ss", $hashpwd, $_POST['u_id']);
										if ( $stmt->execute() ){
											array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.edit.php -> UPDATE user SET user_pwd WHERE id');
											$statusArr['is_success']['is_edited'] = true;
										} else {
											array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/panel.user.edit.php -> UPDATE user SET user_pwd WHERE id');
											(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
										}
									} else {
										array_push($statusArr['debug']['messages'], '/!\ Prepared statement failed @ /ajax/panel.user.edit.php -> UPDATE user SET user_pwd WHERE id');
										(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
									}

								}
//
// END EDITING PWD
// START FALLBACK
//
							} else {
								array_push($statusArr['debug']['messages'], '/!\ Editing variable is unknown');
							}
//
// END FALLBACK
// END EDITING SELECTION
//
// START LOG USER ACTION
//
							if ( $stmt = $conn->prepare("INSERT INTO `users_actions` (`action_user_id`, `action_action`, `action_time`, `action_ip`, `action_was_success`) VALUES (?, ?, ?, ?, ?);")){
								array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.edit.php -> INSERT INTO users_actions');

								$temp2 = 'panel_user_edit_'.$_POST['edit'];
								$temp3 = time();
								$temp4 = $ipUtil->getIpAddress();
								$temp5 = ($statusArr['is_success']['is_edited']==true) ? 1 : 0 ;

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
							array_push($statusArr['debug']['messages'], '/!\ We could not find any used with that ID');
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
				array_push($statusArr['debug']['messages'], '/!\ User id is missing');
			}
		} else {
			array_push($statusArr['debug']['messages'], '/!\ Edit variable is missing');
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
