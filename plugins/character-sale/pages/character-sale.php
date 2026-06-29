<?php
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Mercado de Personagens';
$accountId = $logged ? (int)$account_logged->getId() : 0;
$action = $_POST['market_action'] ?? '';
$offerIdParam = isset($_GET['offer_id']) ? (int)$_GET['offer_id'] : 0;

function eclipseMarketOutfit(array $player): string
{
	return setting('core.outfit_images_url') . '?id=' . $player['looktype'] . '&addons=' . $player['lookaddons'] . '&head=' . $player['lookhead'] . '&body=' . $player['lookbody'] . '&legs=' . $player['looklegs'] . '&feet=' . $player['lookfeet'];
}

function eclipseMarketTimeAgo(?int $timestamp): string
{
	if (empty($timestamp)) return 'Nunca';
	$days = max(0, floor((time() - $timestamp) / 86400));
	if ($days === 0) return 'Hoje';
	if ($days === 1) return '1 dia atrás';
	return $days . ' dias atrás';
}

function eclipseMarketDuration(int $seconds): string
{
	if ($seconds <= 0) return '0h';
	$hours = floor($seconds / 3600);
	$minutes = floor(($seconds % 3600) / 60);
	return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'min' : '');
}

function eclipseMarketStamina(int $minutes): string
{
	return floor($minutes / 60) . 'h ' . str_pad((string)($minutes % 60), 2, '0', STR_PAD_LEFT) . 'min';
}

function eclipseMarketItemName(int $itemId): string
{
	return function_exists('getItemNameById') ? getItemNameById($itemId) : ('Item ' . $itemId);
}

function eclipseMarketOfferLink(int $offerId): string
{
	return rtrim(getLink('character-sale'), '/') . '/' . $offerId;
}

function eclipseMarketPercentValue($value, int $decimals = 2): string
{
	return rtrim(rtrim(number_format(((int)$value) / 100, $decimals, ',', '.'), '0'), ',') . '%';
}

function eclipseMarketBlessingsCount(array $offer): int
{
	$count = 0;
	for ($i = 1; $i <= 8; $i++) {
		$count += !empty($offer['blessings' . $i]) ? 1 : 0;
	}

	return max($count, (int)($offer['blessings'] ?? 0));
}

function eclipseMarketAddStat(array &$stats, string $label, $value, bool $show = true): void
{
	if ($show) {
		$stats[] = ['label' => $label, 'value' => $value];
	}
}

function eclipseMarketMeleeSkill(array $offer): array
{
	$skills = [
		'Club' => (int)$offer['skill_club'],
		'Sword' => (int)$offer['skill_sword'],
		'Axe' => (int)$offer['skill_axe'],
	];
	arsort($skills);
	$label = (string)array_key_first($skills);

	return ['label' => $label, 'value' => (int)$skills[$label]];
}

function eclipseMarketMainSkill(array $offer): array
{
	$vocation = (int)$offer['vocation'];
	if (in_array($vocation, [1, 2, 5, 6], true)) {
		return ['label' => 'Magic Level', 'value' => (int)$offer['maglevel']];
	}

	if (in_array($vocation, [3, 7], true)) {
		return ['label' => 'Distance', 'value' => (int)$offer['skill_dist']];
	}

	if (in_array($vocation, [9, 10], true)) {
		return ['label' => 'Fist', 'value' => (int)$offer['skill_fist']];
	}

	return eclipseMarketMeleeSkill($offer);
}

function eclipseMarketOfferHighlights(array $offer): array
{
	$mainSkill = eclipseMarketMainSkill($offer);
	$highlights = [
		$mainSkill,
		['label' => 'Charm Points', 'value' => (int)($offer['charm_points'] ?? 0)],
		['label' => 'Boss Points', 'value' => (int)($offer['boss_points'] ?? 0)],
	];

	$vocation = (int)$offer['vocation'];
	if (in_array($vocation, [1, 2, 5, 6, 9, 10], true)) {
		$highlights[] = ['label' => 'Mana Leech', 'value' => eclipseMarketPercentValue($offer['skill_mana_leech_amount'])];
	} elseif (in_array($vocation, [3, 4, 7, 8], true)) {
		$highlights[] = ['label' => 'Shielding', 'value' => (int)$offer['skill_shielding']];
	}

	return array_slice($highlights, 0, 4);
}

function eclipseMarketAchievementPoints($db, int $playerId): int
{
	if (!$db->hasTable('player_storage') || !defined('PLUGINS')) {
		return 0;
	}

	$achievementsFile = PLUGINS . 'theme-canary/achievements.php';
	if (!is_file($achievementsFile)) {
		return 0;
	}

	$points = 0;
	$achievements = require $achievementsFile;
	foreach ($achievements as $achievement => $value) {
		$storage = 300000 + (int)$achievement;
		$stmt = $db->prepare('SELECT `key` FROM `player_storage` WHERE `key` = ? AND `player_id` = ? LIMIT 1');
		$stmt->execute([$storage, $playerId]);
		if ($stmt->fetch()) {
			$points += (int)($value['points'] ?? 0);
		}
	}

	return $points;
}

if (isRequestMethod('post')) {
	csrfProtect();
	if (!$logged) {
		error('Você precisa entrar na sua conta para negociar personagens.');
	} else {
		try {
			if ($action === 'sell') {
				$playerId = (int)($_POST['player_id'] ?? 0);
				$price = (int)($_POST['price'] ?? 0);
				if ($price < 25 || $price > 100000) throw new RuntimeException('Informe um valor entre 25 e 100.000 coins.');
				$stmt = $db->prepare('SELECT p.id, p.name FROM players p LEFT JOIN players_online po ON po.player_id = p.id WHERE p.id = ? AND p.account_id = ? AND p.deletion = 0 AND po.player_id IS NULL');
				$stmt->execute([$playerId, $accountId]);
				$player = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$player) throw new RuntimeException('O personagem não pertence à sua conta ou está online.');
				$stmt = $db->prepare('INSERT INTO myaac_character_offers (player_id, seller_account_id, price) VALUES (?, ?, ?)');
				$stmt->execute([$playerId, $accountId, $price]);
				success($player['name'] . ' foi anunciado no mercado.');
			} elseif ($action === 'cancel') {
				$stmt = $db->prepare('DELETE FROM myaac_character_offers WHERE id = ? AND seller_account_id = ?');
				$stmt->execute([(int)($_POST['offer_id'] ?? 0), $accountId]);
				if (!$stmt->rowCount()) throw new RuntimeException('Oferta não encontrada.');
				success('Oferta cancelada.');
			} elseif ($action === 'buy') {
				$offerId = (int)($_POST['offer_id'] ?? 0);
				$db->beginTransaction();
				$stmt = $db->prepare('SELECT o.id, o.price, o.player_id, o.seller_account_id, p.name FROM myaac_character_offers o INNER JOIN players p ON p.id = o.player_id LEFT JOIN players_online po ON po.player_id = p.id WHERE o.id = ? AND p.account_id = o.seller_account_id AND p.deletion = 0 AND po.player_id IS NULL FOR UPDATE');
				$stmt->execute([$offerId]);
				$offer = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$offer) throw new RuntimeException('A oferta não está mais disponível ou o personagem está online.');
				if ((int)$offer['seller_account_id'] === $accountId) throw new RuntimeException('Você não pode comprar seu próprio personagem.');
				$stmt = $db->prepare('SELECT coins FROM accounts WHERE id = ? FOR UPDATE');
				$stmt->execute([$accountId]);
				$buyer = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$buyer || (int)$buyer['coins'] < (int)$offer['price']) throw new RuntimeException('Saldo de coins insuficiente.');
				$db->prepare('UPDATE accounts SET coins = coins - ? WHERE id = ?')->execute([(int)$offer['price'], $accountId]);
				$db->prepare('UPDATE accounts SET coins = coins + ? WHERE id = ?')->execute([(int)$offer['price'], (int)$offer['seller_account_id']]);
				$transfer = $db->prepare('UPDATE players SET account_id = ? WHERE id = ? AND account_id = ?');
				$transfer->execute([$accountId, (int)$offer['player_id'], (int)$offer['seller_account_id']]);
				if ($transfer->rowCount() !== 1) throw new RuntimeException('A propriedade do personagem mudou durante a compra.');
				$db->prepare('DELETE FROM myaac_character_offers WHERE id = ?')->execute([$offerId]);
				$db->commit();
				success($offer['name'] . ' agora pertence à sua conta.');
			}
		} catch (Throwable $exception) {
			if ($db->inTransaction()) $db->rollBack();
			if ($exception instanceof PDOException && (string)$exception->getCode() === '23000') error('Este personagem já está anunciado.');
			else error(htmlspecialchars($exception->getMessage()));
		}
	}
}

$hasCharmTable = $db->hasTableAndColumns('player_charms', ['player_id', 'charm_points']);
$charmSelect = $hasCharmTable ? 'COALESCE(pc.charm_points, 0) AS charm_points,' : '0 AS charm_points,';
$charmJoin = $hasCharmTable ? 'LEFT JOIN player_charms pc ON pc.player_id = p.id' : '';

$offerSql = 'SELECT o.id, o.price, o.seller_account_id, o.created_at,
	p.id AS player_id, p.name, p.level, p.vocation, p.health, p.healthmax, p.mana, p.manamax,
	p.maglevel, p.soul, p.cap, p.sex, p.town_id, p.lastlogin, p.lastlogout, p.onlinetime,
	p.stamina, p.experience, p.balance, p.boss_points, p.task_points, p.prey_wildcard,
	p.bonus_rerolls, p.created, p.comment, p.blessings, p.blessings1, p.blessings2, p.blessings3,
	p.blessings4, p.blessings5, p.blessings6, p.blessings7, p.blessings8, p.forge_dusts,
	p.forge_dust_level, p.manashield, p.max_manashield, p.xpboost_stamina, p.xpboost_value,
	p.skill_critical_hit_chance, p.skill_critical_hit_damage, p.skill_life_leech_chance,
	p.skill_life_leech_amount, p.skill_mana_leech_chance, p.skill_mana_leech_amount,
	p.skill_fist, p.skill_club, p.skill_sword, p.skill_axe, p.skill_dist, p.skill_shielding, p.skill_fishing,
	p.looktype, p.lookaddons, p.lookhead, p.lookbody, p.looklegs, p.lookfeet,
	' . $charmSelect . '
	t.name AS town_name, g.name AS guild_name, gr.name AS guild_rank, po.player_id AS online_id
	FROM myaac_character_offers o
	INNER JOIN players p ON p.id = o.player_id
	' . $charmJoin . '
	LEFT JOIN towns t ON t.id = p.town_id
	LEFT JOIN guild_membership gm ON gm.player_id = p.id
	LEFT JOIN guilds g ON g.id = gm.guild_id
	LEFT JOIN guild_ranks gr ON gr.id = gm.rank_id
	LEFT JOIN players_online po ON po.player_id = p.id
	WHERE p.account_id = o.seller_account_id AND p.deletion = 0';

if ($offerIdParam > 0) {
	$stmt = $db->prepare($offerSql . ' AND o.id = ? LIMIT 1');
	$stmt->execute([$offerIdParam]);
	$offer = $stmt->fetch(PDO::FETCH_ASSOC);
	if (!$offer) {
		error('Oferta não encontrada ou indisponível.');
		$twig->display('character-sale/views/detail.html.twig', ['offer' => null]);
		return;
	}

	$offer['vocation_name'] = config('vocations')[(int)$offer['vocation']] ?? 'Sem vocação';
	$offer['outfit'] = eclipseMarketOutfit($offer);
	$offer['detail_link'] = eclipseMarketOfferLink((int)$offer['id']);
	$offer['sex_name'] = ((int)$offer['sex'] === 0) ? 'Feminino' : 'Masculino';
	$offer['status_name'] = !empty($offer['online_id']) ? 'Online' : 'Offline';
	$offer['created_readable'] = !empty($offer['created']) ? date('d/m/Y', (int)$offer['created']) : 'Não informado';
	$offer['lastlogin_readable'] = eclipseMarketTimeAgo((int)$offer['lastlogin']);
	$offer['onlinetime_readable'] = eclipseMarketDuration((int)$offer['onlinetime']);
	$offer['stamina_readable'] = eclipseMarketStamina((int)$offer['stamina']);
	$offer['posted_readable'] = date('d/m/Y H:i', strtotime($offer['created_at']));
	$offer['xpboost_stamina_readable'] = eclipseMarketStamina((int)$offer['xpboost_stamina']);
	$offer['main_skill'] = eclipseMarketMainSkill($offer);
	$offer['melee_skill'] = eclipseMarketMeleeSkill($offer);

	$summaryStats = [];
	eclipseMarketAddStat($summaryStats, 'Nível', number_format((int)$offer['level'], 0, ',', '.'));
	eclipseMarketAddStat($summaryStats, 'Vocação', $offer['vocation_name']);
	eclipseMarketAddStat($summaryStats, 'Magic Level', number_format((int)$offer['maglevel'], 0, ',', '.'));
	eclipseMarketAddStat($summaryStats, 'Experiência', number_format((int)$offer['experience'], 0, ',', '.'));
	eclipseMarketAddStat($summaryStats, 'HP', $offer['health'] . '/' . $offer['healthmax']);
	eclipseMarketAddStat($summaryStats, 'Mana', $offer['mana'] . '/' . $offer['manamax']);
	eclipseMarketAddStat($summaryStats, 'Soul', number_format((int)$offer['soul'], 0, ',', '.'));
	eclipseMarketAddStat($summaryStats, 'Cap', number_format((int)$offer['cap'], 0, ',', '.'));
	eclipseMarketAddStat($summaryStats, 'Stamina', $offer['stamina_readable']);

	$infoStats = [];
	eclipseMarketAddStat($infoStats, 'Cidade', $offer['town_name'] ?: 'Não informada');
	eclipseMarketAddStat($infoStats, 'Guild', $offer['guild_name'] ? $offer['guild_rank'] . ' de ' . $offer['guild_name'] : 'Sem guild');
	eclipseMarketAddStat($infoStats, 'Sexo', $offer['sex_name']);
	eclipseMarketAddStat($infoStats, 'Criado em', $offer['created_readable']);
	eclipseMarketAddStat($infoStats, 'Último login', $offer['lastlogin_readable']);
	eclipseMarketAddStat($infoStats, 'Tempo online', $offer['onlinetime_readable']);
	eclipseMarketAddStat($infoStats, 'Anunciado em', $offer['posted_readable']);
	eclipseMarketAddStat($infoStats, 'Banco', number_format((int)$offer['balance'], 0, ',', '.') . ' gold');

	$progressStats = [];
	$achievementPoints = eclipseMarketAchievementPoints($db, (int)$offer['player_id']);
	$charmPoints = (int)($offer['charm_points'] ?? 0);
	eclipseMarketAddStat($progressStats, 'Boss Points', number_format((int)$offer['boss_points'], 0, ',', '.'));
	eclipseMarketAddStat($progressStats, 'Charm Points', number_format($charmPoints, 0, ',', '.'), $charmPoints > 0);
	eclipseMarketAddStat($progressStats, 'Achievement Points', number_format($achievementPoints, 0, ',', '.'), $achievementPoints > 0);
	eclipseMarketAddStat($progressStats, 'Task Points', number_format((int)$offer['task_points'], 0, ',', '.'));
	eclipseMarketAddStat($progressStats, 'Prey Wildcards', number_format((int)$offer['prey_wildcard'], 0, ',', '.'));
	eclipseMarketAddStat($progressStats, 'Bonus Rerolls', number_format((int)$offer['bonus_rerolls'], 0, ',', '.'));
	eclipseMarketAddStat($progressStats, 'Blessings', eclipseMarketBlessingsCount($offer) . '/8', eclipseMarketBlessingsCount($offer) > 0);
	eclipseMarketAddStat($progressStats, 'Exalted Dust', number_format((int)$offer['forge_dusts'], 0, ',', '.') . '/' . number_format((int)$offer['forge_dust_level'], 0, ',', '.'), (int)$offer['forge_dust_level'] > 0);
	eclipseMarketAddStat($progressStats, 'XP Boost', (int)$offer['xpboost_value'] . '% por ' . $offer['xpboost_stamina_readable'], (int)$offer['xpboost_value'] > 0 || (int)$offer['xpboost_stamina'] > 0);

	$combatStats = [];
	eclipseMarketAddStat($combatStats, 'Critical Chance', eclipseMarketPercentValue($offer['skill_critical_hit_chance']), (int)$offer['skill_critical_hit_chance'] > 0);
	eclipseMarketAddStat($combatStats, 'Critical Damage', eclipseMarketPercentValue($offer['skill_critical_hit_damage']), (int)$offer['skill_critical_hit_damage'] > 0);
	eclipseMarketAddStat($combatStats, 'Life Leech Chance', eclipseMarketPercentValue($offer['skill_life_leech_chance']), (int)$offer['skill_life_leech_chance'] > 0);
	eclipseMarketAddStat($combatStats, 'Life Leech Amount', eclipseMarketPercentValue($offer['skill_life_leech_amount']), (int)$offer['skill_life_leech_amount'] > 0);
	eclipseMarketAddStat($combatStats, 'Mana Leech Chance', eclipseMarketPercentValue($offer['skill_mana_leech_chance']), (int)$offer['skill_mana_leech_chance'] > 0);
	eclipseMarketAddStat($combatStats, 'Mana Leech Amount', eclipseMarketPercentValue($offer['skill_mana_leech_amount']), (int)$offer['skill_mana_leech_amount'] > 0);
	eclipseMarketAddStat($combatStats, 'Mana Shield', $offer['manashield'] . '/' . $offer['max_manashield'], (int)$offer['max_manashield'] > 0);

	$skills = [
		['label' => 'Fist', 'value' => $offer['skill_fist']],
		['label' => 'Club', 'value' => $offer['skill_club']],
		['label' => 'Sword', 'value' => $offer['skill_sword']],
		['label' => 'Axe', 'value' => $offer['skill_axe']],
		['label' => 'Distance', 'value' => $offer['skill_dist']],
		['label' => 'Shielding', 'value' => $offer['skill_shielding']],
		['label' => 'Fishing', 'value' => $offer['skill_fishing']],
	];

	$equipmentSlots = [
		1 => 'Head', 2 => 'Amulet', 3 => 'Backpack', 4 => 'Armor', 5 => 'Right Hand',
		6 => 'Left Hand', 7 => 'Legs', 8 => 'Feet', 9 => 'Ring', 10 => 'Ammo',
	];
	$stmt = $db->prepare('SELECT pid, itemtype, count FROM player_items WHERE player_id = ? AND pid BETWEEN 1 AND 10 ORDER BY pid');
	$stmt->execute([(int)$offer['player_id']]);
	$equipment = [];
	foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
		$itemId = (int)$item['itemtype'];
		$equipment[] = [
			'slot' => $equipmentSlots[(int)$item['pid']] ?? ('Slot ' . (int)$item['pid']),
			'id' => $itemId,
			'name' => eclipseMarketItemName($itemId),
			'count' => (int)$item['count'],
			'image' => setting('core.item_images_url') . $itemId . setting('core.item_images_extension'),
		];
	}

	$stmt = $db->prepare('SELECT time, level, killed_by, mostdamage_by, is_player FROM player_deaths WHERE player_id = ? ORDER BY time DESC LIMIT 5');
	$stmt->execute([(int)$offer['player_id']]);
	$deaths = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($deaths as &$death) {
		$death['date'] = date('d/m/Y H:i', (int)$death['time']);
	}

	$twig->display('character-sale/views/detail.html.twig', [
		'offer' => $offer,
		'summaryStats' => $summaryStats,
		'infoStats' => $infoStats,
		'progressStats' => $progressStats,
		'combatStats' => $combatStats,
		'skills' => $skills,
		'equipment' => $equipment,
		'deaths' => $deaths,
		'accountId' => $accountId,
	]);
	return;
}

$offers = $db->query($offerSql . ' ORDER BY o.created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
$vocationFilterOptions = [];
foreach ($offers as &$offer) {
	$offer['vocation_name'] = config('vocations')[(int)$offer['vocation']] ?? 'Sem vocação';
	$offer['outfit'] = eclipseMarketOutfit($offer);
	$offer['detail_link'] = eclipseMarketOfferLink((int)$offer['id']);
	$offer['status_name'] = !empty($offer['online_id']) ? 'Online' : 'Offline';
	$offer['posted_readable'] = date('d/m/Y H:i', strtotime($offer['created_at']));
	$offer['posted_sort'] = strtotime($offer['created_at']) ?: 0;
	$vocationFilterOptions[(int)$offer['vocation']] = $offer['vocation_name'];
	$offer['main_skill'] = eclipseMarketMainSkill($offer);
	$offer['melee_skill'] = eclipseMarketMeleeSkill($offer);
	$offer['skill_preview'] = eclipseMarketOfferHighlights($offer);
}
asort($vocationFilterOptions);

$accountPlayers = [];
if ($logged) {
	$stmt = $db->prepare('SELECT p.id, p.name, p.level, p.vocation FROM players p LEFT JOIN myaac_character_offers o ON o.player_id = p.id LEFT JOIN players_online po ON po.player_id = p.id WHERE p.account_id = ? AND p.deletion = 0 AND o.id IS NULL AND po.player_id IS NULL ORDER BY p.name');
	$stmt->execute([$accountId]);
	$accountPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($accountPlayers as &$player) $player['vocation_name'] = config('vocations')[(int)$player['vocation']] ?? 'Sem vocação';
}

$twig->display('character-sale/views/market.html.twig', [
	'offers' => $offers,
	'accountPlayers' => $accountPlayers,
	'accountId' => $accountId,
	'vocationFilterOptions' => $vocationFilterOptions,
]);
