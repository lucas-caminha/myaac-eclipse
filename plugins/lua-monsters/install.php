<?php
defined('MYAAC') or die('Direct access not allowed!');

if(
	!$db->hasTable('myaac_lua_monsters')
) {
	// import schema
	try {
		$db->query(file_get_contents(PLUGINS . 'lua-monsters/schema.sql'));
		success('Importing database schema.');
	}
	catch(PDOException $error_) {
		error($error_);
		return;
	}
}

if ($db->hasTable('myaac_lua_monsters') && !$db->hasColumn('myaac_lua_monsters', 'bestiary_class')) {
	$db->query("ALTER TABLE `myaac_lua_monsters` ADD `bestiary_class` VARCHAR(100) NOT NULL DEFAULT '' AFTER `health`");
	success('Campo de categoria do Bestiary criado.');
}
