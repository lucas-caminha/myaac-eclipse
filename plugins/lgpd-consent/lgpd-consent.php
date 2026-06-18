<?php
defined('MYAAC') or die('Direct access not allowed!');

if(PAGE !== 'account/create' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
	return;
}

if(!isset($_POST['save']) || $_POST['save'] != '1') {
	return;
}

if(!empty($_POST['accept_privacy'])) {
	return;
}

unset($_POST['save']);

$twig->display('error_box.html.twig', [
	'errors' => [
		'Para criar a conta, leia e aceite a Politica de Privacidade.'
	],
]);

