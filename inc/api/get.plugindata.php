<?php

if ($stmt = $conn->prepare("SELECT `release_id`, `release_version`, `release_type`, `release_filename`, `release_downloads`, `release_time` FROM `releases` ORDER BY `release_id` DESC;")) {
	array_push($statusData['debug']['messages'], 'Successfully querried data from the database');

	if ($stmt->execute()){
		$stmt->store_result();
		if ($stmt->num_rows >= 1){

			array_push($statusData['debug']['messages'], 'One or more release exists');
			$r = array();

			$stmt->bind_result($r['id'], $r['ver'], $r['type'], $r['filename'], $r['downloads'], $r['time']);
			while ( $stmt->fetch() ){

				$path = __DIR__ . '/../uploads/'.$r['uniqid'].'-'.$r['filename'];
				if( file_exists($path) ){
					$i = floor(log(filesize($path), 1024));
					$r['size'] = round(filesize($path) / pow(1024, $i), [0,0,2,2,3][$i]).['B','kB','MB','GB','TB'][$i];
				}

				array_push($statusData['data'], array(
					'id' => $r['id'],
					'ver' => $r['ver'],
					'type' => $r['type'],
					'filename' => $r['filename'],
					'size' => $r['size'],
					'downloads' => $r['downloads'],
					'time' => $r['time']
				));
			}

		} else {
			array_push($statusData['debug']['messages'], '/!\ We could not find any releases!');
		}
	} else {
		$statusData['debug']['error'] = true;
		array_push($statusData['debug']['messages'], '/!\ Prepared statement failed to execute get.plugindata.php @ SELECT data FROM releases');
	}

	/* close statement */
	$stmt->close();

} else {
	$statusData['debug']['error'] = true;
	array_push($statusData['debug']['messages'], '/!\ Prepared statement failed to prepare get.plugindata.php @ SELECT data FROM releases');
}
