<?php
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Monstros';

require_once PLUGINS . 'lua-monsters/src/Models/LuaMonster.php';
require_once PLUGINS . 'lua-monsters/src/Monsters.php';

use MyAAC\Plugins\LuaMonsters\Monsters;
use MyAAC\Plugins\LuaMonsters\Models\LuaMonster as LuaMonsterModel;
use MyAAC\Timer;

function eclipseMonsterImage(array $monster): string
{
	$outfit = json_decode($monster['outfit'] ?? '', true) ?: [];
	if (!empty($outfit['lookTypeEx'])) {
		return setting('core.item_images_url') . $outfit['lookTypeEx'] . setting('core.item_images_extension');
	}
	if (!empty($outfit['lookType'])) {
		return setting('core.outfit_images_url') . '?id=' . $outfit['lookType'] . '&addons=' . ($outfit['lookAddons'] ?? 0) . '&head=' . ($outfit['lookHead'] ?? 0) . '&body=' . ($outfit['lookBody'] ?? 0) . '&legs=' . ($outfit['lookLegs'] ?? 0) . '&feet=' . ($outfit['lookFeet'] ?? 0);
	}
	return setting('core.monsters_images_url') . 'nophoto.png';
}

$reload = isset($_POST['reload']) && (int)$_POST['reload'] === 1;

if($reload && admin()) {
	csrfProtect();
	$timer = new Timer();

	Monsters::reload(true);

	$timeTotal = round($timer->elapsed(), 2);

	success("Monstros atualizados em $timeTotal segundos.");
}

if(admin()) {
	echo $twig->render('lua-monsters/views/reload.html.twig');
}

if (empty($_GET['name'])) {
	$categories = LuaMonsterModel::where('hide', '!=', 1)
		->where('rewardboss', '!=', 1)
		->where('bestiary_class', '!=', '')
		->selectRaw('bestiary_class AS name, COUNT(*) AS total, MIN(id) AS sample_id')
		->groupBy('bestiary_class')
		->orderBy('name')
		->get()
		->toArray();

	$bossQuery = LuaMonsterModel::where('hide', '!=', 1)->where('rewardboss', 1);
	$bossCount = $bossQuery->count();
	if ($bossCount > 0) {
		$bossSample = $bossQuery->orderBy('name')->first();
		$categories[] = ['name' => 'Chefes', 'total' => $bossCount, 'sample_id' => $bossSample->id];
	}

	$sampleIds = array_values(array_filter(array_column($categories, 'sample_id')));
	$categorySamples = LuaMonsterModel::whereIn('id', $sampleIds)->get()->keyBy('id');

	$requestedCategory = trim((string)($_GET['category'] ?? ''));
	$availableCategories = array_column($categories, 'name');
	$selectedCategory = in_array($requestedCategory, $availableCategories, true)
		? $requestedCategory
		: ($availableCategories[0] ?? 'Chefes');

	$query = LuaMonsterModel::where('hide', '!=', 1)->orderBy('name');
	if ($selectedCategory === 'Chefes') {
		$query->where('rewardboss', 1);
	} else {
		$query->where('rewardboss', '!=', 1)->where('bestiary_class', $selectedCategory);
	}
	$monsters = $query->get()->toArray();

	foreach($monsters as $key => &$monster) {
		$monster['img_link'] = eclipseMonsterImage($monster);
	}
	foreach ($categories as &$category) {
		$sample = $categorySamples->get((int)$category['sample_id']);
		$category['image'] = $sample ? eclipseMonsterImage($sample->toArray()) : '';
		$category['link'] = getLink('monsters') . '?category=' . rawurlencode($category['name']);
		unset($category['sample_id']);
	}

	$twig->display('lua-monsters/views/monsters.html.twig', array(
		'monsters' => $monsters,
		'categories' => $categories,
		'selectedCategory' => $selectedCategory,
		'isAdmin' => admin(),
	));

	return;
}

// display monster
$monster_name = urldecode(stripslashes(ucwords(strtolower($_GET['name']))));
$monsterModel = LuaMonsterModel::where('hide', '!=', 1)->where('name', $monster_name)->first();

if ($monsterModel && isset($monsterModel->name)) {
	/** @var array $monster */
	$monster = $monsterModel->toArray();

	function sort_by_chance($a, $b) {
		if ($a['chance'] == $b['chance']) {
			return 0;
		}
		return ($a['chance'] > $b['chance']) ? -1 : 1;
	}

	$title = $monster['name'] . " - Monstros";

	$outfit = json_decode($monster['outfit'], true);

	if (isset($outfit['lookTypeEx'])) {
		$monster['img_link'] = setting('core.item_images_url') . $outfit['lookTypeEx'] . setting('core.item_images_extension');
	}
	else {
		$monster['img_link'] = eclipseMonsterImage($monster);
	}

	$voices = json_decode($monster['voices'], true);
	$summons = json_decode($monster['summons'], true);
	$elements = json_decode($monster['elements'], true);
	$immunities = json_decode($monster['immunities'], true);
	$loot = json_decode($monster['loot'], true);
	if (!empty($loot)) {
		usort($loot, 'sort_by_chance');
	}

	foreach ($loot as &$item) {
		if (isset($item['id'])) {
			if ($item['id'] > 0) $item['name'] = getItemNameById($item['id']);
		}
		else {
			$item['id'] = 0;
		}

		$item['rarity_chance'] = round($item['chance'] / 1000, 2);
		$item['rarity'] = getItemRarity($item['chance']);
		$item['tooltip'] = ucfirst($item['name']) . '<br/>Chance: ' . $item['rarity'] . (setting('core.monsters_loot_percentage') ? ' ('. $item['rarity_chance'] .'%)' : '') . '<br/>Max count: ' . ($item['maxCount'] ?? 1);
	}

	$monster['loot'] = $loot ?? null;
	$monster['voices'] = $voices ?? null;
	$monster['summons'] = $summons ?? null;
	$monster['elements'] = $elements ?? null;
	$monster['immunities'] = $immunities ?? null;

	$twig->display('lua-monsters/views/monster.html.twig', array(
		'monster' => $monster,
	));

} else {
	error("O monstro <b>" . htmlspecialchars($monster_name) . "</b> não foi encontrado.");
}

// back button
$twig->display('lua-monsters/views/monsters.back_button.html.twig');
