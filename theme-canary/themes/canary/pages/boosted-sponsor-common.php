<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Account;

function eclipseBoostedSponsorPriceCoins(string $type): int
{
	return $type === 'creature' ? 300 : 250;
}

function eclipseBoostedSponsorEnv(string $name, ?string $fallback = null): ?string
{
	$value = getenv($name);
	return $value === false || $value === '' ? $fallback : $value;
}

function eclipseBoostedSponsorProfileComplete(Account $account): bool
{
	return strlen(trim((string)$account->rlname)) >= 3
		&& strlen(trim((string)$account->cpf)) >= 11
		&& !empty($account->birth_date);
}

function eclipseBoostedSponsorNextServerSaveDate(): DateTimeImmutable
{
	global $config;

	$serverSave = (string)($config['server_save'] ?? '05:00:00');
	$parts = array_map('intval', explode(':', $serverSave));
	$hour = $parts[0] ?? 5;
	$minute = $parts[1] ?? 0;
	$second = $parts[2] ?? 0;

	$timezone = new DateTimeZone(date_default_timezone_get() ?: 'America/Sao_Paulo');
	$now = new DateTimeImmutable('now', $timezone);
	$todayServerSave = $now->setTime($hour, $minute, $second);

	return $now < $todayServerSave ? $todayServerSave : $todayServerSave->modify('+1 day');
}

function eclipseBoostedSponsorFormatDate(DateTimeImmutable $date): string
{
	return $date->format('d/m/Y H:i');
}

function eclipseBoostedSponsorDateKey(DateTimeImmutable $date): string
{
	return $date->format('Y-m-d');
}

function eclipseBoostedSponsorBuildImage(array $monster): string
{
	$outfit = json_decode((string)($monster['outfit'] ?? ''), true) ?: [];
	if (!empty($outfit['lookTypeEx'])) {
		return setting('core.item_images_url') . $outfit['lookTypeEx'] . setting('core.item_images_extension');
	}
	if (!empty($outfit['lookType'])) {
		return setting('core.outfit_images_url') . '?id=' . $outfit['lookType'] . '&addons=' . ($outfit['lookAddons'] ?? 0) . '&head=' . ($outfit['lookHead'] ?? 0) . '&body=' . ($outfit['lookBody'] ?? 0) . '&legs=' . ($outfit['lookLegs'] ?? 0) . '&feet=' . ($outfit['lookFeet'] ?? 0) . '&mount=' . ($outfit['lookMount'] ?? 0);
	}
	return setting('core.monsters_images_url') . 'nophoto.png';
}

function eclipseBoostedSponsorMonsterSourceDirs(): array
{
	$configured = eclipseBoostedSponsorEnv('ECLIPSE_MONSTER_DATA_PATH');
	$dirs = [];

	if($configured) {
		$dirs[] = rtrim($configured, '/\\');
	}

	$dirs[] = 'D:/otserver/server/data-otservbr-global/monster';
	$dirs[] = '/opt/canary/data-otservbr-global/monster';
	$dirs[] = '/opt/otserver/server/data-otservbr-global/monster';
	$dirs[] = '/home/canary/data-otservbr-global/monster';
	$dirs[] = '/srv/canary/data-otservbr-global/monster';

	return array_values(array_unique($dirs));
}

function eclipseBoostedSponsorResolveMonsterSource(string $monsterName): ?array
{
	static $cache = [];

	if(isset($cache[$monsterName])) {
		return $cache[$monsterName];
	}

	foreach(eclipseBoostedSponsorMonsterSourceDirs() as $dir) {
		if(!is_dir($dir)) {
			continue;
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
		);

		foreach($iterator as $file) {
			if(strtolower($file->getExtension()) !== 'lua') {
				continue;
			}

			$content = @file_get_contents($file->getPathname());
			if($content === false || strpos($content, 'Game.createMonsterType("' . $monsterName . '")') === false) {
				continue;
			}

			$data = [];
			if(preg_match('/monster\\.raceId\\s*=\\s*(\\d+)/', $content, $match)) {
				$data['raceid'] = (int)$match[1];
			}
			if(empty($data['raceid']) && preg_match('/bossRaceId\\s*=\\s*(\\d+)/', $content, $match)) {
				$data['raceid'] = (int)$match[1];
			}
			if(preg_match('/lookTypeEx\\s*=\\s*(\\d+)/', $content, $match)) {
				$data['lookTypeEx'] = (int)$match[1];
			}
			if(preg_match('/lookType\\s*=\\s*(\\d+)/', $content, $match)) {
				$data['lookType'] = (int)$match[1];
			}
			if(preg_match('/lookHead\\s*=\\s*(\\d+)/', $content, $match)) {
				$data['lookHead'] = (int)$match[1];
			}
			if(preg_match('/lookBody\\s*=\\s*(\\d+)/', $content, $match)) {
				$data['lookBody'] = (int)$match[1];
			}
			if(preg_match('/lookLegs\\s*=\\s*(\\d+)/', $content, $match)) {
				$data['lookLegs'] = (int)$match[1];
			}
			if(preg_match('/lookFeet\\s*=\\s*(\\d+)/', $content, $match)) {
				$data['lookFeet'] = (int)$match[1];
			}
			if(preg_match('/lookAddons\\s*=\\s*(\\d+)/', $content, $match)) {
				$data['lookAddons'] = (int)$match[1];
			}
			if(preg_match('/lookMount\\s*=\\s*(\\d+)/', $content, $match)) {
				$data['lookMount'] = (int)$match[1];
			}

			$cache[$monsterName] = $data ?: null;
			return $cache[$monsterName];
		}
	}

	$cache[$monsterName] = null;
	return null;
}

function eclipseBoostedSponsorTypeLabel(string $type): string
{
	return $type === 'boss' ? 'Boss' : 'Creature';
}

function eclipseBoostedSponsorActiveSlot($db, string $type, string $scheduledForDate): ?array
{
	$stmt = $db->prepare(
		'SELECT * FROM eclipse_boosted_sponsorships
		WHERE target_type = :type AND scheduled_for_date = :scheduled
		  AND status IN ("paid", "applied")
		ORDER BY id ASC LIMIT 1'
	);
	$stmt->execute([
		':type' => $type,
		':scheduled' => $scheduledForDate,
	]);

	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	return $row ?: null;
}

function eclipseBoostedSponsorRecentApplied($db, string $type, int $limit = 6): array
{
	$stmt = $db->prepare(
		'SELECT target_name, target_category, scheduled_for_date, applied_at
		   FROM eclipse_boosted_sponsorships
		  WHERE target_type = :type AND status = "applied"
		  ORDER BY scheduled_for_date DESC, id DESC
		  LIMIT ' . (int)$limit
	);
	$stmt->execute([':type' => $type]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function eclipseBoostedSponsorCandidates($db, string $type, string $scheduledForDate): array
{
	$isBoss = $type === 'boss';
	$categoryColumn = $isBoss ? 'lm.bosstiary_class' : 'lm.bestiary_class';
	$typeFilter = $isBoss ? "lm.bosstiary_class != ''" : "lm.bosstiary_class = ''";

	$sql = 'SELECT lm.id, lm.name, lm.outfit, lm.health, lm.exp, ' . $categoryColumn . ' AS category
		FROM myaac_lua_monsters lm
		WHERE lm.hide != 1
		  AND ' . $typeFilter . '
		  AND NOT EXISTS (
			SELECT 1 FROM eclipse_boosted_sponsorships s
			WHERE s.target_type = :type
			  AND CONVERT(s.target_name USING utf8mb4) COLLATE utf8mb4_general_ci = lm.name COLLATE utf8mb4_general_ci
			  AND (
				s.status IN ("paid", "applied")
			  )
			  AND (
				s.scheduled_for_date = :scheduled
				OR s.cooldown_until >= :scheduled
			  )
		  )
		ORDER BY lm.name ASC';

	$stmt = $db->prepare($sql);
	$stmt->execute([
		':type' => $type,
		':scheduled' => $scheduledForDate,
	]);

	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
	foreach($rows as &$row) {
		$row['img_link'] = eclipseBoostedSponsorBuildImage($row);
	}

	return $rows;
}

function eclipseBoostedSponsorLoadTarget($db, string $type, int $targetId): ?array
{
	$isBoss = $type === 'boss';
	$typeFilter = $isBoss ? "lm.bosstiary_class != ''" : "lm.bosstiary_class = ''";
	$categoryColumn = $isBoss ? 'lm.bosstiary_class' : 'lm.bestiary_class';

	$stmt = $db->prepare(
		'SELECT lm.id, lm.name, lm.outfit, lm.health, lm.exp, ' . $categoryColumn . ' AS category, mm.id AS raceid
		   FROM myaac_lua_monsters lm
		   LEFT JOIN myaac_monsters mm ON mm.name = lm.name
		  WHERE lm.id = :id AND lm.hide != 1 AND ' . $typeFilter . '
		  LIMIT 1'
	);
	$stmt->execute([':id' => $targetId]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if(!$row) {
		return null;
	}

	$row['img_link'] = eclipseBoostedSponsorBuildImage($row);
	return $row;
}

function eclipseBoostedSponsorSlotConflict($db, string $type, string $scheduledForDate, int $ignoreId = 0): ?array
{
	$stmt = $db->prepare(
		'SELECT id, target_name FROM eclipse_boosted_sponsorships
		  WHERE target_type = :type
		    AND scheduled_for_date = :scheduled
		    AND status IN ("paid", "applied")
		    AND id != :ignore
		  ORDER BY id ASC LIMIT 1'
	);
	$stmt->execute([
		':type' => $type,
		':scheduled' => $scheduledForDate,
		':ignore' => $ignoreId,
	]);

	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	return $row ?: null;
}

function eclipseBoostedSponsorCooldownConflict($db, string $type, string $targetName, string $scheduledForDate, int $ignoreId = 0): ?array
{
	$stmt = $db->prepare(
		'SELECT id, scheduled_for_date, cooldown_until FROM eclipse_boosted_sponsorships
		  WHERE target_type = :type
		    AND target_name = :target
		    AND status IN ("paid", "applied")
		    AND cooldown_until >= :scheduled
		    AND id != :ignore
		  ORDER BY cooldown_until DESC LIMIT 1'
	);
	$stmt->execute([
		':type' => $type,
		':target' => $targetName,
		':scheduled' => $scheduledForDate,
		':ignore' => $ignoreId,
	]);

	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	return $row ?: null;
}

function eclipseBoostedSponsorApplyToBoostedTable($db, array $order, array $target): void
{
	$table = $order['target_type'] === 'boss' ? 'boosted_boss' : 'boosted_creature';
	$current = $db->query('SELECT `date` FROM `' . $table . '` LIMIT 1')->fetch(PDO::FETCH_ASSOC);
	$dateValue = $current['date'] ?? date('j');

	$outfit = json_decode((string)$target['outfit'], true) ?: [];
	$fallbackSource = empty($target['raceid']) ? eclipseBoostedSponsorResolveMonsterSource($target['name']) : null;

	$payload = [
		'boostname' => $target['name'],
		'date' => $dateValue,
		'raceid' => (string)($target['raceid'] ?? $fallbackSource['raceid'] ?? ''),
		'looktype' => (int)($outfit['lookType'] ?? $fallbackSource['lookType'] ?? 136),
		'lookfeet' => (int)($outfit['lookFeet'] ?? $fallbackSource['lookFeet'] ?? 0),
		'looklegs' => (int)($outfit['lookLegs'] ?? $fallbackSource['lookLegs'] ?? 0),
		'lookhead' => (int)($outfit['lookHead'] ?? $fallbackSource['lookHead'] ?? 0),
		'lookbody' => (int)($outfit['lookBody'] ?? $fallbackSource['lookBody'] ?? 0),
		'lookaddons' => (int)($outfit['lookAddons'] ?? $fallbackSource['lookAddons'] ?? 0),
		'lookmount' => (int)($outfit['lookMount'] ?? $fallbackSource['lookMount'] ?? 0),
	];

	if($table === 'boosted_boss') {
		$payload['looktypeEx'] = (int)($outfit['lookTypeEx'] ?? $fallbackSource['lookTypeEx'] ?? 0);
	}

	if($payload['raceid'] === '') {
		throw new RuntimeException('Could not resolve raceid for ' . $target['name'] . '. Configure ECLIPSE_MONSTER_DATA_PATH before applying boosted sponsorships.');
	}

	if($current) {
		$columns = array_keys($payload);
		$assignments = implode(', ', array_map(static function(string $column): string {
			return '`' . $column . '` = :' . $column;
		}, $columns));

		$stmt = $db->prepare('UPDATE `' . $table . '` SET ' . $assignments);
		$stmt->execute($payload);
		return;
	}

	$columns = array_map(static function(string $column): string {
		return '`' . $column . '`';
	}, array_keys($payload));
	$placeholders = array_map(static function(string $column): string {
		return ':' . $column;
	}, array_keys($payload));

	$stmt = $db->prepare(
		'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')'
	);
	$stmt->execute($payload);
}
