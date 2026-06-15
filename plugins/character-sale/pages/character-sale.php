<?php
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Mercado de Personagens';
$accountId = $logged ? (int)$account_logged->getId() : 0;
$action = $_POST['market_action'] ?? '';

if (isRequestMethod('post')) {
	csrfProtect();
	if (!$logged) {
		error('Você precisa entrar na sua conta para negociar personagens.');
	} else {
		try {
			if ($action === 'sell') {
				$playerId = (int)($_POST['player_id'] ?? 0);
				$price = (int)($_POST['price'] ?? 0);
				if ($price < 25 || $price > 100000) throw new RuntimeException('Informe um valor entre 25 e 100.000 coins.');
				$stmt = $db->prepare('SELECT p.id, p.name FROM players p LEFT JOIN players_online po ON po.player_id = p.id WHERE p.id = ? AND p.account_id = ? AND p.deletion = 0 AND po.player_id IS NULL');
				$stmt->execute([$playerId, $accountId]);
				$player = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$player) throw new RuntimeException('O personagem não pertence à sua conta ou está online.');
				$stmt = $db->prepare('INSERT INTO myaac_character_offers (player_id, seller_account_id, price) VALUES (?, ?, ?)');
				$stmt->execute([$playerId, $accountId, $price]);
				success($player['name'] . ' foi anunciado no mercado.');
			} elseif ($action === 'cancel') {
				$stmt = $db->prepare('DELETE FROM myaac_character_offers WHERE id = ? AND seller_account_id = ?');
				$stmt->execute([(int)($_POST['offer_id'] ?? 0), $accountId]);
				if (!$stmt->rowCount()) throw new RuntimeException('Oferta não encontrada.');
				success('Oferta cancelada.');
			} elseif ($action === 'buy') {
				$offerId = (int)($_POST['offer_id'] ?? 0);
				$db->beginTransaction();
				$stmt = $db->prepare('SELECT o.id, o.price, o.player_id, o.seller_account_id, p.name FROM myaac_character_offers o INNER JOIN players p ON p.id = o.player_id LEFT JOIN players_online po ON po.player_id = p.id WHERE o.id = ? AND p.account_id = o.seller_account_id AND p.deletion = 0 AND po.player_id IS NULL FOR UPDATE');
				$stmt->execute([$offerId]);
				$offer = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$offer) throw new RuntimeException('A oferta não está mais disponível ou o personagem está online.');
				if ((int)$offer['seller_account_id'] === $accountId) throw new RuntimeException('Você não pode comprar seu próprio personagem.');
				$stmt = $db->prepare('SELECT coins FROM accounts WHERE id = ? FOR UPDATE');
				$stmt->execute([$accountId]);
				$buyer = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$buyer || (int)$buyer['coins'] < (int)$offer['price']) throw new RuntimeException('Saldo de coins insuficiente.');
				$db->prepare('UPDATE accounts SET coins = coins - ? WHERE id = ?')->execute([(int)$offer['price'], $accountId]);
				$db->prepare('UPDATE accounts SET coins = coins + ? WHERE id = ?')->execute([(int)$offer['price'], (int)$offer['seller_account_id']]);
				$transfer = $db->prepare('UPDATE players SET account_id = ? WHERE id = ? AND account_id = ?');
				$transfer->execute([$accountId, (int)$offer['player_id'], (int)$offer['seller_account_id']]);
				if ($transfer->rowCount() !== 1) throw new RuntimeException('A propriedade do personagem mudou durante a compra.');
				$db->prepare('DELETE FROM myaac_character_offers WHERE id = ?')->execute([$offerId]);
				$db->commit();
				success($offer['name'] . ' agora pertence à sua conta.');
			}
		} catch (Throwable $exception) {
			if ($db->inTransaction()) $db->rollBack();
			if ($exception instanceof PDOException && (string)$exception->getCode() === '23000') error('Este personagem já está anunciado.');
			else error(htmlspecialchars($exception->getMessage()));
		}
	}
}

$offers = $db->query('SELECT o.id, o.price, o.seller_account_id, o.created_at, p.id AS player_id, p.name, p.level, p.vocation, p.looktype, p.lookaddons, p.lookhead, p.lookbody, p.looklegs, p.lookfeet FROM myaac_character_offers o INNER JOIN players p ON p.id = o.player_id WHERE p.account_id = o.seller_account_id AND p.deletion = 0 ORDER BY o.created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
foreach ($offers as &$offer) {
	$offer['vocation_name'] = config('vocations')[(int)$offer['vocation']] ?? 'Sem vocação';
	$offer['outfit'] = setting('core.outfit_images_url') . '?id=' . $offer['looktype'] . '&addons=' . $offer['lookaddons'] . '&head=' . $offer['lookhead'] . '&body=' . $offer['lookbody'] . '&legs=' . $offer['looklegs'] . '&feet=' . $offer['lookfeet'];
}

$accountPlayers = [];
if ($logged) {
	$stmt = $db->prepare('SELECT p.id, p.name, p.level, p.vocation FROM players p LEFT JOIN myaac_character_offers o ON o.player_id = p.id LEFT JOIN players_online po ON po.player_id = p.id WHERE p.account_id = ? AND p.deletion = 0 AND o.id IS NULL AND po.player_id IS NULL ORDER BY p.name');
	$stmt->execute([$accountId]);
	$accountPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($accountPlayers as &$player) $player['vocation_name'] = config('vocations')[(int)$player['vocation']] ?? 'Sem vocação';
}

$twig->display('character-sale/views/market.html.twig', ['offers' => $offers, 'accountPlayers' => $accountPlayers, 'accountId' => $accountId]);
