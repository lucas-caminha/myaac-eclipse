<?php
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Magias';

require_once PLUGINS . 'lua-spells/src/LuaSpell.php';
require_once PLUGINS . 'lua-spells/src/Models/LuaSpell.php';
require_once PLUGINS . 'lua-spells/src/Spells.php';

use MyAAC\Plugins\LuaSpells\Spells;
use MyAAC\Plugins\LuaSpells\Models\LuaSpell as LuaSpellModel;

$reload = isset($_POST['reload']) && (int)$_POST['reload'] === 1;

if($reload && admin()) {
	csrfProtect();
	Spells::reload(true);

	$totals = Spells::$totalsAdded;

	success("Magias atualizadas. Instantâneas: {$totals[Spells::TYPE_INSTANT]}, conjurações: {$totals[Spells::TYPE_CONJURE]}, runas: {$totals[Spells::TYPE_RUNE]}.");
}

if(admin()) {
	echo $twig->render('lua-spells/views/reload.html.twig');
}

if(isset($_GET['vocation_id'])) {
	$vocation_id = $_GET['vocation_id'];
	if($vocation_id == 'all') {
		$vocation = 'all';
	}
	else {
		$vocation_id = (int)$vocation_id;
		$vocation = config('vocations')[$vocation_id] ?? 'all';
	}
}
else {
	$vocation = (isset($_REQUEST['vocation']) ? urldecode($_REQUEST['vocation']) : 'all');

	if($vocation == 'all') {
		$vocation_id = 'all';
	}
	else {
		$vocation_ids = array_flip(config('vocations'));
		$vocation_id = $vocation_ids[$vocation];
	}
}

$order = 'name';
$spells = [];
$spells_db = LuaSpellModel::where('hide', '!=', 1)->where('type', '<', 4)->orderBy($order)->get();
$filterVocations = [
	1 => ['name' => 'Sorcerer', 'ids' => [1, 5]],
	2 => ['name' => 'Druid', 'ids' => [2, 6]],
	3 => ['name' => 'Paladin', 'ids' => [3, 7]],
	4 => ['name' => 'Knight', 'ids' => [4, 8]],
	9 => ['name' => 'Monk', 'ids' => [9, 10]],
];

if((string)$vocation_id != 'all') {
	$selectedVocationIds = $filterVocations[(int)$vocation_id]['ids'] ?? [(int)$vocation_id];
	foreach($spells_db as $spell) {
		$spell_vocations = json_decode($spell['vocations'], true);
		if(count(array_intersect($selectedVocationIds, $spell_vocations)) > 0 || count($spell_vocations) == 0) {
			$spell['vocations'] = null;
			$spells[] = $spell;
		}
	}
}
else {
	foreach($spells_db as $spell) {
		$vocations = json_decode($spell['vocations'], true);

		foreach($vocations as &$tmp_vocation) {
			if(isset($config['vocations'][$tmp_vocation]))
				$tmp_vocation = $config['vocations'][$tmp_vocation];
			else
				$tmp_vocation = 'Unknown';
		}

		$spell['vocations'] = implode('<br/>', $vocations);
		$spells[] = $spell;
	}
}

$showColumns = [
	'group' => LuaSpellModel::where('group', '!=', '')->count() > 0,
	'level' => LuaSpellModel::where('level', '>', 0)->count() > 0,
];

$twig->display('lua-spells/views/spells.html.twig', array(
	'isAdmin' => admin(),
	'post_vocation_id' => $vocation_id,
	'post_vocation' => $vocation,
	'filter_vocations' => $filterVocations,
	'spells' => $spells,
	'showColumns' => $showColumns,
));
