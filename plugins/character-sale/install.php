<?php
defined('MYAAC_ADMIN') or die('Direct access not allowed!');

if (!$db->hasTable('myaac_character_offers')) {
	$db->query(file_get_contents(__DIR__ . '/schema.sql'));
	success('Tabela do Mercado de Personagens criada.');
}
