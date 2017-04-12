<?php
$application = new \OCA\UserInfo\AppInfo\Application();

$application->registerRoutes(
	$this,
	[
		'routes' => [
			[
				'name' => 'Info#getUsers',
				'url' => '/users',
				'verb' => 'GET'
			],
			[
				'name' => 'Info#getGroups',
				'url' => '/groups',
				'verb' => 'GET'
			],
			[
				'name' => 'Info#getGroup',
				'url' => '/groups/{groupId}',
				'verb' => 'GET'
			]
		],
	]
);
