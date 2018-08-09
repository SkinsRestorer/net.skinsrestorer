<?php

require_once __DIR__ . '/../inc/util/firstload.php';
require_once __DIR__ . '/../inc/util/database.php';
require_once __DIR__ . '/../inc/util/user.php';

$dbUtil = new dbManipulate();
$dbConn = new dbconn();
$userUtil = new user();
$ipUtil = new getUserIp();

$conn = $dbConn->oopmysqli();
$connPdo = $dbConn->pdo();

$statusArr = array(

	// 'POST' => $_POST,

	'is_set' => array(
		'id' => false
	),
	'is_correct' => array(
		'id' => false
	),
	'proceed' => array(
		'is_set' => false,
		'is_correct' => false
	),
	'is_success' => array(
		'dbselect_release' => false,
		'dbselect_drop' => false,
		'dbinsert_dropLog' => false
	),
	'is_dropped' => false,
	'is_activesession' => $userUtil->isLogedIn()
);

if ( $userUtil->isLogedIn() ){
	{
			if ( isset($_POST['id']) && !empty($_POST['id']) ){
				$statusArr['is_set']['id'] = true;
			} else {
				// 400
				// pwdNow was not provided
			}
	}
	if ($statusArr['is_set']['id']===true) {
			if (is_numeric($_POST['id'])){
				if ( $stmt = $conn->prepare("SELECT `release_id` FROM `releases` WHERE release_id = ?;") ){
					// Prepared statement is valid
					$stmt->bind_param("s", $_POST['id']);
					$stmt->execute();
					$stmt->store_result();
					if ($stmt->num_rows == 1){
						// Release was found in the database
						$statusArr['is_correct']['id'] = true;
						$statusArr['is_success']['dbselect_release'] = true;

					} else {
						// Something went wrong! Release ID can not be found in the database
						$statusArr['is_correct']['id'] = false;
						$statusArr['is_success']['dbselect_release'] = false;
					}
				} else {
					// Prepared statement failed...
					$statusArr['is_correct']['id'] = false;
					$statusArr['is_success']['dbselect_release'] = false;
				}
				$stmt->close();
			}
	}
	if (
		$statusArr['is_set']['id'] === true &&		// Check if the ID is set
		$statusArr['is_correct']['id'] === true		// Check if the release with this ID actually exists
	){

		$actionsarr = array(
			'action_action' => 'release_delete',
			'action_time' => time(),
			'action_ip' => $ipUtil->getIpAddress(),
			'action_user_id' => $_SESSION['u_id'],
			'action_was_success' => false
		);

		if ( $stmt = $conn->prepare("DELETE FROM `releases` WHERE release_id = ?;") ){
			// Prepared statement is valid
			$stmt->bind_param("s", $_POST['id']);
			if ( $stmt->execute() ){
				// Prepared statement was executed
				$statusArr['is_dropped'] = true;
				$actionsarr['action_was_success'] = true;

			} else {
				// Prepared statement was not executed
				$actionsarr['action_was_success'] = false;
				Header('Content-type: application/json');
				die(json_encode(array(
					'error'=>'Execution of a prepared statement failed at user.release.delete.php -> DELETE FROM -> statement->execute ! Please inform the administrator.',
					'message'=>$stmt->error
				), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
			}

		} else {
			// Prepared statement failed
			$actionsarr['action_was_success'] = false;
			Header('Content-type: application/json');
			die(json_encode(array(
				'error'=>'prepared statement failed at panel.release.delete.php -> DELETE FROM -> statement->prepare ! Please inform the administrator.',
				'message'=>$stmt->error
			), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

		}
		$stmt->close();

		if ( $dbUtil->insert($actionsarr, 'users_actions') ){
			$statusArr['is_success']['dbinsert_dropLog'] = true;

		} else {
			// 500
			// Prepared statement failed
			Header('Content-type: application/json');
			die(json_encode(array(
				'error'=>'prepared statement failed at panel.release.delete.php -> insert($actionsarr, "users_actions") -> statement->prepare ! Please inform the administrator.'
			), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
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
