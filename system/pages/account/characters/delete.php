<?php
/**
 * Delete character
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Excluir Personagem';
require PAGES . 'account/base.php';

if(!$logged) {
	return;
}

csrfProtect();

$player_name = isset($_POST['delete_name']) ? stripslashes($_POST['delete_name']) : null;
$password_verify = isset($_POST['delete_password']) ? $_POST['delete_password'] : null;
$password_verify = encrypt((USE_ACCOUNT_SALT ? $account_logged->getCustomField('salt') : '') . $password_verify);
if(isset($_POST['deletecharactersave']) && $_POST['deletecharactersave'] == 1) {
	if(empty($player_name) || empty($password_verify)) {
		$errors[] = 'Nome do personagem e senha são obrigatórios.';
	}

	if(empty($errors) && !Validator::characterName($player_name)) {
		$errors[] = 'O nome contém caracteres inválidos.';
	}

	$player = new OTS_Player();
	$player->find($player_name);
	if(empty($errors) && !$player->isLoaded()) {
		$errors[] = 'Não existe personagem com este nome.';
	}

	if(empty($errors)) {
		$player_account = $player->getAccount();
		if($account_logged->getId() != $player_account->getId()) {
			$errors[] = 'O personagem <b>' . $player_name . '</b> não pertence à sua conta.';
		}
	}

	if(empty($errors) && $password_verify != $account_logged->getPassword()) {
		$errors[] = 'Senha da conta incorreta.';
	}

	if(empty($errors) && $player->isOnline()) {
		$errors[] = 'Este personagem está online.';
	}

	if(empty($errors) && $player->isDeleted()) {
		$errors[] = 'Este personagem já foi excluído.';
	}

	if(empty($errors) && $db->hasColumn('houses', 'id')) {
		$house = $db->query('SELECT `id` FROM `houses` WHERE `owner` = ' . $player->getId());
		if($house->rowCount() > 0) {
			$errors[] = 'Você não pode excluir um personagem dono de uma casa.';
		}
	}

	if(empty($errors)) {
		$ownerid = 'ownerid';
		if($db->hasColumn('guilds', 'owner_id')) {
			$ownerid = 'owner_id';
		}
		$guild = $db->query('SELECT `name` FROM `guilds` WHERE `' . $ownerid . '` = ' . $player->getId());
		if($guild->rowCount() > 0) {
			$errors[] = 'Você não pode excluir um personagem dono de uma guild.';
		}
	}

	if(empty($errors)) {
		$show_form = false;
		if($db->hasColumn('players', 'deletion')) {
			$player->setCustomField('deletion', 1);
		}
		else {
			$player->setCustomField('deleted', 1);
		}

		$account_logged->logAction('Deleted character <b>' . $player->getName() . '</b>.');
		$twig->display('success.html.twig', [
			'title' => 'Personagem excluído',
			'description' => 'O personagem <b>' . $player_name . '</b> foi excluído.',
		]);
	}
}

if($show_form) {
	if(!empty($errors)) {
		$twig->display('error_box.html.twig', ['errors' => $errors]);
	}

	$twig->display('account.characters.delete.html.twig');
}
