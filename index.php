<?php

require_once __DIR__ . '/inc/util/firstload.php';
require_once __DIR__ . '/inc/util/database.php';
require_once __DIR__ . '/inc/includes.php';

$htmlUtil = new includes();

?>
<!DOCTYPE html>
<html>
<head>

	<title>Skinsrestorer</title>
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
					$path = __DIR__ . '/inc/pages/index-'.$_GET['par1'].'.php';
					if (file_exists($path)){
						echo '<script>document.title="'.$_GET['par1'].' - Skinsrestorer";</script>';
						include_once $path;
					} else {
						echo '<script>document.title="Page \"'.$_GET['par1'].'\" not found - Skinsrestorer";</script>';
						include_once __DIR__ . '/inc/pages/error.php';
					}
				} else {
					echo '<script>document.title="Skinsrestorer";</script>';
					require_once __DIR__ . '/inc/pages/index-index.php';
				}
			}

		?>

	</div>
</main>
<?php $htmlUtil->footer(); ?>
</body>
<?php $htmlUtil->foot(); ?>
</html>
