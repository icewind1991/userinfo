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

namespace OCA\UserInfo\UserInfo;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IUser;
use OCP\IUserManager;

class UserInfoManager {
	/** @var IUserManager */
	private $userManager;

	private $connection;

	/**
	 * UserInfoManager constructor.
	 *
	 * @param IUserManager $userManager
	 */
	public function __construct(IUserManager $userManager, IDBConnection $connection) {
		$this->userManager = $userManager;
		$this->connection = $connection;
	}

	/**
	 * @param IUser[] $users
	 * @return array
	 */
	public function getUsersInfo(array $users) {
		$userIds = array_map(function (IUser $user) {
			return $user->getUID();
		}, $users);

		$usage = $this->getUsageInfo($userIds);
		$usedQuota = $this->getUsedQuota($userIds);

		$info = array_map(function (IUser $user) use ($usage, $usedQuota) {
			$lastLogin = new \DateTime();
			$lastLogin->setTimestamp($user->getLastLogin());
			return [
				'displayname' => $user->getDisplayName(),
				'enabled' => $user->isEnabled(),
				'quota' => $user->getQuota(),
				'total_space' => (key_exists($user->getUID(), $usage)) ? (int)$usage[$user->getUID()] : 0,
				'used_quota' => (key_exists($user->getUID(), $usedQuota)) ?(int)$usedQuota[$user->getUID()] : 0,
				'last_login' => $lastLogin->format(\DateTime::ATOM)
			];
		}, $users);

		return array_combine($userIds, $info);
	}

	public function getUsageInfo(array $userIds) {
		$query = $this->connection->getQueryBuilder();

		$query->select('user_id', $query->func()->sum('size'))
			->from('mounts', 'm')
			->innerJoin('m', 'filecache', 'f', $query->expr()->eq('m.root_id', 'f.fileid'))
			->where($query->expr()->in('user_id', $query->createNamedParameter($userIds, IQueryBuilder::PARAM_INT_ARRAY)))
			->groupBy('user_id');

		return $query->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
	}

	public function getUsedQuota(array $userIds) {
		$query = $this->connection->getQueryBuilder();

		$mountPoints = array_map(function ($userId) {
			return '/' . $userId . '/';
		}, $userIds);

		$pathHash = md5('files');

		$query->select('user_id', 'size')
			->from('mounts', 'm')
			->innerJoin('m', 'filecache', 'f', $query->expr()->eq('m.storage_id', 'f.storage'))
			->where($query->expr()->in('mount_point', $query->createNamedParameter($mountPoints, IQueryBuilder::PARAM_STR_ARRAY)))
			->andWhere($query->expr()->eq('path_hash', $query->createNamedParameter($pathHash)));

		return $query->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
	}
}
