<?php
/**
 * Extended online page for Eclipse OT.
 *
 * Keeps the default MyAAC online list and adds a richer server overview.
 */

use MyAAC\Cache\Cache;
use MyAAC\Models\ServerConfig;
use MyAAC\Models\ServerRecord;
use MyAAC\Server\XML\Vocations;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Quem esta online?';

if (setting('core.account_country')) {
	require SYSTEM . 'countries.conf.php';
}

if (!function_exists('eclipseOnlineFormatName')) {
	function eclipseOnlineFormatName(?string $name): string
	{
		$name = trim((string) $name);
		return $name === '' ? 'Indisponivel' : ucwords(strtolower($name));
	}
}

if (!function_exists('eclipseOnlineBoostedImage')) {
	function eclipseOnlineBoostedImage(array $row, bool $isBoss): string
	{
		$itemImage = (int) ($row['looktypeEx'] ?? 0);
		if ($isBoss && $itemImage !== 0) {
			return setting('core.item_images_url') . $itemImage . '.gif';
		}

		return setting('core.outfit_images_url') . '?id=' . (int) ($row['looktype'] ?? 0) .
			'&addons=' . (int) ($row['lookaddons'] ?? 0) .
			'&head=' . (int) ($row['lookhead'] ?? 0) .
			'&body=' . (int) ($row['lookbody'] ?? 0) .
			'&legs=' . (int) ($row['looklegs'] ?? 0) .
			'&feet=' . (int) ($row['lookfeet'] ?? 0) .
			'&mount=' . (int) ($row['lookmount'] ?? 0);
	}
}

if (!function_exists('eclipseOnlineGetBoosted')) {
	function eclipseOnlineGetBoosted($db): array
	{
		$boosted = [
			'boss' => null,
			'creature' => null,
		];

		if ($db->hasTable('boosted_boss')) {
			$boss = $db->query('SELECT `boostname`, `looktypeEx`, `looktype`, `lookfeet`, `looklegs`, `lookhead`, `lookbody`, `lookaddons`, `lookmount` FROM `boosted_boss` LIMIT 1')->fetch(PDO::FETCH_ASSOC);
			if ($boss) {
				$name = eclipseOnlineFormatName($boss['boostname']);
				$boosted['boss'] = [
					'name' => $name,
					'image' => eclipseOnlineBoostedImage($boss, true),
					'link' => getLink('bosses') . '?name=' . rawurlencode($name),
				];
			}
		}

		if ($db->hasTable('boosted_creature')) {
			$creature = $db->query('SELECT `boostname`, `looktype`, `lookfeet`, `looklegs`, `lookhead`, `lookbody`, `lookaddons`, `lookmount` FROM `boosted_creature` LIMIT 1')->fetch(PDO::FETCH_ASSOC);
			if ($creature) {
				$name = eclipseOnlineFormatName($creature['boostname']);
				$boosted['creature'] = [
					'name' => $name,
					'image' => eclipseOnlineBoostedImage($creature, false),
					'link' => getLink('monsters') . '?name=' . rawurlencode($name),
				];
			}
		}

		return $boosted;
	}
}

if (!function_exists('eclipseOnlineGetTopPlayers')) {
	function eclipseOnlineGetTopPlayers($db): array
	{
		$deletionColumn = $db->hasColumn('players', 'deletion') ? 'deletion' : 'deleted';
		$groupLimit = (int) setting('core.highscores_groups_hidden');
		$hiddenIds = setting('core.highscores_ids_hidden');
		if (!is_array($hiddenIds)) {
			$hiddenIds = preg_split('/[^0-9]+/', (string) $hiddenIds, -1, PREG_SPLIT_NO_EMPTY);
		}

		$where = [
			'`' . $deletionColumn . '` = 0',
			'`group_id` < ' . $groupLimit,
		];

		if (!empty($hiddenIds)) {
			$where[] = '`id` NOT IN (' . implode(',', array_map('intval', $hiddenIds)) . ')';
		}

		$players = [];
		$sql = 'SELECT `name`, `level`, `vocation`, `experience` FROM `players` WHERE ' . implode(' AND ', $where) . ' ORDER BY `experience` DESC, `level` DESC LIMIT 3';
		$configVocations = config('vocations');

		foreach ($db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $index => $player) {
			$vocation = (int) $player['vocation'];
			$players[] = [
				'rank' => $index + 1,
				'name' => $player['name'],
				'link' => getPlayerLink($player['name'], false),
				'level' => (int) $player['level'],
				'vocation' => $configVocations[$vocation] ?? (string) $vocation,
			];
		}

		return $players;
	}
}

if (!function_exists('eclipseOnlineGetLatestUpdate')) {
	function eclipseOnlineGetLatestUpdate($db): array
	{
		if ($db->hasTable('myaac_changelog')) {
			$row = $db->query('SELECT `body`, `date` FROM `myaac_changelog` WHERE `hide` = 0 ORDER BY `date` DESC, `id` DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
			if ($row) {
				return [
					'title' => $row['body'],
					'date' => (int) $row['date'],
					'link' => getLink('changelog'),
				];
			}
		}

		$changelogPath = BASE . 'docs/changelog.md';
		if (is_file($changelogPath)) {
			$lines = file($changelogPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			foreach ($lines as $line) {
				$line = trim($line);
				if (str_starts_with($line, '- ')) {
					return [
						'title' => substr($line, 2),
						'date' => null,
						'link' => getLink('changelog'),
					];
				}
			}
		}

		return [
			'title' => 'Nenhuma atualizacao publicada ainda.',
			'date' => null,
			'link' => getLink('changelog'),
		];
	}
}

if (!function_exists('eclipseOnlineEventDateInRange')) {
	function eclipseOnlineEventDateInRange(array $event, int $today): bool
	{
		$start = strtotime((string) ($event['startdate'] ?? ''));
		$end = strtotime((string) ($event['enddate'] ?? ''));

		return $start !== false && $end !== false && $today >= $start && $today <= $end;
	}
}

if (!function_exists('eclipseOnlineGetActiveEvent')) {
	function eclipseOnlineGetActiveEvent(): ?array
	{
		$eventsJson = config('data_path') . 'json/eventscheduler/events.json';
		$eventsXml = config('data_path') . 'XML/events.xml';
		$today = strtotime(date('m/d/Y'));

		if (is_file($eventsJson)) {
			$decoded = json_decode(file_get_contents($eventsJson), true);
			foreach (($decoded['events'] ?? []) as $event) {
				if (eclipseOnlineEventDateInRange($event, $today)) {
					return [
						'name' => $event['name'] ?? 'Evento ativo',
						'description' => $event['description'] ?? '',
						'link' => getLink('event-schedule'),
					];
				}
			}
		}

		if (is_file($eventsXml)) {
			$xml = simplexml_load_file($eventsXml);
			foreach (($xml->event ?? []) as $event) {
				$row = [
					'name' => (string) ($event['name'] ?? ''),
					'description' => (string) ($event->description['description'] ?? ''),
					'startdate' => (string) ($event['startdate'] ?? ''),
					'enddate' => (string) ($event['enddate'] ?? ''),
				];

				if (eclipseOnlineEventDateInRange($row, $today)) {
					return [
						'name' => $row['name'] ?: 'Evento ativo',
						'description' => $row['description'],
						'link' => getLink('event-schedule'),
					];
				}
			}
		}

		return null;
	}
}

if (!function_exists('eclipseOnlineGetServerSave')) {
function eclipseOnlineGetServerSave(): array
{
		global $config;

		$serverSave = (string) ($config['server_save'] ?? '05:00:00');
		$parts = array_map('intval', explode(':', $serverSave));
		$hour = $parts[0] ?? 5;
		$minute = $parts[1] ?? 0;
		$second = $parts[2] ?? 0;

		$now = new DateTime();
		$next = (clone $now)->setTime($hour, $minute, $second);
		if ($next <= $now) {
			$next->modify('+1 day');
		}

		return [
			'time' => sprintf('%02d:%02d', $hour, $minute),
			'next' => $next->format('d/m/Y H:i'),
		];
	}
}

$promotion = '';
if ($db->hasColumn('players', 'promotion')) {
	$promotion = '`promotion`,';
}

$order = $_GET['order'] ?? 'name_asc';
if (!in_array($order, ['country_asc', 'country_desc', 'name_asc', 'name_desc', 'level_asc', 'level_desc', 'vocation_asc', 'vocation_desc'])) {
	$order = 'name_asc';
} else if ($order == 'vocation_asc' || $order == 'vocation_desc') {
	$order = $promotion . 'vocation_' . (str_contains($order, 'asc') ? 'asc' : 'desc');
}

$cached = Cache::remember('online_rich_' . $order, setting('core.online_cache_ttl') * 60, function () use ($db, $promotion, $order) {
	$orderExplode = explode('_', $order);
	$orderSql = $orderExplode[0] . ' ' . $orderExplode[1];

	$skull_type = 'skull';
	if ($db->hasColumn('players', 'skull_type')) {
		$skull_type = 'skull_type';
	}

	$skull_time = 'skulltime';
	if ($db->hasColumn('players', 'skull_time')) {
		$skull_time = 'skull_time';
	}

	$outfit_addons = false;
	$outfit = ', lookbody, lookfeet, lookhead, looklegs, looktype';
	if ($db->hasColumn('players', 'lookaddons')) {
		$outfit .= ', lookaddons';
		$outfit_addons = true;
	}

	$vocations = array_map(function () {
		return 0;
	}, config('vocations'));

	if ($db->hasTable('players_online')) {
		$playersOnline = $db->query('SELECT `accounts`.`country`, `players`.`name`, `players`.`level`, `players`.`vocation`' . $outfit . ', `' . $skull_time . '` as `skulltime`, `' . $skull_type . '` as `skull` FROM `accounts`, `players`, `players_online` WHERE `players`.`id` = `players_online`.`player_id` AND `accounts`.`id` = `players`.`account_id` ORDER BY ' . $orderSql);
	} else {
		$playersOnline = $db->query('SELECT `accounts`.`country`, `players`.`name`, `players`.`level`, `players`.`vocation`' . $outfit . ', ' . $promotion . ' `' . $skull_time . '` as `skulltime`, `' . $skull_type . '` as `skull` FROM `accounts`, `players` WHERE `players`.`online` > 0 AND `accounts`.`id` = `players`.`account_id` ORDER BY ' . $orderSql);
	}

	$configVocations = config('vocations');

	$players = [];
	foreach ($playersOnline as $player) {
		$skull = '';
		if ($player['skulltime'] > 0) {
			if ($player['skull'] == 3) {
				$skull = ' <img style="border: 0;" src="images/white_skull.gif"/>';
			} elseif ($player['skull'] == 4) {
				$skull = ' <img style="border: 0;" src="images/red_skull.gif"/>';
			} elseif ($player['skull'] == 5) {
				$skull = ' <img style="border: 0;" src="images/black_skull.gif"/>';
			}
		}

		$player['vocation'] = OTS_Toolbox::getVocationFromPromotion($player['vocation'], $player['promotion'] ?? 0);

		$players[] = [
			'name' => getPlayerLink($player['name']),
			'player' => $player,
			'level' => $player['level'],
			'vocation' => $configVocations[$player['vocation']],
			'skull' => $skull,
			'country_image' => getFlagImage($player['country']),
			'outfit' => setting('core.outfit_images_url') . '?id=' . $player['looktype'] . ($outfit_addons ? '&addons=' . $player['lookaddons'] : '') . '&head=' . $player['lookhead'] . '&body=' . $player['lookbody'] . '&legs=' . $player['looklegs'] . '&feet=' . $player['lookfeet'],
		];

		$vocations[Vocations::getOriginal($player['vocation'])]++;
	}

	$record = '';
	if (count($players) > 0 && setting('core.online_record')) {
		$result = null;
		$timestamp = false;
		if ($db->hasTable('server_record')) {
			$timestamp = $db->hasColumn('server_record', 'timestamp');
			$serverRecordQuery = ServerRecord::query();

			if ($db->hasColumn('server_record', 'world_id')) {
				$serverRecordQuery->where('world_id', configLua('worldId'));
			}

			$result = $serverRecordQuery->orderByDesc('record')->first();
			if ($result) {
				$result = $result->toArray();
			}
		} else if ($db->hasTable('server_config')) {
			$row = ServerConfig::where('config', 'players_record')->first();
			if ($row) {
				$result = ['record' => $row->value];
			}
		}

		if ($result) {
			$record = $result['record'] . ' player' . ($result['record'] > 1 ? 's' : '') . ($timestamp ? ' (on ' . date('M d Y, H:i:s', $result['timestamp']) . ')' : '');
		}
	}

	return [
		'players' => $players,
		'record' => $record,
		'vocations' => $vocations,
		'overview' => [
			'boosted' => eclipseOnlineGetBoosted($db),
			'topPlayers' => eclipseOnlineGetTopPlayers($db),
			'latestUpdate' => eclipseOnlineGetLatestUpdate($db),
			'activeEvent' => eclipseOnlineGetActiveEvent(),
			'serverSave' => eclipseOnlineGetServerSave(),
		],
	];
});

$twig->display('online.html.twig', [
	'players' => $cached['players'],
	'record' => $cached['record'],
	'vocations' => $cached['vocations'],
	'vocs' => $cached['vocations'],
	'order' => $order,
	'baseVocations' => Vocations::getBase(false),
	'serverOverview' => $cached['overview'],
]);

$twig->display('characters.form.html.twig');
