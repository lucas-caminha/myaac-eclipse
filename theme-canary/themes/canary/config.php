<?php
$config['menu_default_links_color'] = '#ffffff';

$config['server_save'] = '05:00:00';
$config['allow_menu_animated'] = true;
$config['logo_image'] = 'logo-eclipse-transparent.png';

// status bar
$config['status_bar'] = true;
$config['discord_link'] = '#';
$config['whatsapp_link'] = '';
$config['instagram_link'] = '';
$config['facebook_link'] = '';
$config['x_link'] = '';
$config['collapse_status'] = true;

// slide
$config['carousel_status'] = true;
$config['carousel'] = [
	'carousel_1' => 'runemaster_small.jpg',
	'carousel_2' => 'merrygarb_small.jpg',
	'carousel_3' => 'mothcape_small.jpg',
];

// banner home
$config['banner_status'] = false;
$config['banner_image'] = '500x660.png';
$config['banner_link'] = '#';

$config['menu_categories'] = [
	MENU_CATEGORY_NEWS       => ['id' => 'news',           'name' => 'Latest News'],
	MENU_CATEGORY_ACCOUNT    => ['id' => 'account',        'name' => 'Account'],
	MENU_CATEGORY_COMMUNITY  => ['id' => 'community',      'name' => 'Community'],
	MENU_CATEGORY_LIBRARY    => ['id' => 'library',        'name' => 'Library'],
	7 => ['id' => 'charactertrade', 'name' => 'Char Bazaar'],
	MENU_CATEGORY_SHOP       => ['id' => 'shops',          'name' => 'Shop'],
];

$config['menus'] = require __DIR__ . '/menus.php';
