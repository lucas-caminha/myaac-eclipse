<?php
/**
 * Extended highscores page for Eclipse OT.
 *
 * Keeps the default MyAAC highscores behavior and exposes additional ranking
 * categories when the expected database columns exist in the target server.
 */

use MyAAC\Cache\Cache;
use MyAAC\Models\Player;
use MyAAC\Models\PlayerDeath;
use MyAAC\Models\PlayerKillers;
use MyAAC\Server\XML\Vocations;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Highscores';
$settingHighscoresCountryBox = setting('core.highscores_country_box');

if (config('account_country') && $settingHighscoresCountryBox) {
	require SYSTEM . 'countries.conf.php';
}

$highscoresTTL = setting('core.highscores_cache_ttl');
$list = urldecode($_GET['list'] ?? 'experience');
$page = $_GET['page'] ?? 1;
$vocation = urldecode($_GET['vocation'] ?? 'all');

if (!is_numeric($page) || $page < 1 || $page > PHP_INT_MAX) {
	$page = 1;
}

$configVocations = config('vocations');
$baseVocations = Vocations::getBase(false);

if (!function_exists('eclipseHighscoresHiddenPlayerIds')) {
	function eclipseHighscoresHiddenPlayerIds(): array
	{
		$hiddenIds = setting('core.highscores_ids_hidden');
		if (!is_array($hiddenIds)) {
			$hiddenIds = preg_split('/[^0-9]+/', (string) $hiddenIds, -1, PREG_SPLIT_NO_EMPTY);
		}

		return array_values(array_filter(array_map('intval', $hiddenIds)));
	}
}

$customRankingCandidates = [
	'charm-points' => [
		'label' => 'Bestiary (Charm Points)',
		'table' => 'player_charms',
		'columns' => ['charm_points'],
	],
	'achievement-points' => [
		'label' => 'Achievement Points',
		'table' => 'players',
		'columns' => ['achievement_points', 'achievements_points'],
	],
	'bosstiary-points' => [
		'label' => 'Bosstiary (Boss Points)',
		'table' => 'players',
		'columns' => ['bosstiary_points', 'boss_points'],
	],
	'task-points' => [
		'label' => 'Task Points',
		'table' => 'players',
		'columns' => ['task_points'],
	],
	'prey-wildcards' => [
		'label' => 'Prey Wildcards',
		'table' => 'players',
		'columns' => ['prey_wildcard', 'prey_wildcards'],
	],
	'forge-dusts' => [
		'label' => 'Forge Dusts',
		'table' => 'players',
		'columns' => ['forge_dusts'],
	],
];

$customRankings = [];

foreach ($customRankingCandidates as $key => $ranking) {
	if (!$db->hasTable($ranking['table'])) {
		continue;
	}

	foreach ($ranking['columns'] as $column) {
		if ($db->hasColumn($ranking['table'], $column)) {
			$ranking['column'] = $column;
			$customRankings[$key] = $ranking;
			break;
		}
	}
}

if ($db->hasTableAndColumns('eclipse_donation_intents', ['account_id', 'coins', 'status'])) {
	$customRankings['top-donators'] = [
		'label' => 'Top Donators',
		'table' => 'eclipse_donation_intents',
		'source' => 'donations',
	];
}

if ($db->hasTableAndColumns('accounts', ['creation', 'premdays', 'premdays_purchased'])) {
	$loyaltyCreationDay = (int) (configLua('loyaltyPointsPerCreationDay') ?? 1);
	$loyaltyPremiumSpent = (int) (configLua('loyaltyPointsPerPremiumDaySpent') ?? 4);
	$loyaltyPremiumPurchased = (int) (configLua('loyaltyPointsPerPremiumDayPurchased') ?? 4);
	$loyaltyExpression = '(GREATEST(FLOOR((UNIX_TIMESTAMP() - accounts.creation) / 86400), 0) * ' . $loyaltyCreationDay .
		' + GREATEST(accounts.premdays_purchased - accounts.premdays, 0) * ' . $loyaltyPremiumSpent .
		' + accounts.premdays_purchased * ' . $loyaltyPremiumPurchased . ')';
	$customRankings['loyalty-points'] = [
		'label' => 'Loyalty Points',
		'table' => 'accounts',
		'expression' => $loyaltyExpression,
		'condition' => 'accounts.creation > 0',
	];
}

$customRanking = $customRankings[$list] ?? null;
$skill = POT::SKILL__LEVEL;
$listResolved = $customRanking !== null;

if ($customRanking === null) {
	if (is_numeric($list)) {
		$list = (int) $list;
		if ($list >= POT::SKILL_FIRST && $list <= POT::SKILL__LAST) {
			$skill = $list;
			$listResolved = true;
		}
	} else {
		switch ($list) {
			case 'fist':
				$skill = POT::SKILL_FIST;
				$listResolved = true;
				break;
			case 'club':
				$skill = POT::SKILL_CLUB;
				$listResolved = true;
				break;
			case 'sword':
				$skill = POT::SKILL_SWORD;
				$listResolved = true;
				break;
			case 'axe':
				$skill = POT::SKILL_AXE;
				$listResolved = true;
				break;
			case 'distance':
				$skill = POT::SKILL_DIST;
				$listResolved = true;
				break;
			case 'shield':
				$skill = POT::SKILL_SHIELD;
				$listResolved = true;
				break;
			case 'fishing':
				$skill = POT::SKILL_FISH;
				$listResolved = true;
				break;
			case 'level':
			case 'experience':
				$skill = POT::SKILL__LEVEL;
				$listResolved = true;
				break;
			case 'magic':
				$skill = POT::SKILL__MAGLEVEL;
				$listResolved = true;
				break;
			case 'frags':
				if (setting('core.highscores_frags')) {
					$skill = SKILL_FRAGS;
					$listResolved = true;
				}
				break;
			case 'balance':
				if (setting('core.highscores_balance')) {
					$skill = SKILL_BALANCE;
					$listResolved = true;
				}
				break;
		}
	}

	if (!$listResolved) {
		$list = 'experience';
		$skill = POT::SKILL__LEVEL;
	}
}

$query = Player::query();
$vocationId = null;
$vocationIds = null;

if ($vocation !== 'all') {
	foreach ($configVocations as $id => $name) {
		if (strtolower($name) == $vocation) {
			$vocationId = $id;
			$add_vocs = [$id];

			while ($promotedVocation = Vocations::getPromoted($id)) {
				$id = $promotedVocation;
				$add_vocs[] = $promotedVocation;
			}

			$query->whereIn('players.vocation', $add_vocs);
			$vocationIds = $add_vocs;
			break;
		}
	}
}

$promotion = '';
if ($db->hasColumn('players', 'promotion')) {
	$promotion = ',players.promotion';
}

$outfit_addons = false;
$outfit = ', lookbody, lookfeet, lookhead, looklegs, looktype';
if ($db->hasColumn('players', 'lookaddons')) {
	$outfit .= ', lookaddons';
	$outfit_addons = true;
}

$configHighscoresPerPage = setting('core.highscores_per_page');
$limit = $configHighscoresPerPage + 1;
$highscores = [];
$needReCache = true;
$accountsJoined = false;

$query
	->withOnlineStatus()
	->whereNotIn('players.id', setting('core.highscores_ids_hidden'))
	->notDeleted()
	->where('players.group_id', '<', setting('core.highscores_groups_hidden'));

if ($customRanking !== null && ($customRanking['source'] ?? '') !== 'donations') {
	$hasCustomExpression = isset($customRanking['expression']);
	$customColumnReference = $hasCustomExpression ? null : $customRanking['table'] . '.' . $customRanking['column'];

	if ($customRanking['table'] === 'accounts') {
		$query->join('accounts', 'accounts.id', '=', 'players.account_id');
		$accountsJoined = true;
	} else if ($customRanking['table'] === 'player_charms') {
		$query->join('player_charms', 'player_charms.player_id', '=', 'players.id');
	}

	if ($hasCustomExpression) {
		$query->whereRaw($customRanking['expression'] . ' > 0');
	} else {
		$query->where($customColumnReference, '>', 0);
	}

	if (isset($customRanking['condition'])) {
		$query->whereRaw($customRanking['condition']);
	}
}

$totalResultsQuery = clone $query;
$customCacheKey = $customRanking !== null ? $list : $skill;
$cacheKey = 'highscores_v3_' . $customCacheKey . '_' . $vocation . '_' . $page . '_' . $configHighscoresPerPage;
$cache = Cache::getInstance();

if ($cache->enabled() && $highscoresTTL > 0) {
	$tmp = '';

	if ($cache->fetch($cacheKey, $tmp)) {
		$data = unserialize($tmp);
		$totalResults = $data['totalResults'];
		$highscores = $data['highscores'];
		$updatedAt = $data['updatedAt'];
		$needReCache = false;
	}
}

$offset = ($page - 1) * $configHighscoresPerPage;

if (empty($highscores) && $customRanking !== null && ($customRanking['source'] ?? '') === 'donations') {
	$deletionColumn = $db->hasColumn('players', 'deletion') ? 'deletion' : 'deleted';
	$hiddenPlayerIds = eclipseHighscoresHiddenPlayerIds();
	$groupLimit = (int) setting('core.highscores_groups_hidden');
	$playerConditions = [
		'p.`' . $deletionColumn . '` = 0',
		'p.`group_id` < ' . $groupLimit,
	];
	$subPlayerConditions = [
		'p2.`' . $deletionColumn . '` = 0',
		'p2.`group_id` < ' . $groupLimit,
		'p2.`account_id` = p.`account_id`',
	];

	if (!empty($hiddenPlayerIds)) {
		$hiddenList = implode(',', $hiddenPlayerIds);
		$playerConditions[] = 'p.`id` NOT IN (' . $hiddenList . ')';
		$subPlayerConditions[] = 'p2.`id` NOT IN (' . $hiddenList . ')';
	}

	if ($vocationIds !== null) {
		$vocationList = implode(',', array_map('intval', $vocationIds));
		$playerConditions[] = 'p.`vocation` IN (' . $vocationList . ')';
		$subPlayerConditions[] = 'p2.`vocation` IN (' . $vocationList . ')';
	}

	$donationTotalsSql = 'SELECT `account_id`, SUM(`coins`) AS `value` FROM `eclipse_donation_intents` WHERE `status` = ' . $db->quote('paid') . ' GROUP BY `account_id` HAVING `value` > 0';
	$bestPlayerSql = 'SELECT p2.`id` FROM `players` p2 WHERE ' . implode(' AND ', $subPlayerConditions) . ' ORDER BY p2.`experience` DESC, p2.`id` ASC LIMIT 1';
	$whereSql = implode(' AND ', $playerConditions) . ' AND p.`id` = (' . $bestPlayerSql . ')';
	$playerSelectColumns = str_replace('players.', 'p.', $outfit . $promotion);
	$onlineSelect = '0 AS `online`';
	$onlineJoin = '';
	if ($db->hasTable('players_online')) {
		$onlineSelect = 'IF(po.`player_id` IS NULL, 0, 1) AS `online`';
		$onlineJoin = 'LEFT JOIN `players_online` po ON po.`player_id` = p.`id` ';
	}

	$selectSql = 'SELECT accounts.`country`, p.`id`, p.`name`, p.`account_id`, p.`level`, p.`vocation`' . $playerSelectColumns . ', dt.`value`, ' . $onlineSelect . ' ' .
		'FROM (' . $donationTotalsSql . ') dt ' .
		'JOIN `players` p ON p.`account_id` = dt.`account_id` ' .
		'JOIN `accounts` accounts ON accounts.`id` = p.`account_id` ' .
		$onlineJoin .
		'WHERE ' . $whereSql . ' ' .
		'ORDER BY dt.`value` DESC, p.`experience` DESC, p.`id` ASC ' .
		'LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;

	$countSql = 'SELECT COUNT(*) FROM (' . $donationTotalsSql . ') dt ' .
		'JOIN `players` p ON p.`account_id` = dt.`account_id` ' .
		'WHERE ' . $whereSql;

	$highscores = array_map(function ($row) use ($configVocations) {
		$row['online'] = (int) $row['online'];
		$row['vocation'] = $configVocations[(int) $row['vocation']] ?? (string) $row['vocation'];
		$row['link'] = getPlayerLink($row['name'], false);

		return $row;
	}, $db->query($selectSql)->fetchAll(PDO::FETCH_ASSOC));

	$totalResults = (int) $db->query($countSql)->fetchColumn();
	$updatedAt = time();
}

if (($customRanking['source'] ?? '') !== 'donations') {
	if (!$accountsJoined) {
		$query->join('accounts', 'accounts.id', '=', 'players.account_id');
	}

	$query
		->limit($limit)
		->offset($offset)
		->selectRaw('accounts.country, players.id, players.name, players.account_id, players.level, players.vocation' . $outfit . $promotion)
		->orderByDesc('value');
}

if (empty($highscores) && (($customRanking['source'] ?? '') !== 'donations')) {
	if ($customRanking !== null) {
		$customValueExpression = $customRanking['expression'] ?? ('COALESCE(' . $customRanking['table'] . '.' . $customRanking['column'] . ', 0)');
		$query
			->selectRaw($customValueExpression . ' as value')
			->orderByDesc('players.experience');
	} else if ($skill == POT::SKILL__MAGLEVEL) {
		$query
			->addSelect('players.maglevel as value', 'players.maglevel')
			->orderByDesc('players.manaspent');
	} else if ($skill >= POT::SKILL_FIRST && $skill <= POT::SKILL__LAST) {
		$skill_ids = [
			POT::SKILL_FIST => 'skill_fist',
			POT::SKILL_CLUB => 'skill_club',
			POT::SKILL_SWORD => 'skill_sword',
			POT::SKILL_AXE => 'skill_axe',
			POT::SKILL_DIST => 'skill_dist',
			POT::SKILL_SHIELD => 'skill_shielding',
			POT::SKILL_FISH => 'skill_fishing',
		];

		$skillColumn = $skill_ids[$skill] ?? null;

		if ($skillColumn !== null && $db->hasColumn('players', $skillColumn)) {
			$query
				->addSelect($skillColumn . ' as value')
				->orderByDesc($skillColumn . '_tries');
		} else {
			$query
				->addSelect('players.level as value', 'players.experience')
				->orderByDesc('players.experience');
			$skill = POT::SKILL__LEVEL;
			$list = 'experience';
		}
	} else if ($skill == SKILL_FRAGS) {
		if ($db->hasTable('player_killers')) {
			$query->addSelect(['value' => PlayerKillers::whereColumn('player_killers.player_id', 'players.id')->selectRaw('COUNT(*)')]);
		} else {
			$query->addSelect(['value' => PlayerDeath::unjustified()->whereColumn('player_deaths.killed_by', 'players.name')->selectRaw('COUNT(*)')]);
		}
	} else if ($skill == SKILL_BALANCE) {
		$query->addSelect('players.balance as value');
	} else {
		$query
			->addSelect('players.level as value', 'players.experience')
			->orderByDesc('players.experience');
		$list = 'experience';
	}

	$highscores = $query->get()->map(function ($row) {
		/** @var Player $row */
		$tmp = $row->toArray();
		$tmp['online'] = $row->online_status;
		$tmp['vocation'] = $row->vocation_name;
		$tmp['outfit_url'] = $row->outfit_url;
		$tmp['link'] = getPlayerLink($row->name, false);
		unset($tmp['online_table']);

		return $tmp;
	})->toArray();

	$updatedAt = time();
	$totalResults = $totalResultsQuery->count();
}

if ($highscoresTTL > 0 && $cache->enabled() && $needReCache) {
	$cache->set($cacheKey, serialize([
		'totalResults' => $totalResults,
		'highscores' => $highscores,
		'updatedAt' => $updatedAt,
	]), $highscoresTTL * 60);
}

$show_link_to_next_page = false;
$i = 0;

foreach ($highscores as $id => &$player) {
	if (++$i <= $configHighscoresPerPage) {
		if ($customRanking !== null) {
			$player['value'] = number_format((int) $player['value']);
		} else if ($skill == POT::SKILL__MAGLEVEL) {
			$player['value'] = $player['maglevel'];
		} else if ($skill == POT::SKILL__LEVEL) {
			$player['value'] = $player['level'];
			$player['experience'] = number_format($player['experience']);
		}

		$player['flag'] = getFlagImage($player['country']);
		$player['outfit'] = '';

		if ($skill != POT::SKILL__LEVEL) {
			if (isset($lastValue) && $lastValue == $player['value']) {
				$player['rank'] = $lastRank;
			} else {
				$player['rank'] = $offset + $i;
			}

			$lastRank = $player['rank'];
			$lastValue = $player['value'];
		} else {
			$player['rank'] = $offset + $i;
		}
	} else {
		unset($highscores[$id]);
		$show_link_to_next_page = true;
		break;
	}
}

$linkPreviousPage = '';
if ($page > 1) {
	$linkPreviousPage = getLink('highscores') . '/' . $list . ($vocation !== 'all' ? '/' . $vocation : '') . '/' . ($page - 1);
}

$linkNextPage = '';
if ($show_link_to_next_page) {
	$linkNextPage = getLink('highscores') . '/' . $list . ($vocation !== 'all' ? '/' . $vocation : '') . '/' . ($page + 1);
}

$baseLink = getLink('highscores') . '/' . $list . ($vocation !== 'all' ? '/' . $vocation : '') . '/';
$types = [
	'experience' => 'Experience',
	'magic' => 'Magic',
	'shield' => 'Shielding',
	'distance' => 'Distance',
	'club' => 'Club',
	'sword' => 'Sword',
	'axe' => 'Axe',
	'fist' => 'Fist',
	'fishing' => 'Fishing',
];

if (setting('core.highscores_frags')) {
	$types['frags'] = 'Frags';
}

if (setting('core.highscores_balance')) {
	$types['balance'] = 'Balance';
}

foreach ($customRankings as $key => $ranking) {
	$types[$key] = $ranking['label'];
}

if ($highscoresTTL > 0 && $cache->enabled()) {
	echo '*Note: Highscores are updated every' . ($highscoresTTL > 1 ? ' ' . $highscoresTTL : '') . ' minute' . ($highscoresTTL > 1 ? 's' : '') . '.<br/>';
}

if ($customRanking !== null) {
	$skillName = $customRanking['label'];
	$levelName = $customRanking['label'];
} else if ($skill == POT::SKILL__MAGLEVEL) {
	$skillName = 'Magic Level';
	$levelName = 'Magic Level';
} else {
	$skillLabels = [
		POT::SKILL_FIST => 'Fist',
		POT::SKILL_CLUB => 'Club',
		POT::SKILL_SWORD => 'Sword',
		POT::SKILL_AXE => 'Axe',
		POT::SKILL_DIST => 'Distance',
		POT::SKILL_SHIELD => 'Shielding',
		POT::SKILL_FISH => 'Fishing',
		POT::SKILL__LEVEL => 'Level',
	];
	$skillName = ($skill == SKILL_FRAGS ? 'Frags' : ($skill == SKILL_BALANCE ? 'Balance' : ($skillLabels[$skill] ?? getSkillName($skill))));
	$levelName = $skill == POT::SKILL__LEVEL ? 'Level' : $skillName;
}

/** @var Twig\Environment $twig */
$twig->display('highscores.html.twig', [
	'highscores' => $highscores,
	'list' => $list,
	'skill' => $customRanking !== null ? $list : $skill,
	'skillName' => $skillName,
	'levelName' => $levelName,
	'vocation' => $vocation !== 'all' ? $vocation : null,
	'vocationId' => $vocationId,
	'baseVocations' => $baseVocations,
	'types' => $types,
	'linkPreviousPage' => $linkPreviousPage,
	'linkNextPage' => $linkNextPage,
	'totalResults' => $totalResults,
	'page' => $page,
	'baseLink' => $baseLink,
	'updatedAt' => $updatedAt,
]);
