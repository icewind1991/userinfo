<?php

namespace OCA\UserInfo\AppInfo;

use OCA\Provisioning_API\UserInfo\UserInfoManager;
use OCP\AppFramework\App;
use OCP\AppFramework\IAppContainer;

class Application extends App {
	public function __construct(array $urlParams = []) {
		parent::__construct('userinfo', $urlParams);

		$container = $this->getContainer();

		$container->registerService(UserInfoManager::class, function (IAppContainer $c) {
			$server = $c->getServer();
			return new UserInfoManager(
				$server->getUserManager()
			);
		});
	}
}
