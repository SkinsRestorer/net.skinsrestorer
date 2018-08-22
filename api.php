<?php
require_once __DIR__ . '/inc/util/firstload.php';
require_once __DIR__ . '/inc/util/database.php';
require_once __DIR__ . '/inc/util/user.php';

$dbUtil = new dbManipulate();
$connUtil = new dbconn();
$userUtil = new user();

$conn = $connUtil->oopmysqli();

$statusData = array(
	'isset' => array(
		'par1' => null,
		'par2' => null,
		'par3' => null,
		'get' => null,
		'post' => null,
		'session' => $userUtil->isLogedIn()
	),
	'exists' => array(
		'file' => null
	),
	'debug' => array(
		'time' => time(),
		'get' => null,
		'post' => null,
		'location' => '/api',
		'error' => false,
		'messages' => array()
	),
	'data' => array()
);

if (isset($_POST) && !empty($_POST)){
    $statusData['isset']['post'] = true;
} else {
    $statusData['isset']['post'] = false;
}

if (isset($_GET) && !empty($_GET)){
    $statusData['isset']['get'] = true;
    $statusData['debug']['get'] = $_GET;

	if (isset($_GET['par1'])&&!empty($_GET['par1'])){
		$statusData['isset']['par1'] = true;
		$statusData['debug']['location'] .= '/'.$_GET['par1'];

		if (isset($_GET['par2'])&&!empty($_GET['par2'])){
			$statusData['isset']['par2'] = true;
			$statusData['debug']['location'] .= '/'.$_GET['par2'];

			if (isset($_GET['par3'])&&!empty($_GET['par3'])){
				$statusData['isset']['par3'] = true;
				$statusData['debug']['location'] .= '/'.$_GET['par3'];

				$path = __DIR__ . '/inc/api/'.$_GET['par1'].'.'.$_GET['par2'].'.'.$_GET['par3'].'.php';
				if (file_exists($path)){
					include_once $path;
					$statusData['exists']['file'] = true;
				} else {
					/* FILE DOES NOT EXIST */
				}

			} else {
				/* PARAMETER 3 IS NOT DEFINED */
				$statusData['isset']['par3'] = false;

				$path = __DIR__ . '/inc/api/'.$_GET['par1'].'.'.$_GET['par2'].'.php';
				if (file_exists($path)){
					include_once $path;
					$statusData['exists']['file'] = true;
				} else {
					/* FILE DOES NOT EXIST */
					$statusData['exists']['file'] = false;
				}
			}
		} else {
			/* PARAMETER 2 IS NOT DEFINED */
			$statusData['isset']['par2'] = false;
		}

	} else {
		/* PARAMETER 1 IS NOT DEFINED */
		$statusData['isset']['par1'] = false;
	}

} else {
	/* NO $_GET DATA PASSED */
    $statusData['isset']['get'] = false;
}

/* close connection */
$conn->close();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
Header('Content-Type: application/json');
echo json_encode($statusData, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
