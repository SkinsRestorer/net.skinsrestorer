<?php

require_once __DIR__ . '/inc/util/firstload.php';
require_once __DIR__ . '/inc/util/user.php';
require_once __DIR__ . '/inc/includes.php';

$htmlUtil = new includes();
$userUtil = new user();

if ($userUtil->isLogedIn()){
	if (isset($_GET['par1'])&&$_GET['par1']==='login'){
		// User is loged in, but is viewing the login page - not okay, redirecting to panel home page
		Header('Location: /panel');
		exit();
	} else {
		// User is loged in and is not viewing the login page - everything is fine
	}
} else {
	if (isset($_GET['par1'])&&$_GET['par1']==='login'){
		// User is not loged in, but is viewing the login page - everything is fine
	} else {
		// User is not loged in and wants to view other pages - not fine, redirect to login page
		Header('Location: /panel/login');
		exit();
	}
}

?>
<!DOCTYPE html>
<html>
<head>

	<title>Skinsrestorer - panel</title>
	<meta name="description" content="Home page">
	<?php $htmlUtil->head(); ?>

</head>
<body>
<?php $htmlUtil->header(); ?>
<div id="loader-wrapper"></div>
<main>
	<?php $htmlUtil->navbar(); ?>
  	<div class="container">

		<?php

			{
				if (isset($_GET['par1'])&&!empty($_GET['par1'])){
					$path = __DIR__ . '/inc/pages/panel-'.$_GET['par1'].'.php';
					if (file_exists($path)){
						echo '<script>document.title="'.$_GET['par1'].' - Skinsrestorer panel";</script>';
						include_once $path;
					} else {
						echo '<script>document.title="Page \"'.$_GET['par1'].'\" not found - Skinsrestorer panel";</script>';
						include_once __DIR__ . '/inc/pages/error.php';
					}
				} else {
					echo '<script>document.title="Skinsrestorer panel";</script>';
					require_once __DIR__ . '/inc/pages/panel-index.php';
				}
			}

		?>

	</div>
</main>
<?php $htmlUtil->footer(); ?>
</body>
<?php $htmlUtil->foot(); ?>
</html>
