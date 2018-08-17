<?php

$data = array(
	'releases' => array()
);

if ( isset($_GET['byid']) && is_numeric($_GET['byid']) ){

	if ($stmt = $conn->prepare("SELECT `release_id`, `release_title`, `release_version`, `release_type`, `release_filename`, `release_uniqid`, `release_content`, `release_downloads`, `release_time` FROM `releases` WHERE `release_id` = ?;")) {
		array_push($statusData['debug']['messages'], array(
			'query_latest' => array(
				'is_success' => true,
				'message' => 'Database query successful.'
			)
		));
		$stmt->bind_param("s", $_GET['byid']);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows == 1){

			array_push($statusData['debug']['messages'], 'We found the requested release');
			$r = array();

			$stmt->bind_result($r['id'], $r['title'], $r['ver'], $r['type'], $r['filename'], $r['uniqid'], $r['content'], $r['downloads'], $r['time']);
			while ( $stmt->fetch() ){

				$path = __DIR__ . '/../uploads/'.$r['uniqid'].'-'.$r['filename'];
				if( file_exists($path) ){
					$i = floor(log(filesize($path), 1024));
					$r['size'] = round(filesize($path) / pow(1024, $i), [0,0,2,2,3][$i]).['B','kB','MB','GB','TB'][$i];
				}

				array_push($data['releases'], array(
					'id' => $r['id'],
					'title' => $r['title'],
					'ver' => $r['ver'],
					'type' => $r['type'],
					'filename' => $r['filename'],
					'uniqid' => $r['uniqid'],
					'content' => $r['content'],
					'size' => $r['size'],
					'downloads' => $r['downloads'],
					'time' => $r['time']
				));
			}


		} else {
			array_push($statusData['debug']['messages'], '/!\ We could not find any releases OR we found multiple!');
		}


		/* close statement */
		$stmt->close();

	} else {
		array_push($statusData['debug']['messages'], '/!\ Error; prepared statement failed at get.php -> get latest release! Please inform an administrator');
	}

} else {

	if ($stmt = $conn->prepare("SELECT `release_id`, `release_title`, `release_version`, `release_type`, `release_filename`, `release_uniqid`, `release_content`, `release_downloads`, `release_time` FROM `releases` ORDER BY `release_id` DESC;")) {
		array_push($statusData['debug']['messages'], array(
			'query_latest' => array(
				'is_success' => true,
				'message' => 'Database query successful.'
			)
		));
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows >= 1){

			array_push($statusData['debug']['messages'], 'One or more release exists');
			$r = array();

			$stmt->bind_result($r['id'], $r['title'], $r['ver'], $r['type'], $r['filename'], $r['uniqid'], $r['content'], $r['downloads'], $r['time']);
			while ( $stmt->fetch() ){

				$path = __DIR__ . '/../uploads/'.$r['uniqid'].'-'.$r['filename'];
				if( file_exists($path) ){
					$i = floor(log(filesize($path), 1024));
					$r['size'] = round(filesize($path) / pow(1024, $i), [0,0,2,2,3][$i]).['B','kB','MB','GB','TB'][$i];
				}

				array_push($data['releases'], array(
					'id' => $r['id'],
					'title' => $r['title'],
					'ver' => $r['ver'],
					'type' => $r['type'],
					'filename' => $r['filename'],
					'uniqid' => $r['uniqid'],
					'content' => $r['content'],
					'size' => $r['size'],
					'downloads' => $r['downloads'],
					'time' => $r['time']
				));
			}


		} else {
			array_push($statusData['debug']['messages'], '/!\ We could not find any releases!');
		}


		/* close statement */
		$stmt->close();

	} else {
		array_push($statusData['debug']['messages'], '/!\ Error; prepared statement failed at get.php -> get latest release! Please inform an administrator');
	}

}


$statusData['data'] = $data;
