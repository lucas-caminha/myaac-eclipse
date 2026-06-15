<?php
defined('MYAAC') or die('Direct access not allowed!');

if (PAGE !== 'news' || !$db->hasTable('guilds') || !$db->hasTable('guild_membership')) {
	return;
}

$guilds = \MyAAC\Cache::remember('eclipse-powerful-guilds', 600, function () use ($db) {
	$sql = 'SELECT g.id, g.name, g.logo_name, COUNT(DISTINCT gm.player_id) AS members, COUNT(pd.player_id) AS frags
		FROM guilds g
		LEFT JOIN guild_membership gm ON gm.guild_id = g.id
		LEFT JOIN players p ON p.id = gm.player_id
		LEFT JOIN player_deaths pd ON pd.killed_by = p.name AND pd.is_player = 1 AND pd.unjustified = 1
		GROUP BY g.id, g.name, g.logo_name
		ORDER BY frags DESC, members DESC, g.level DESC, g.name ASC
		LIMIT 5';
	return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
});

foreach ($guilds as &$guild) {
	$guild['link'] = getGuildLink($guild['name'], false);
	$guild['logo'] = !empty($guild['logo_name']) && file_exists(BASE . 'images/guilds/' . $guild['logo_name']) ? $guild['logo_name'] : 'default.gif';
}

global $twig_loader;
$twig_loader->prependPath(__DIR__);
$twig->display('powerful-guilds.html.twig', ['guilds' => $guilds]);
