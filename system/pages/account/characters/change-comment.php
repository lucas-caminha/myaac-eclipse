<?php
/**
 * Change comment
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Player;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Editar Comentário';
require PAGES . 'account/base.php';

if(!$logged) {
	return;
}

csrfProtect();

$player = null;
$player_name = isset($_REQUEST['name']) ? stripslashes(urldecode($_REQUEST['name'])) : null;
$new_comment = isset($_POST['comment']) ? htmlspecialchars(stripslashes(substr($_POST['comment'], 0, 2000))) : null;
$new_hideacc = isset($_POST['accountvisible']) ? (int)$_POST['accountvisible'] : null;

if($player_name != null) {
	if(Validator::characterName($player_name)) {
		$player = Player::query()
			->where('name', $player_name)
			->where('account_id', $account_logged->getId())
			->first();

		if($player) {
			if($player->is_deleted) {
				$errors[] = 'Este personagem está excluído.';
				$player = null;
			}

			if(isset($_POST['changecommentsave']) && $_POST['changecommentsave'] == 1) {
				if(empty($errors)) {
					$player->hide = $new_hideacc;
					$player->comment = $new_comment;
					$player->save();
					$account_logged->logAction('Changed comment for character <b>' . $player->name . '</b>.');
					$twig->display('success.html.twig', [
						'title' => 'Comentário atualizado',
						'description' => 'As informações do personagem foram atualizadas.',
					]);
					$show_form = false;

					$hooks->trigger(HOOK_ACCOUNT_CHARACTERS_CHANGE_COMMENT_AFTER_SUCCESS, ['player' => $player]);
				}
			}
		} else {
			$errors[] = 'Personagem não encontrado nesta conta.';
		}
	} else {
		$errors[] = 'O nome contém caracteres inválidos.';
	}
}
else {
	$errors[] = 'Informe o nome do personagem.';
}

if($show_form) {
	if(!empty($errors)) {
		$twig->display('error_box.html.twig', ['errors' => $errors]);
	}

	if(isset($player) && $player) {
		$_player = $player->toArray();
		$_player['id'] = $player->id;

		$twig->display('account.characters.change-comment.html.twig', [
			'player' => $_player,
		]);
	}
}
