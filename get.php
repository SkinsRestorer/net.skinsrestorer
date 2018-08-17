<?php

require_once __DIR__ . '/inc/util/firstload.php';
require_once __DIR__ . '/inc/util/database.php';
require_once __DIR__ . '/inc/util/user.php';

$dbUtil = new dbManipulate();
$connUtil = new dbconn();
$userUtil = new user();

$conn = $connUtil->oopmysqli();

$progress = array();

function printAndEnd($data=false){
	global $progress;

	if ($data!=false){
		array_push($progress, $data);
	}
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	Header('Content-Type: application/json');
	echo json_encode($progress, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
	exit();
}

function fileDownload($filename='defi',$id='defi',$ver='defi',$uniqid='defi'){
	global $progress;
	global $conn;

	array_push($progress, 'Calling fileDownload function...');

	if ($filename=='defi'){
		printAndEnd('/!\ fileDownload() was not called correctly - file path was not passed');
	} elseif ($ver=='defi') {
		printAndEnd('/!\ fileDownload() was not called correctly - release version was not passed');
	} elseif ($id=='defi') {
		printAndEnd('/!\ fileDownload() was not called correctly - release id was not passed');
	} elseif ($uniqid=='defi') {
		printAndEnd('/!\ fileDownload() was not called correctly - release uniqid was not passed');
	} else {
		array_push($progress, 'fileDownload() was called correctly');
	}

	$path = __DIR__ . '/inc/uploads/'.$uniqid.'-'.$filename;


	if (!file_exists($path)){
		printAndEnd('/!\ path given in fileDownload() is incorrect - file does not exist :'.$path);
	} else {
		{
			// array_push($progress, 'File download forced - updating download statistics');
			// INCREASE DOWNLOAD STAT
			if ($stmt2 = $conn->prepare('UPDATE releases SET release_downloads=(release_downloads+1) WHERE release_id = ?;')) {
				$stmt2->bind_param("s", $id);
				$stmt2->execute();
				$stmt2->close();
			} else {
				printAndEnd('/!\ Error; prepared statement failed at get.php -> Increase release download count! Please inform an administrator');
			}
		}
		
		array_push($progress, 'File exists - forcing download');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="SkinsRestorer-'.$ver.'.jar"');
		header('Expires: 0');
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Content-Length: " . filesize($path));

        ob_clean();
        flush();
        readfile($path);


	}

}

if (isset($_GET) && !empty($_GET)){
	// $_GET DATA IS PASSED
	array_push($progress, '$_GET data was passed');

	if (isset($_GET['par1'])&&!empty($_GET['par1'])){
		// PARAMETER 1 IS DEFINED
		array_push($progress, 'parameter 1 [\'par1\'] is defined - '.$_GET['par1']);

		if ($_GET['par1']=='latest'){
			array_push($progress, 'parameter 1 [\'par1\'] has a value of "latest"');

			if ($stmt = $conn->prepare("SELECT `release_id`, `release_version`, `release_filename`, `release_uniqid` FROM `releases` WHERE `release_type` = 'stable' ORDER BY `release_id` DESC LIMIT 1;")) {
				array_push($progress, array(
					'query_latest' => array(
						'is_success' => true,
						'message' => 'Database query successful.'
					)
				));
				$stmt->execute();
				$stmt->store_result();
				if ($stmt->num_rows == 1){

					array_push($progress, 'Release exists');
					$r = array();

					$stmt->bind_result($r['id'], $r['ver'], $r['filename'], $r['uniqid']);
					if ($stmt->fetch()){
						array_push($progress, 'Successfully fetched release information');
						array_push($progress, array('Release data'=>$r));

						fileDownload($r['filename'], $r['id'], $r['ver'], $r['uniqid']);


					} else {
						printAndEnd('/!\ Failed to fetch release information!');
					}


				} else {
					printAndEnd('/!\ We could not find any releases!');
				}


				/* close statement */
				$stmt->close();

			} else {
				printAndEnd('/!\ Error; prepared statement failed at get.php -> get latest release! Please inform an administrator');
			}
			/* close connection */
			$conn->close();

		} else if (is_numeric($_GET['par1'])) {
			array_push($progress, 'parameter 1 [\'par1\'] is an intiger');

			if ($stmt = $conn->prepare("SELECT `release_id`, `release_version`, `release_filename`, `release_uniqid` FROM `releases` WHERE `release_id` = ?")) {

				array_push($progress, array(
					'query_byid' => array(
						'is_success' => true,
						'message' => 'Database query successful.'
					)
				));

				$stmt->bind_param("s", $_GET['par1']);
				$stmt->execute();
				$stmt->store_result();
				if ($stmt->num_rows == 1){

					array_push($progress, 'Release exists');
					$r = array();

					$stmt->bind_result($r['id'], $r['ver'], $r['filename'], $r['uniqid']);
					if ($stmt->fetch()){
						array_push($progress, 'Successfully fetched release information');
						array_push($progress, array('Release data'=>$r));

						fileDownload($r['filename'], $r['id'], $r['ver'], $r['uniqid']);

					} else {
						printAndEnd('/!\ Failed to fetch release information!');
					}


				} else {
					printAndEnd('/!\ Release with the given id does not exist!');
				}

				/* close statement */
				$stmt->close();

			} else {
				printAndEnd('/!\ Error; prepared statement failed at get.php -> get release by ID! Please inform an administrator');
			}
			/* close connection */
			$conn->close();

		} else {
			// PARAMETER 1 IS NOT 'LATEST' NOR AN INTIGER
			printAndEnd('/!\ parameter 1 [\'par1\'] is not "latest" nor an intiger');
		}

	} else {
		// PARAMETER 1 IS NOT DEFINED
		printAndEnd('/!\ parameter 1 [\'par1\'] is not defined');
	}

} else {
	// NO $_GET DATA PASSED
	printAndEnd('/!\ no $_GET data was passed');
}
