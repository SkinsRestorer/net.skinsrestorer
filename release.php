<?php

require_once __DIR__ . '/inc/util/firstload.php';
require_once __DIR__ . '/inc/util/database.php';
require_once __DIR__ . '/inc/includes.php';

$htmlUtil = new includes();

?>
<!DOCTYPE html>
<html>
<head>

	<title>Releases - Skinsrestorer</title>
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
				if (isset($_GET['id'])&&!empty($_GET['id'])){
					include_once __DIR__ . '/inc/pages/release-index.php';
				} else {
					echo '<script>window.location.replace("/");</script>';
				}
			}

		?>

	</div>
</main>
<?php $htmlUtil->footer(); ?>
</body>
<?php $htmlUtil->foot(); ?>
</html>
