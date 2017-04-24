<?php
/**
 * @copyright Copyright (c) 2017 Robin Appelman <robin@icewind.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\UserInfo\Controller;

use OCA\UserInfo\UserInfo\UserInfoManager;
use OCP\AppFramework\Controller;
use OCP\IGroup;
use OCP\IGroupManager;
use OCP\IRequest;
use OCP\IUser;
use OCP\IUserManager;

class InfoController extends Controller {
	/** @var UserInfoManager */
	private $infoManager;

	private $groupManager;

	private $userManager;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param IGroupManager $groupManager
	 * @param IUserManager $userManager
	 * @param UserInfoManager $infoManager
	 */
	public function __construct($appName,
								IRequest $request,
								IGroupManager $groupManager,
								IUserManager $userManager,
								UserInfoManager $infoManager
	) {
		parent::__construct($appName, $request);

		$this->infoManager = $infoManager;
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
	}

	/**
	 * @NoCSRFRequired
	 */
	public function getUser($user) {
		if ($this->userManager->userExists($user) !== true) {
			return null;
		}

		$users = [];
		$users[] = $this->userManager->get($user);

		return $this->infoManager->getUsersInfo($users);
	}



	/**
	 * @NoCSRFRequired
	 */
	public function getUsers() {
		$users = [];
		$this->userManager->callForAllUsers(function (IUser $user) use (&$users) {
			$users[] = $user;
		});

		return $this->infoManager->getUsersInfo($users);
	}

	/**
	 * @NoCSRFRequired
	 */
	public function getGroups() {
		return array_map(function (IGroup $group) {
			return $group->getGID();
		}, $this->groupManager->search(''));
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @param string $groupId
	 * @return array
	 */
	public function getGroup($groupId) {
		$group = $this->groupManager->get($groupId);
		if (!$group) {
			return [];
		}

		return $this->infoManager->getUsersInfo($group->getUsers());
	}
}
