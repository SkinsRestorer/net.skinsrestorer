<?php

$data = array(
	'userdata' => array(
		'is_loged' => $userUtil->isLogedIn(),
		'uid' => null,
		'id' => null,
		'email' => null
	),
	'tabs' => array(
		array(
			'element' => 'a',
			'href' => 'https://skinsrestorer.net/',
			'materialicon' => 'home',
			'class' => 'red',
			'text' => 'Home page'
		),
		array(
			'element' => 'a',
			'href' => 'https://skinsrestorer.net/get/latest',
			'materialicon' => 'get_app',
			'class' => 'green',
			'text' => 'Get latest'
		),
		array(
			'element' => 'a',
			'href' => 'https://discord.gg/qHVch38',
			'target' => '_blank',
			'imgicon' => 'https://skinsrestorer.net/src/_media/icon_Discord-Logo-White.png',
			'class' => 'indigo darken-1',
			'text' => 'Discord'
		),
		array(
			'element' => 'a',
			'href' => 'https://www.spigotmc.org/resources/skinsrestorer.2124/',
			'target' => '_blank',
			'imgicon' => 'https://skinsrestorer.net/src/_media/icon_spigot2.png',
			'class' => 'orange darken-2',
			'text' => 'Spigot'
		),
		array(
			'element' => 'a',
			'href' => 'https://github.com/McLive/SkinsRestorerX',
			'target' => '_blank',
			'imgicon' => 'https://skinsrestorer.net/src/_media/icon_GitHub-Mark-Light-32px.png',
			'class' => 'blue-grey darken-4',
			'text' => 'Dev Github'
		)
	)
);

if ( $userUtil->isLogedIn() ){
	$data['userdata']['uid'] = $_SESSION['u_uid'];
	$data['userdata']['id'] = $_SESSION['u_id'];

	$temp1 = array(
			array(
				'element' => 'dropdown',
				'id' => 'navbarloged_dropdown_user',
				'materialicon' => 'face',
				'text' => $_SESSION['u_uid'],
				'class' => 'blue darken-1',
				'items' => array()
			)
		);

		if ( $_SESSION['u_isop'] == true ) {
			$temp1[0]['items'] = array_merge($temp1[0]['items'], array(
				array(
					'element' => 'a',
					'href' => 'https://skinsrestorer.net/panel/releases',
					'materialicon' => 'announcement',
					'text' => 'Releases'
				)
			));
		}

		if ( $_SESSION['u_isop'] == true ) {
			$temp1[0]['items'] = array_merge($temp1[0]['items'], array(
				array(
					'element' => 'a',
					'href' => 'https://skinsrestorer.net/devbox/errors.php',
					'materialicon' => 'bug_report',
					'target' => '_blank',
					'text' => 'Backend errors'
				)
			));
		}

		if ( $_SESSION['u_isop'] == true ) {
			$temp1[0]['items'] = array_merge($temp1[0]['items'], array(
				array(
					'element' => 'a',
					'href' => 'https://skinsrestorer.net/panel/users',
					'materialicon' => 'supervised_user_circle',
					'text' => 'Userlist'
				)
			));
		}

		if ( true ) {
			$temp1[0]['items'] = array_merge($temp1[0]['items'], array(
				array(
					'element' => 'a',
					'href' => 'https://skinsrestorer.net/panel/settings',
					'materialicon' => 'settings',
					'text' => 'Account settings'
				)
			));
		}

		if ( true ) {
			$temp1[0]['items'] = array_merge($temp1[0]['items'], array(
				array(
					'element' => 'a',
					'href' => 'https://skinsrestorer.net/ajax/user.logout.php',
					'materialicon' => 'exit_to_app',
					'text' => 'Logout'
				)
			));
		}


	$data['tabs'] = array_merge($data['tabs'], $temp1);
} else {
	$data['tabs'] = array_merge($data['tabs'],
		array(
			array(
				'element' => 'dropdown',
				'id' => 'navbarunloged_dropdown_loginsignup',
				'materialicon' => 'view_quilt',
				'class' => 'purple darken-2',
				'text' => 'Panel',
				'items' => array(
					array(
						'element' => 'a',
						'href' => 'https://skinsrestorer.net/panel/login',
						'materialicon' => 'vpn_key',
						'text' => 'Login'
					)
				)
			)
		)
	);
}


$statusData['data'] = $data;
