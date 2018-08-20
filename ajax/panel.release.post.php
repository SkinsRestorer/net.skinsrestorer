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

$conf = array(
	'acceptExt' => array(
		'jar',
		'zip'
	)
);

$statusArr = array(

	'POST' => $_POST,
	'FILES' => $_FILES,

	'is_set' => array(
		'rel_title' => false,
		'rel_version' => false,
		'rel_type' => false,
		'rel_file' => false,
		'rel_content' => false
	),
	'is_correct' => array(
		'rel_title' => false,
		'rel_version' => false,
		'rel_type' => false,
		'rel_file' => false,
		'rel_content' => false
	),
	'proceed' => array(
		'is_set' => false,
		'is_correct' => false
	),
	'is_success' => array(
		'filemove_plugin' => false,
		'dbprepare_statement' => false,
		'dbinsert_release' => false,
		'dbinsert_postLog' => false
	),
	'is_posted' => false,
	'is_activesession' => $userUtil->isLogedIn()
);

if ( $userUtil->isLogedIn() ){
	{
			if ( isset($_POST['rel_title']) && !empty($_POST['rel_title']) ){
				$statusArr['is_set']['rel_title'] = true;

			} else {
				// 400
				// Title was not correctly provided
			}

            if ( isset($_POST['rel_version']) && !empty($_POST['rel_version']) ){
                $statusArr['is_set']['rel_version'] = true;

            } else {
                // 400
                // Version was not correctly provided
            }

            if ( isset($_POST['rel_type']) && !empty($_POST['rel_type']) ){
                $statusArr['is_set']['rel_type'] = true;

            } else {
                // 400
                // Type was not correctly provided
            }

            if ( isset($_FILES['rel_file']) && !empty($_FILES['rel_file']) ){
                $statusArr['is_set']['rel_file'] = true;

            } else {
                // 400
                // File was not correctly provided
            }

            if ( isset($_POST['rel_content']) && !empty($_POST['rel_content']) ){
                $statusArr['is_set']['rel_content'] = true;

            } else {
                // 400
                // Content was not correctly provided
            }
	}
    {
            if ( !in_array(false, $statusArr['is_set']) ) {
                $statusArr['proceed']['is_set'] = true;
			}
			{

                    if ( $statusArr['is_set']['rel_title'] && strlen($_POST['rel_title']) > 3 ){
        				$statusArr['is_correct']['rel_title'] = true;

        			} else {
        				// 400
        				// Title was not correctly provided
        			}

                    if ( $statusArr['is_set']['rel_version']  ){
        				$statusArr['is_correct']['rel_version'] = true;

        			} else {
        				// 400
        				// Title was not correctly provided
        			}

                    if ( $statusArr['is_set']['rel_type'] ){
        				$statusArr['is_correct']['rel_type'] = true;

        			} else {
        				// 400
        				// Title was not correctly provided
        			}

                    if (
						$statusArr['is_set']['rel_file'] &&
						!$_FILES['rel_file']['size'] == 0 &&
						!empty($_FILES['rel_file']['name']) &&
						!$_FILES['rel_file']['error'] > 0 &&
						in_array(pathinfo($_FILES['rel_file']['name'], PATHINFO_EXTENSION), $conf['acceptExt'])
					) {
                    	$statusArr['is_correct']['rel_file'] = true;

                    } else {
                        // 400
                        // Content was not correctly provided
                    }

                    if ( $statusArr['is_set']['rel_content'] ){
        				$statusArr['is_correct']['rel_content'] = true;

        			} else {
        				// 400
        				// Title was not correctly provided
        			}

            }
    }
    {
            if ( !in_array(false, $statusArr['is_correct']) ) {
                $statusArr['proceed']['is_correct'] = true;
            }
    }
    {
            if ( !in_array(false, $statusArr['proceed']) ) {

                $r_title = $_POST['rel_title'];
                $r_version = $_POST['rel_version'];
                $r_type = $_POST['rel_type'];
                $r_file = $_FILES['rel_file'];
				$r_file_name = basename($r_file['name']);
                $r_content = $_POST['rel_content'];

				$actionsarr = array(
					'action_action' => 'release_post',
					'action_time' => time(),
					'action_ip' => $ipUtil->getIpAddress(),
					'action_user_id' => $_SESSION['u_id'],
					'action_was_success' => false
				);


				{

					$r_uniqid = random_int(1000000000, 2147483647);
					$r_file_fullname = $r_uniqid.'-'.$r_file_name;
					$r_path = '../inc/uploads/'.$r_file_fullname;

					while ( file_exists($r_path) ) {
						$r_uniqid = random_int(1000000000, 2147483647);
						$r_path = '../inc/uploads/'.$r_uniqid.'-'.$r_file_name;
					}


					if ( move_uploaded_file($_FILES['rel_file']['tmp_name'], $r_path )) {
						$statusArr['is_success']['filemove_plugin'] = true;
						$statusArr['is_posted'] = true;

						if ( $stmt = $conn->prepare("INSERT INTO `releases` (`release_title`, `release_version`, `release_type`, `release_filename`, `release_uniqid`, `release_content`, `release_time`) VALUES (?, ?, ?, ?, ?, ?, ?);") ){

							$statusArr['is_success']['dbprepare_statement'] = true;

							$r_time = time();
							$stmt->bind_param("ssssssi", $r_title, $r_version, $r_type, $r_file_name, $r_uniqid, $r_content, $r_time);

							$stmt->execute();

							if ( $stmt->affected_rows == 1 ){
								$statusArr['is_success']['dbinsert_release'] = true;
								$actionsarr['action_was_success'] = true;


							} else {
								// 500
								// Prepared statement failed
								Header('Content-type: application/json');
								die(json_encode(array(
									'error'=>'Statement execution failed at user.login.php -> insert($releaseData, "releases") -> statement->execute ! The "rows_affected" does not equal to 1 ! Please inform the administrator.',
									'message'=> (isset($stmt->error)) ? $stmt->error : null
								), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
							}
						} else {
							// 500
							// Prepared statement failed
							Header('Content-type: application/json');
							die(json_encode(array(
								'error'=>'prepared statement failed at panel.release.post.php -> insert($releaseData, "releases") -> statement->prepare ! Please inform the administrator.',
								'message'=> (isset($stmt->error)) ? $stmt->error : null
							), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
						}

					} else {
						// 500
						// Prepared statement failed
						Header('Content-type: application/json');
						die(json_encode(array(
							'error'=>'Something went wrong at user.login.php -> move_uploaded_file() ! Please inform the administrator.'
						), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
					}

				}

				if ( $dbUtil->insert($actionsarr, 'users_actions') ){
					$statusArr['is_success']['dbinsert_postLog'] = true;

				} else {
					// 500
					// Prepared statement failed
					Header('Content-type: application/json');
					die(json_encode(array(
						'error'=>'prepared statement failed at panel.release.post.php -> insert($actionsarr, "users_actions") -> statement->prepare ! Please inform the administrator.',
						'message'=> (isset($stmt->error)) ? $stmt->error : null
					), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				}

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
