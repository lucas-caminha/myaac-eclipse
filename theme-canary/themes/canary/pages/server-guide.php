<?php
/**
 * New player guide for Eclipse OT.
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = t('server_guide.page_title');

function eclipseGuideEscape(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function eclipseGuideTranslate(string $key): string
{
	return t('server_guide.' . $key);
}

function eclipseGuideStepLabel(int $index): string
{
	return eclipseGuideTranslate('step_label') . ' ' . ($index + 1);
}

$imageBase = '/plugins/theme-canary/themes/canary/images/';
$itemImageBase = setting('core.item_images_url');
$itemImageExtension = setting('core.item_images_extension');

if ($itemImageExtension === '') {
	$itemImageExtension = '.gif';
}

$wikiImages = [
	'evonary' => 'https://tibiawiki.com.br/images/1/13/Evonary_art.png',
	'goldPouch' => 'https://static.wikia.nocookie.net/tibia/images/8/87/Gold_Pouch.gif/revision/latest?cb=20220214051441&path-prefix=en',
	'knightArtwork' => 'https://static.wikia.nocookie.net/tibia/images/7/71/Knight_Artwork.png/revision/latest?cb=20241120235803&path-prefix=en',
	'oberon' => 'https://static.wikia.nocookie.net/tibia/images/4/49/Grand_Master_Oberon.gif/revision/latest?cb=20180615195759&path-prefix=en',
	'storeChest' => 'https://static.wikia.nocookie.net/tibia/images/d/d1/Store_Chest.gif/revision/latest?cb=20230822174443&path-prefix=en',
];

$rewardItemIds = [
	'paladin armor' => 8063,
	'avenger' => 6527,
	'axe of destruction' => 27451,
	'bambus jo' => 50270,
	'batwing hat' => 9103,
	'blade of destruction' => 27449,
	'bow of destruction' => 27455,
	'chain bolter' => 8022,
	'composite hornbow' => 8027,
	'coned hat of enlightenment' => 50274,
	'crude umbral axe' => 20070,
	'crude umbral blade' => 20064,
	'crude umbral mace' => 20076,
	'dark vision bandana' => 50190,
	'dark whispers' => 29427,
	'depth claws' => 50176,
	'depth lorica' => 13994,
	'dragon robe' => 8039,
	'dream shroud' => 29423,
	'ectoplasmic shield' => 29430,
	'eldritch crescent moon spade' => 50170,
	'elite draken helmet' => 11689,
	'enchanted theurgic amulet' => 30402,
	'fabulous legs' => 32617,
	'fireborn giant armor' => 8053,
	'furious frock' => 19391,
	'gnomish footwraps' => 50290,
	'iks footwraps' => 50291,
	'jungle bow' => 35518,
	'jungle quiver' => 35524,
	'jungle rod' => 35521,
	'jungle survivor legs' => 50186,
	'jungle wand' => 35522,
	'legs of enlightenment' => 50269,
	'legs of wisdom' => 50187,
	'mace of destruction' => 27453,
	"master archer's armor" => 8060,
	'mercenary sword' => 7386,
	'merudri nanbando' => 50261,
	'merudri scale mail' => 50263,
	'nunchaku' => 50182,
	'nunchaku of destruction' => 50168,
	'nunchaku of enlightenment' => 50273,
	'ornate chestplate' => 13993,
	'ornate legs' => 13999,
	'ornate shield' => 14000,
	'prismatic armor' => 16110,
	'prismatic helmet' => 16109,
	'prismatic legs' => 16111,
	'rift bow' => 22866,
	'rift crossbow' => 22867,
	'robe of enlightenment' => 50268,
	'robe of the ice queen' => 8038,
	'rod of destruction' => 27458,
	'royal scale robe' => 11687,
	'sai of enlightenment' => 50272,
	"snake god's wristguard" => 11691,
	'soulful legs' => 32618,
	'spellbook of ancient arcana' => 14769,
	'spellbook of lost souls' => 8075,
	'spellbook of mind control' => 8074,
	'spellbook of vigilance' => 16107,
	'spellbook of warding' => 8073,
	'spirit guide' => 29431,
	'thaian sword' => 7391,
	'titan axe' => 7413,
	'twin axe' => 3335,
	'umbral master axe' => 20072,
	'umbral master bow' => 20084,
	'umbral master crossbow' => 20087,
	'umbral master katar' => 50165,
	'umbral master mace' => 20078,
	'umbral master spellbook' => 20090,
	'umbral masterblade' => 20066,
	'underworld rod' => 8082,
	'wand of destruction' => 27457,
	'wand of starstorm' => 8092,
	'war hammer' => 3279,
	'warsinger bow' => 8026,
	'winged boots' => 31617,
	'yalahari armor' => 8862,
	'yalahari footwraps' => 50289,
	'yalahari leg piece' => 8863,
	'yalahari mask' => 8864,
	'Zaoan armor' => 10384,
	'Zaoan monk robe' => 50259,
	'Zaoan robe' => 10439,
];

$exerciseRewardItems = [
	['name' => 'lasting exercise sword', 'id' => 35285],
	['name' => 'lasting exercise axe', 'id' => 35286],
	['name' => 'lasting exercise club', 'id' => 35287],
	['name' => 'lasting exercise bow', 'id' => 35288],
	['name' => 'lasting exercise rod', 'id' => 35289],
	['name' => 'lasting exercise wand', 'id' => 35290],
	['name' => 'lasting exercise shield', 'id' => 44067],
	['name' => 'lasting exercise wraps', 'id' => 50295],
];

$rewardGroups = [
	[
		'name' => 'Sorcerer',
		'image' => $imageBase . 'vocations/sorcererbanner.png',
		'levels' => [
			40 => ['wand of starstorm', 'spellbook of warding'],
			50 => ['spellbook of mind control', 'batwing hat'],
			60 => ['spellbook of lost souls', 'Zaoan robe'],
			75 => ['dragon robe'],
			80 => ['yalahari mask'],
			100 => ['royal scale robe', "snake god's wristguard"],
			130 => ['spellbook of vigilance', 'furious frock'],
			150 => ['spellbook of ancient arcana', 'jungle wand'],
			180 => ['spirit guide', 'soulful legs', 'dream shroud'],
			200 => ['wand of destruction'],
			220 => ['enchanted theurgic amulet'],
			250 => ['Summoner outfit'],
			350 => ['umbral master spellbook'],
		],
	],
	[
		'name' => 'Druid',
		'image' => $imageBase . 'vocations/druidbanner.png',
		'levels' => [
			40 => ['underworld rod', 'spellbook of warding'],
			50 => ['spellbook of mind control', 'batwing hat'],
			60 => ['spellbook of lost souls', 'Zaoan robe'],
			75 => ['robe of the ice queen'],
			80 => ['yalahari mask'],
			100 => ['royal scale robe', "snake god's wristguard"],
			130 => ['spellbook of vigilance', 'furious frock'],
			150 => ['spellbook of ancient arcana', 'jungle rod'],
			180 => ['spirit guide', 'soulful legs', 'dream shroud'],
			200 => ['rod of destruction'],
			220 => ['enchanted theurgic amulet'],
			250 => ['Druid outfit'],
			350 => ['umbral master spellbook'],
		],
	],
	[
		'name' => 'Paladin',
		'image' => $imageBase . 'vocations/paladinbanner.png',
		'levels' => [
			50 => ['composite hornbow', 'Zaoan armor'],
			60 => ['chain bolter', 'paladin armor'],
			80 => ['warsinger bow', 'yalahari leg piece'],
			100 => ["master archer's armor", 'elite draken helmet'],
			120 => ['rift bow', 'rift crossbow', 'prismatic armor'],
			150 => ['depth lorica', 'prismatic legs', 'jungle bow', 'jungle quiver'],
			180 => ['dark whispers'],
			200 => ['bow of destruction'],
			220 => ['winged boots'],
			250 => ['Hunter outfit'],
			350 => ['umbral master bow', 'umbral master crossbow'],
		],
	],
	[
		'name' => 'Knight',
		'image' => $imageBase . 'vocations/knightbanner.png',
		'levels' => [
			40 => ['mercenary sword', 'titan axe'],
			50 => ['thaian sword', 'twin axe', 'war hammer', 'Zaoan armor'],
			75 => ['avenger', 'crude umbral blade', 'crude umbral axe', 'crude umbral mace'],
			80 => ['yalahari armor', 'shield of corruption'],
			100 => ['fireborn giant armor'],
			130 => ['ornate shield'],
			150 => ['prismatic helmet'],
			180 => ['ectoplasmic shield', 'ornate legs'],
			200 => ['blade of destruction', 'axe of destruction', 'mace of destruction', 'ornate chestplate'],
			225 => ['fabulous legs'],
			250 => ['Knight outfit'],
			350 => ['highest melee skill umbral master weapon'],
		],
	],
	[
		'name' => 'Monk',
		'image' => $imageBase . 'vocations/monkbanner.png',
		'levels' => [
			40 => ['nunchaku', 'legs of enlightenment'],
			50 => ['nunchaku of enlightenment', 'Zaoan monk robe'],
			70 => ['coned hat of enlightenment'],
			80 => ['legs of wisdom', 'yalahari footwraps'],
			100 => ['sai of enlightenment', 'merudri scale mail'],
			125 => ['merudri nanbando'],
			135 => ['depth claws', 'jungle survivor legs'],
			150 => ['bambus jo', 'robe of enlightenment', 'gnomish footwraps'],
			180 => ['dark vision bandana'],
			200 => ['nunchaku of destruction'],
			250 => ['iks footwraps', 'eldritch crescent moon spade', 'Monk outfit'],
			350 => ['umbral master katar'],
		],
	],
];

$guideSteps = [
	[
		'kicker' => 'step_1_kicker',
		'title' => 'step_1_title',
		'image' => $imageBase . 'vocations/knightbanner.png',
		'image_alt' => 'step_1_alt',
		'text' => 'step_1_text',
		'bullets' => ['step_1_bullet_1', 'step_1_bullet_2', 'step_1_bullet_3'],
	],
	[
		'kicker' => 'step_2_kicker',
		'title' => 'step_2_title',
		'type' => 'rewards',
		'text' => 'step_2_text',
	],
	[
		'kicker' => 'step_3_kicker',
		'title' => 'step_3_title',
		'image' => $wikiImages['goldPouch'],
		'image_alt' => 'step_3_alt',
		'text' => 'step_3_text',
		'bullets' => ['step_3_bullet_1', 'step_3_bullet_2', 'step_3_bullet_3'],
	],
	[
		'kicker' => 'step_4_kicker',
		'stage' => 'step_4_stage_1',
		'title' => 'step_4_title',
		'image' => $imageBase . 'premiumfeatures/PremiumIcon-VIP.png',
		'secondary_image' => $wikiImages['storeChest'],
		'image_alt' => 'step_4_alt',
		'text' => 'step_4_text',
		'bullets' => ['step_4_bullet_1', 'step_4_bullet_2', 'step_4_bullet_3'],
		'secondary' => [
			'stage' => 'step_4_stage_2',
			'title' => 'step_5_title',
			'text' => 'step_5_text',
			'bullets' => ['step_5_bullet_1', 'step_5_bullet_2', 'step_5_bullet_3'],
		],
	],
	[
		'kicker' => 'step_6_kicker',
		'title' => 'step_6_title',
		'image' => $wikiImages['evonary'],
		'image_alt' => 'step_6_alt',
		'text' => 'step_6_text',
		'bullets' => ['step_6_bullet_1', 'step_6_bullet_2', 'step_6_bullet_3'],
	],
	[
		'kicker' => 'step_7_kicker',
		'title' => 'step_7_title',
		'image' => $wikiImages['oberon'],
		'image_alt' => 'step_7_alt',
		'text' => 'step_7_text',
		'bullets' => ['step_7_bullet_1', 'step_7_bullet_2', 'step_7_bullet_3'],
	],
];
?>

<style>
	.eclipse-guide,
	.eclipse-guide * {
		box-sizing: border-box;
		color: #1f0804 !important;
		-webkit-text-fill-color: #1f0804 !important;
		font-family: Arial, Helvetica, sans-serif;
		font-weight: 800;
		text-shadow: none !important;
	}

	.eclipse-guide .guide-shell {
		overflow: hidden;
		border: 2px solid #a66a23;
		border-radius: 5px;
		background: linear-gradient(180deg, #f6dfa9 0%, #dfba72 66%, #c99448 100%);
		box-shadow: inset 0 0 0 1px rgba(255, 246, 202, .7), 0 10px 26px rgba(0, 0, 0, .42);
	}

	.eclipse-guide .guide-hero {
		display: grid;
		grid-template-columns: auto minmax(0, 1fr);
		gap: 10px 14px;
		align-items: center;
		padding: 13px 16px;
		border-bottom: 1px solid rgba(112, 65, 24, .48);
		background: linear-gradient(180deg, #fff0bd 0%, #e8c27a 100%);
	}

	.eclipse-guide .guide-eyebrow {
		display: inline-block;
		margin: 0;
		padding: 5px 10px;
		border: 1px solid rgba(91, 22, 9, .35);
		border-radius: 4px;
		background: linear-gradient(180deg, #6d1a0e 0%, #2b0604 100%);
		color: #fff8dc !important;
		-webkit-text-fill-color: #fff8dc !important;
		font: 900 11px Verdana, Arial, sans-serif;
		letter-spacing: .08em;
		text-transform: uppercase;
		text-shadow: 0 1px 1px #000 !important;
	}

	.eclipse-guide h1 {
		grid-column: 2;
		margin: 0;
		color: #0b0502 !important;
		-webkit-text-fill-color: #0b0502 !important;
		font: 400 27px Georgia, "Times New Roman", serif;
		line-height: 1.08;
	}

	.eclipse-guide .guide-lead {
		grid-column: 1 / -1;
		max-width: none;
		margin: 0;
		padding: 9px 11px;
		border: 1px solid rgba(118, 70, 26, .28);
		border-radius: 4px;
		background: rgba(255, 248, 220, .45);
		font-size: 13px;
		line-height: 1.35;
	}

	.eclipse-guide .guide-steps {
		display: grid;
		grid-template-columns: repeat(7, minmax(0, 1fr));
		gap: 6px;
		padding: 10px;
		background: rgba(81, 38, 9, .12);
	}

	.eclipse-guide .guide-step-button {
		display: grid;
		grid-template-columns: 24px minmax(0, 1fr);
		gap: 5px;
		align-items: center;
		width: 100%;
		min-height: 44px;
		padding: 5px 7px 5px 5px;
		border: 1px solid #8a4d17;
		border-radius: 5px;
		background: linear-gradient(180deg, #fff2bd 0%, #d5a456 100%);
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, .64), 0 2px 5px rgba(61, 31, 5, .24);
		color: #050201 !important;
		-webkit-text-fill-color: #050201 !important;
		cursor: pointer;
		text-align: left;
	}

	.eclipse-guide .guide-step-button.active {
		border-color: #5d2606;
		background: linear-gradient(180deg, #ffe08a 0%, #ec9f2e 100%);
		color: #050201 !important;
		-webkit-text-fill-color: #050201 !important;
		text-shadow: none !important;
	}

	.eclipse-guide .guide-step-number {
		display: grid;
		place-items: center;
		width: 24px;
		height: 24px;
		border-radius: 50%;
		background: rgba(255, 255, 255, .42);
		color: #050201 !important;
		-webkit-text-fill-color: #050201 !important;
		font: 900 12px Arial, sans-serif;
	}

	.eclipse-guide .guide-step-label {
		overflow: hidden;
		color: #050201 !important;
		-webkit-text-fill-color: #050201 !important;
		font: 400 11px/1.16 Arial, sans-serif;
		text-overflow: ellipsis;
	}

	.eclipse-guide .guide-track-wrap {
		overflow: hidden;
		transition: height .28s ease;
	}

	.eclipse-guide .guide-track {
		display: flex;
		align-items: flex-start;
		transition: transform .28s ease;
	}

	.eclipse-guide .guide-panel {
		display: grid;
		grid-template-columns: minmax(0, 210px) minmax(0, 1fr);
		gap: 12px;
		min-width: 100%;
		padding: 12px;
		align-items: start;
	}

	.eclipse-guide .guide-panel.rewards {
		display: grid;
		grid-template-columns: 1fr;
	}

	.eclipse-guide .guide-image-card {
		display: flex;
		align-items: center;
		justify-content: center;
		min-height: 178px;
		padding: 10px;
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #f8e5b0 0%, #e3bd74 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 226, .72), 0 4px 12px rgba(64, 36, 9, .2);
	}

	.eclipse-guide .guide-image-card.has-multiple {
		flex-direction: row;
		flex-wrap: wrap;
		gap: 10px;
	}

	.eclipse-guide .guide-image-card img {
		display: block;
		max-width: 100%;
		max-height: 164px;
		object-fit: contain;
	}

	.eclipse-guide .guide-image-card img[src*="static.wikia.nocookie.net"][src*=".gif"] {
		width: 82px;
		height: 82px;
		max-height: 82px;
		image-rendering: pixelated;
		filter: drop-shadow(0 10px 14px rgba(56, 24, 6, .36));
	}

	.eclipse-guide .guide-copy,
	.eclipse-guide .guide-rewards-copy {
		padding: 13px;
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff2c4 0%, #e9c27a 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 226, .72), 0 4px 12px rgba(64, 36, 9, .2);
	}

	.eclipse-guide .guide-kicker {
		display: inline-block;
		margin-bottom: 5px;
		color: #000 !important;
		-webkit-text-fill-color: #000 !important;
		font-size: 11px;
		text-transform: uppercase;
		letter-spacing: .06em;
	}

	.eclipse-guide .guide-stage {
		display: inline-block;
		margin: 0 0 5px 6px;
		padding: 2px 7px;
		border: 1px solid rgba(112, 65, 24, .45);
		border-radius: 999px;
		background: rgba(255, 244, 199, .72);
		color: #0b0502 !important;
		-webkit-text-fill-color: #0b0502 !important;
		font: 900 10px Arial, sans-serif;
		text-transform: uppercase;
		letter-spacing: .05em;
	}

	.eclipse-guide h2 {
		margin: 0 0 7px;
		color: #0b0502 !important;
		-webkit-text-fill-color: #0b0502 !important;
		font: 400 23px/1.1 Georgia, "Times New Roman", serif;
	}

	.eclipse-guide .guide-copy p,
	.eclipse-guide .guide-rewards-copy p {
		margin: 0 0 9px;
		font-size: 13px;
		line-height: 1.42;
	}

	.eclipse-guide .guide-copy ul {
		margin: 0;
		padding-left: 18px;
	}

	.eclipse-guide .guide-copy li {
		margin: 5px 0;
		font-size: 12px;
		line-height: 1.34;
	}

	.eclipse-guide .guide-copy-section + .guide-copy-section {
		margin-top: 10px;
		padding-top: 10px;
		border-top: 1px solid rgba(105, 55, 16, .4);
		box-shadow: inset 0 1px 0 rgba(255, 248, 210, .78);
	}

	.eclipse-guide .guide-vocation-grid {
		display: grid;
		grid-template-columns: repeat(5, minmax(0, 1fr));
		gap: 7px;
		margin-top: 10px;
	}

	.eclipse-guide .guide-exercise-reward {
		display: grid;
		grid-template-columns: minmax(0, 1fr) minmax(236px, auto);
		gap: 10px;
		align-items: center;
		margin: 8px 0 0;
		padding: 8px;
		border: 1px solid rgba(118, 70, 26, .36);
		border-radius: 5px;
		background: rgba(255, 247, 215, .62);
	}

	.eclipse-guide .guide-exercise-reward strong {
		display: block;
		margin-bottom: 4px;
		color: #000 !important;
		-webkit-text-fill-color: #000 !important;
		font-size: 13px;
	}

	.eclipse-guide .guide-exercise-reward span {
		display: block;
		font-size: 12px;
		line-height: 1.35;
	}

	.eclipse-guide .guide-exercise-items {
		display: grid;
		grid-template-columns: repeat(4, 30px);
		justify-content: flex-end;
		gap: 4px;
	}

	.eclipse-guide .guide-exercise-items img {
		width: 30px;
		height: 30px;
		padding: 2px;
		border: 1px solid rgba(118, 70, 26, .3);
		border-radius: 4px;
		background: rgba(255, 252, 232, .82);
		object-fit: contain;
	}

	.eclipse-guide .guide-vocation-card {
		overflow: hidden;
		border: 1px solid rgba(118, 70, 26, .55);
		border-radius: 5px;
		background: rgba(255, 244, 198, .55);
		box-shadow: 0 3px 8px rgba(64, 36, 9, .18);
		cursor: pointer;
		padding: 0;
	}

	.eclipse-guide .guide-vocation-card img {
		display: block;
		width: 100%;
		height: 66px;
		padding: 3px;
		background: rgba(255, 246, 210, .55);
		object-fit: contain;
		object-position: center center;
	}

	.eclipse-guide .guide-vocation-card.active {
		border-color: #ffe1a0;
		background: linear-gradient(180deg, #ffdf8c 0%, #d99d42 100%);
		box-shadow: 0 0 0 2px rgba(94, 23, 13, .2), 0 4px 10px rgba(64, 36, 9, .28);
	}

	.eclipse-guide .guide-vocation-card strong {
		display: block;
		padding: 6px 6px;
		text-align: center;
		color: #000 !important;
		-webkit-text-fill-color: #000 !important;
		font-size: 12px;
	}

	.eclipse-guide .guide-reward-table-wrap {
		overflow-x: auto;
		margin-top: 10px;
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: rgba(255, 246, 210, .64);
	}

	.eclipse-guide .guide-reward-panel {
		display: none;
	}

	.eclipse-guide .guide-reward-panel.active {
		display: block;
	}

	.eclipse-guide .guide-reward-table {
		width: 100%;
		border-collapse: collapse;
		min-width: 680px;
	}

	.eclipse-guide .guide-panel.rewards .guide-reward-table {
		min-width: 0;
	}

	.eclipse-guide .guide-reward-table th,
	.eclipse-guide .guide-reward-table td {
		padding: 6px 8px;
		border-bottom: 1px solid rgba(118, 70, 26, .32);
		text-align: left;
		vertical-align: top;
		font-size: 12px;
		line-height: 1.35;
	}

	.eclipse-guide .guide-reward-table th {
		background: rgba(83, 29, 9, .15);
		color: #000 !important;
		-webkit-text-fill-color: #000 !important;
	}

	.eclipse-guide .guide-reward-level {
		white-space: nowrap;
		color: #5f1a0c !important;
		-webkit-text-fill-color: #5f1a0c !important;
	}

	.eclipse-guide .guide-reward-row {
		display: grid;
		grid-template-columns: 52px minmax(0, 1fr);
		gap: 7px;
		align-items: flex-start;
		padding: 4px 0;
		border-bottom: 1px solid rgba(118, 70, 26, .2);
	}

	.eclipse-guide .guide-reward-row:last-child {
		border-bottom: 0;
	}

	.eclipse-guide .guide-reward-items {
		display: flex;
		flex-wrap: wrap;
		gap: 5px;
		min-width: 0;
	}

	.eclipse-guide .guide-reward-pill {
		display: inline-flex;
		align-items: center;
		gap: 4px;
		padding: 2px 5px 2px 3px;
		border: 1px solid rgba(118, 70, 26, .32);
		border-radius: 4px;
		background: rgba(255, 248, 220, .6);
		font-size: 11px;
		line-height: 1.2;
	}

	.eclipse-guide .guide-reward-pill img {
		width: 24px;
		height: 24px;
		object-fit: contain;
		image-rendering: auto;
	}

	.eclipse-guide .guide-actions {
		display: flex;
		justify-content: space-between;
		gap: 10px;
		padding: 0 12px 12px;
	}

	.eclipse-guide .guide-nav {
		min-width: 120px;
		padding: 8px 14px;
		border: 1px solid #ffe1a0;
		border-radius: 4px;
		background: linear-gradient(180deg, #173f54 0%, #08202d 100%);
		color: #fff1bd !important;
		-webkit-text-fill-color: #fff1bd !important;
		cursor: pointer;
		font: 900 12px Arial, sans-serif;
		text-transform: uppercase;
		text-shadow: 0 1px 1px #000 !important;
	}

	.eclipse-guide .guide-nav.next {
		background: linear-gradient(180deg, #ff9b1f 0%, #df6505 100%);
		color: #fff7d4 !important;
		-webkit-text-fill-color: #fff7d4 !important;
	}

	.eclipse-guide .guide-nav:disabled {
		opacity: .45;
		cursor: default;
	}

	#submenu_server-guide .SubmenuitemLabel {
		color: #ffe681 !important;
		-webkit-text-fill-color: #ffe681 !important;
		font-weight: 900 !important;
		text-shadow: 0 1px 2px #210100, 0 0 6px rgba(255, 190, 42, .85) !important;
	}

	@media (max-width: 760px) {
		.eclipse-guide .guide-hero {
			grid-template-columns: 1fr;
		}

		.eclipse-guide h1,
		.eclipse-guide .guide-lead {
			grid-column: auto;
		}

		.eclipse-guide .guide-steps {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}

		.eclipse-guide .guide-panel {
			grid-template-columns: 1fr;
		}

		.eclipse-guide .guide-image-card {
			min-height: 180px;
		}

		.eclipse-guide .guide-vocation-grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}

		.eclipse-guide .guide-exercise-reward {
			grid-template-columns: 1fr;
		}

		.eclipse-guide .guide-exercise-items {
			justify-content: flex-start;
		}

		.eclipse-guide .guide-actions {
			flex-direction: column;
		}
	}
</style>

<div class="eclipse-guide" data-guide>
	<div class="guide-shell">
		<div class="guide-hero">
			<span class="guide-eyebrow"><?php echo eclipseGuideEscape(eclipseGuideTranslate('eyebrow')); ?></span>
			<h1><?php echo eclipseGuideEscape(eclipseGuideTranslate('title')); ?></h1>
			<p class="guide-lead"><?php echo eclipseGuideEscape(eclipseGuideTranslate('lead')); ?></p>
		</div>

		<div class="guide-steps" aria-label="<?php echo eclipseGuideEscape(eclipseGuideTranslate('steps_aria')); ?>">
			<?php foreach ($guideSteps as $index => $step): ?>
				<button class="guide-step-button<?php echo $index === 0 ? ' active' : ''; ?>" type="button" data-guide-step="<?php echo $index; ?>" aria-label="<?php echo eclipseGuideEscape(eclipseGuideTranslate('go_to_step') . ' ' . ($index + 1)); ?>">
					<span class="guide-step-number"><?php echo $index + 1; ?></span>
					<span class="guide-step-label"><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['title'])); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="guide-track-wrap">
			<div class="guide-track">
				<?php foreach ($guideSteps as $panelIndex => $step): ?>
					<?php if (($step['type'] ?? '') === 'rewards'): ?>
						<section class="guide-panel rewards">
							<div class="guide-rewards-copy">
								<span class="guide-kicker"><?php echo eclipseGuideEscape(eclipseGuideStepLabel($panelIndex)); ?></span>
								<h2><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['title'])); ?></h2>
								<p><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['text'])); ?></p>

								<div class="guide-exercise-reward">
									<div>
										<strong><?php echo eclipseGuideEscape(eclipseGuideTranslate('exercise_reward_title')); ?></strong>
										<span><?php echo eclipseGuideEscape(eclipseGuideTranslate('exercise_reward_text')); ?></span>
									</div>
									<div class="guide-exercise-items">
										<?php foreach ($exerciseRewardItems as $item): ?>
											<img src="<?php echo eclipseGuideEscape($itemImageBase . $item['id'] . $itemImageExtension); ?>" alt="<?php echo eclipseGuideEscape($item['name']); ?>" title="<?php echo eclipseGuideEscape($item['name']); ?>" loading="lazy">
										<?php endforeach; ?>
									</div>
								</div>

								<div class="guide-vocation-grid">
									<?php foreach ($rewardGroups as $groupIndex => $group): ?>
										<button class="guide-vocation-card<?php echo $groupIndex === 0 ? ' active' : ''; ?>" type="button" data-guide-reward-tab="<?php echo $groupIndex; ?>">
											<img src="<?php echo eclipseGuideEscape($group['image']); ?>" alt="<?php echo eclipseGuideEscape($group['name']); ?>">
											<strong><?php echo eclipseGuideEscape($group['name']); ?></strong>
										</button>
									<?php endforeach; ?>
								</div>

								<?php foreach ($rewardGroups as $groupIndex => $group): ?>
									<div class="guide-reward-table-wrap guide-reward-panel<?php echo $groupIndex === 0 ? ' active' : ''; ?>" data-guide-reward-panel="<?php echo $groupIndex; ?>">
										<table class="guide-reward-table">
											<thead>
												<tr>
													<th><?php echo eclipseGuideEscape($group['name']); ?></th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>
														<?php foreach ($group['levels'] as $level => $items): ?>
															<div class="guide-reward-row">
																<span class="guide-reward-level">Lv. <?php echo (int) $level; ?>:</span>
																<span class="guide-reward-items">
																	<?php foreach ($items as $itemName): ?>
																		<?php $itemId = $rewardItemIds[$itemName] ?? null; ?>
																		<span class="guide-reward-pill">
																			<?php if ($itemId !== null): ?>
																				<img src="<?php echo eclipseGuideEscape($itemImageBase . $itemId . $itemImageExtension); ?>" alt="<?php echo eclipseGuideEscape($itemName); ?>" loading="lazy">
																			<?php endif; ?>
																			<?php echo eclipseGuideEscape($itemName); ?>
																		</span>
																	<?php endforeach; ?>
																</span>
															</div>
														<?php endforeach; ?>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								<?php endforeach; ?>
							</div>
						</section>
					<?php else: ?>
						<section class="guide-panel">
							<div class="guide-image-card<?php echo isset($step['secondary_image']) ? ' has-multiple' : ''; ?>">
								<img src="<?php echo eclipseGuideEscape($step['image']); ?>" alt="<?php echo eclipseGuideEscape(eclipseGuideTranslate($step['image_alt'])); ?>">
								<?php if (isset($step['secondary_image'])): ?>
									<img src="<?php echo eclipseGuideEscape($step['secondary_image']); ?>" alt="<?php echo eclipseGuideEscape(eclipseGuideTranslate('step_5_alt')); ?>">
								<?php endif; ?>
							</div>
							<div class="guide-copy">
								<div class="guide-copy-section">
									<span class="guide-kicker"><?php echo eclipseGuideEscape(eclipseGuideStepLabel($panelIndex)); ?></span>
									<?php if (isset($step['stage'])): ?>
										<span class="guide-stage"><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['stage'])); ?></span>
									<?php endif; ?>
									<h2><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['title'])); ?></h2>
									<p><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['text'])); ?></p>
									<ul>
										<?php foreach ($step['bullets'] as $bullet): ?>
											<li><?php echo eclipseGuideEscape(eclipseGuideTranslate($bullet)); ?></li>
										<?php endforeach; ?>
									</ul>
								</div>
								<?php if (isset($step['secondary'])): ?>
									<div class="guide-copy-section">
										<span class="guide-kicker"><?php echo eclipseGuideEscape(eclipseGuideStepLabel($panelIndex)); ?></span>
										<?php if (isset($step['secondary']['stage'])): ?>
											<span class="guide-stage"><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['secondary']['stage'])); ?></span>
										<?php endif; ?>
										<h2><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['secondary']['title'])); ?></h2>
										<p><?php echo eclipseGuideEscape(eclipseGuideTranslate($step['secondary']['text'])); ?></p>
										<ul>
											<?php foreach ($step['secondary']['bullets'] as $bullet): ?>
												<li><?php echo eclipseGuideEscape(eclipseGuideTranslate($bullet)); ?></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
							</div>
						</section>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="guide-actions">
			<button class="guide-nav prev" type="button" data-guide-prev><?php echo eclipseGuideEscape(eclipseGuideTranslate('previous')); ?></button>
			<button class="guide-nav next" type="button" data-guide-next><?php echo eclipseGuideEscape(eclipseGuideTranslate('next')); ?></button>
		</div>
	</div>
</div>

<script>
	(function () {
		var guide = document.querySelector('[data-guide]');
		if (!guide) {
			return;
		}

		var track = guide.querySelector('.guide-track');
		var trackWrap = guide.querySelector('.guide-track-wrap');
		var panels = Array.prototype.slice.call(guide.querySelectorAll('.guide-panel'));
		var stepButtons = Array.prototype.slice.call(guide.querySelectorAll('[data-guide-step]'));
		var rewardButtons = Array.prototype.slice.call(guide.querySelectorAll('[data-guide-reward-tab]'));
		var rewardPanels = Array.prototype.slice.call(guide.querySelectorAll('[data-guide-reward-panel]'));
		var prevButton = guide.querySelector('[data-guide-prev]');
		var nextButton = guide.querySelector('[data-guide-next]');
		var activeStep = 0;

		function showStep(step) {
			activeStep = Math.max(0, Math.min(step, stepButtons.length - 1));
			track.style.transform = 'translateX(' + (-activeStep * 100) + '%)';
			if (panels[activeStep] && trackWrap) {
				trackWrap.style.height = panels[activeStep].offsetHeight + 'px';
			}

			stepButtons.forEach(function (button, index) {
				button.classList.toggle('active', index === activeStep);
			});

			prevButton.disabled = activeStep === 0;
			nextButton.disabled = activeStep === stepButtons.length - 1;
		}

		stepButtons.forEach(function (button) {
			button.addEventListener('click', function () {
				showStep(parseInt(button.getAttribute('data-guide-step'), 10));
			});
		});

		rewardButtons.forEach(function (button) {
			button.addEventListener('click', function () {
				var tab = button.getAttribute('data-guide-reward-tab');

				rewardButtons.forEach(function (item) {
					item.classList.toggle('active', item.getAttribute('data-guide-reward-tab') === tab);
				});

				rewardPanels.forEach(function (panel) {
					panel.classList.toggle('active', panel.getAttribute('data-guide-reward-panel') === tab);
				});
				if (panels[activeStep] && trackWrap) {
					trackWrap.style.height = panels[activeStep].offsetHeight + 'px';
				}
			});
		});

		prevButton.addEventListener('click', function () {
			showStep(activeStep - 1);
		});

		nextButton.addEventListener('click', function () {
			showStep(activeStep + 1);
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'ArrowLeft') {
				showStep(activeStep - 1);
			} else if (event.key === 'ArrowRight') {
				showStep(activeStep + 1);
			}
		});

		window.addEventListener('resize', function () {
			showStep(activeStep);
		});

		showStep(0);
	})();
</script>
