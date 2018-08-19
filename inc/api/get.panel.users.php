<?php

if ( $userUtil->isLogedIn() ){
	array_push($statusData['debug']['messages'], 'You are loged in');
	if ($_SESSION['u_isop']==true){
		array_push($statusData['debug']['messages'], 'You have permission to list users');

		if ($stmt = $conn->prepare("SELECT `user_id`, `user_uid`, `user_is_op` FROM `users`;")) {
			array_push($statusData['debug']['messages'], 'Database query successful.');

			$stmt->execute();
			$stmt->store_result();
			if ($stmt->num_rows >= 1){

				$u = array();
				$statusData['data'] = array();

				array_push($statusData['debug']['messages'], 'Successfully querried users');
				$stmt->bind_result($u['id'], $u['uid'], $u['isop']);

				while ( $stmt->fetch() ){
					array_push($statusData['data'], array(
						'id' => $u['id'],
						'uid' => $u['uid'],
						'isop' => $u['isop']
					));
				}


			} else {
				array_push($statusData['debug']['messages'], '/!\ We were not able to query an users!');
			}

			/* close statement */
			$stmt->close();

		} else {
			array_push($statusData['debug']['messages'], '/!\ Error; prepared statement failed at get.php -> get latest release! Please inform an administrator');
		}

	} else {
		array_push($statusData['debug']['messages'], '/!\\ You do not have permission to list users');
	}
} else {
	array_push($statusData['debug']['messages'], '/!\\ You are not loged in');
}

?>
