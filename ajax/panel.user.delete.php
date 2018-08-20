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
		'is_deleted' => false
	),
	'vals' => array(
		'u_id' => null
	),
	'debug' => array(
		'get' => null,
		'post' => null,
		'messages' => array(),
		'is_activesession' => $userUtil->isLogedIn(),
		'is_op' => null,
		'deleting_self' => false
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

		if (isset($_POST['u_id']) && !empty($_POST['u_id']) && is_numeric($_POST['u_id'])){
			array_push($statusArr['debug']['messages'], 'User ID is correctly provided');
			if ($_POST['u_id'] != $_SESSION['u_id']){
				array_push($statusArr['debug']['messages'], 'Given ID does not match your ID :thumbsup:');
				if ( $stmt = $conn->prepare("SELECT `user_id` FROM `users` WHERE user_id = ?") ){
					array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.delete.php -> SELECT user FROM users WHERE id');
					$stmt->bind_param("s", $_POST['u_id']);
					if ( $stmt->execute() ){
						array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.delete.php -> SELECT user FROM users WHERE id');
						$stmt->store_result();
						if ($stmt->num_rows == 1){
							array_push($statusArr['debug']['messages'], 'User with specified ID exists');

	//
	// Start DROP user
	//
							if ( $stmt = $conn->prepare("DELETE FROM `users` WHERE user_id = ?") ){
								array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.delete.php -> DELETE FROM users WHERE id');
								$stmt->bind_param("s", $_POST['u_id']);
								if ( $stmt->execute() ){
									array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.delete.php -> DELETE FROM users WHERE id');
									if ($stmt->affected_rows == 1){
										array_push($statusArr['debug']['messages'], 'User with specified ID what successfully deleted');
										$statusArr['is_success']['is_deleted'] = true;
									} else {
										array_push($statusArr['debug']['messages'], '/!\ We could not delete the user with that ID');
									}
								} else {
									array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/panel.user.delete.php -> DELETE FROM users WHERE id');
									(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
								}
							} else {
								array_push($statusArr['debug']['messages'], '/!\ Prepared statement failed @ /ajax/panel.user.delete.php -> DELETE FROM users WHERE id');
								(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
							}

	//
	// End DROP user
	//
	//
	// START LOG USER ACTION
	//
							if ( $stmt = $conn->prepare("INSERT INTO `users_actions` (`action_user_id`, `action_action`, `action_time`, `action_ip`, `action_was_success`) VALUES (?, ?, ?, ?, ?);")){
								array_push($statusArr['debug']['messages'], 'Statement successfully prepared @ /ajax/panel.user.delete.php -> INSERT INTO users_actions');

								$temp2 = 'panel_user_delete';
								$temp3 = time();
								$temp4 = $ipUtil->getIpAddress();
								$temp5 = ($statusArr['is_success']['is_deleted']==true) ? 1 : 0 ;

								$stmt->bind_param("sssss", $_SESSION['u_id'], $temp2, $temp3, $temp4, $temp5);

								if ($stmt->execute()){
									array_push($statusArr['debug']['messages'], 'Statement successfully executed @ /ajax/panel.user.delete.php -> INSERT INTO users_actions');
									$statusArr['is_success']['dbinsert_editLog'] = true;
								} else {
									array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/panel.user.delete.php -> INSERT INTO users_actions');
									(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
								}
							} else {
								array_push($statusArr['debug']['messages'], '/!\ Statement failed to prepare @ /ajax/panel.user.delete.php -> INSERT INTO users_actions');
								(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
							}
	//
	// END LOG USER ACTION
	//
						} else {
							array_push($statusArr['debug']['messages'], '/!\ We could not find any used with that ID');
						}
					} else {
						array_push($statusArr['debug']['messages'], '/!\ Statement failed to execute @ /ajax/panel.user.delete.php -> SELECT user FROM users WHERE id');
						(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
					}
				} else {
					array_push($statusArr['debug']['messages'], '/!\ Prepared statement failed @ /ajax/panel.user.delete.php -> SELECT user FROM users WHERE id');
					(isset($stmt->error)) ? array_push($statusArr['debug']['messages'], $stmt->error) : null ;
				}
			} else {
				array_push($statusArr['debug']['messages'], '/!\ You can not delete yourself');
				$statusArr['debug']['deleting_self'] = true;
			}
		} else {
			array_push($statusArr['debug']['messages'], 'User ID is not correctly provided');
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
