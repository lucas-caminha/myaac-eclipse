<?php
defined('MYAAC') or die('Direct access not allowed!');

if(!in_array(PAGE, ['account/manage', 'accountmanagement'], true) || !$logged) {
	return;
}

if(!$db->hasTable('eclipse_duo_donation_orders')) {
	return;
}

$duoInviteAccountId = (int)$account_logged->getId();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duo_invite_action'])) {
	csrfProtect();

	$duoInviteAction = (string)$_POST['duo_invite_action'];
	$duoInviteOrderId = (int)($_POST['duo_invite_order_id'] ?? 0);

	if(in_array($duoInviteAction, ['accept', 'decline'], true) && $duoInviteOrderId > 0) {
		$stmt = $db->prepare('SELECT id, status, expires_at FROM eclipse_duo_donation_orders WHERE id = ? AND partner_account_id = ? LIMIT 1');
		$stmt->execute([$duoInviteOrderId, $duoInviteAccountId]);
		$order = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!$order || $order['status'] !== 'pending_partner' || strtotime((string)$order['expires_at']) < time()) {
			error('Este convite de donate em dupla não está mais disponível.');
		}
		else if($duoInviteAction === 'accept') {
			$db->update('eclipse_duo_donation_orders', [
				'status' => 'partner_accepted',
				'partner_accepted_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			], ['id' => (int)$order['id']]);
			success('Convite de donate em dupla aceito. O jogador principal já pode gerar o Pix.');
		}
		else {
			$db->update('eclipse_duo_donation_orders', [
				'status' => 'declined',
				'updated_at' => date('Y-m-d H:i:s'),
			], ['id' => (int)$order['id']]);
			success('Convite de donate em dupla recusado.');
		}
	}
}

$duoInviteStmt = $db->prepare(
	'SELECT o.id, o.amount_brl_cents, o.partner_coins, o.outfit_name, o.expires_at,
		pp.name AS payer_player_name, sp.name AS partner_player_name
	FROM eclipse_duo_donation_orders o
	LEFT JOIN players pp ON pp.id = o.payer_player_id
	LEFT JOIN players sp ON sp.id = o.partner_player_id
	WHERE o.partner_account_id = ?
	  AND o.status = ?
	  AND o.expires_at >= NOW()
	ORDER BY o.created_at ASC
	LIMIT 5'
);
$duoInviteStmt->execute([$duoInviteAccountId, 'pending_partner']);
$duoInvites = $duoInviteStmt->fetchAll(PDO::FETCH_ASSOC);

if(empty($duoInvites)) {
	return;
}

function eclipseDuoInviteMoney(int $amountCents): string
{
	return 'R$ ' . number_format($amountCents / 100, 2, ',', '.');
}
?>
<div class="eclipse-duo-invites">
	<div class="duo-invites-title">
		<strong>Convite de Donate em Dupla</strong>
		<span>Você foi convidado para participar de um apoio em dupla. Aceite somente se reconhecer o jogador principal.</span>
	</div>
	<div class="duo-invites-list">
		<?php foreach($duoInvites as $invite): ?>
			<div class="duo-invite-card">
				<div>
					<strong><?= htmlspecialchars((string)$invite['payer_player_name']) ?> convidou <?= htmlspecialchars((string)$invite['partner_player_name']) ?></strong>
					<span><?= eclipseDuoInviteMoney((int)$invite['amount_brl_cents']) ?> • <?= number_format((int)$invite['partner_coins'], 0, ',', '.') ?> Eclipse Coins para você • Outfit <?= htmlspecialchars((string)$invite['outfit_name']) ?> • Boost de 2 horas</span>
					<small>Expira em <?= date('d/m/Y H:i', strtotime((string)$invite['expires_at'])) ?></small>
				</div>
				<div class="duo-invite-actions">
					<form method="post" action="<?= getLink('account/manage') ?>">
						<?= csrf(true) ?>
						<input type="hidden" name="duo_invite_action" value="accept">
						<input type="hidden" name="duo_invite_order_id" value="<?= (int)$invite['id'] ?>">
						<button type="submit">Aceitar</button>
					</form>
					<form method="post" action="<?= getLink('account/manage') ?>">
						<?= csrf(true) ?>
						<input type="hidden" name="duo_invite_action" value="decline">
						<input type="hidden" name="duo_invite_order_id" value="<?= (int)$invite['id'] ?>">
						<button type="submit" class="secondary">Recusar</button>
					</form>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<style>
.eclipse-duo-invites,
.eclipse-duo-invites * {
	box-sizing: border-box;
	color: #210905 !important;
	-webkit-text-fill-color: #210905 !important;
	text-shadow: none !important;
}
.eclipse-duo-invites {
	margin: 0 0 14px;
	padding: 14px;
	background: linear-gradient(180deg, #f7dfaa 0%, #dfb96f 65%, #c59143 100%);
	border: 2px solid #a56620;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.45), 0 3px 8px rgba(0,0,0,.35);
}
.eclipse-duo-invites .duo-invites-title {
	background: linear-gradient(180deg, #40100a 0%, #180403 100%);
	border: 1px solid #b96d22;
	padding: 14px;
	text-align: center;
}
.eclipse-duo-invites .duo-invites-title strong {
	display: block;
	color: #fff0c5 !important;
	-webkit-text-fill-color: #fff0c5 !important;
	font: 900 20px Georgia, "Times New Roman", serif;
}
.eclipse-duo-invites .duo-invites-title span {
	display: block;
	margin-top: 5px;
	color: #f3d792 !important;
	-webkit-text-fill-color: #f3d792 !important;
	font: 700 12px Verdana, Arial, sans-serif;
}
.eclipse-duo-invites .duo-invites-list {
	display: grid;
	gap: 10px;
	margin-top: 12px;
}
.eclipse-duo-invites .duo-invite-card {
	display: grid;
	grid-template-columns: 1fr auto;
	gap: 12px;
	align-items: center;
	padding: 12px;
	background: rgba(255,248,221,.78);
	border: 1px solid rgba(137,83,33,.55);
}
.eclipse-duo-invites .duo-invite-card strong,
.eclipse-duo-invites .duo-invite-card span,
.eclipse-duo-invites .duo-invite-card small {
	display: block;
	font-family: Verdana, Arial, sans-serif;
}
.eclipse-duo-invites .duo-invite-card strong {
	font-size: 13px;
	font-weight: 900;
}
.eclipse-duo-invites .duo-invite-card span {
	margin-top: 5px;
	font-size: 12px;
	font-weight: 800;
	line-height: 1.45;
}
.eclipse-duo-invites .duo-invite-card small {
	margin-top: 4px;
	font-size: 11px;
	font-weight: 700;
}
.eclipse-duo-invites .duo-invite-actions {
	display: flex;
	gap: 8px;
	flex-wrap: wrap;
	justify-content: flex-end;
}
.eclipse-duo-invites .duo-invite-actions form {
	margin: 0;
	padding: 0;
}
.eclipse-duo-invites .duo-invite-actions button {
	min-height: 32px;
	padding: 7px 14px;
	background: linear-gradient(180deg, #a83b12 0%, #661003 100%);
	border: 1px solid #f0a748;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.35), 0 2px 4px rgba(0,0,0,.35);
	color: #fff2cf !important;
	-webkit-text-fill-color: #fff2cf !important;
	text-shadow: 0 1px 0 #000 !important;
	font: 900 12px Verdana, Arial, sans-serif;
	text-transform: uppercase;
	cursor: pointer;
}
.eclipse-duo-invites .duo-invite-actions button.secondary {
	background: linear-gradient(180deg, #6b4a2f 0%, #362011 100%);
}
@media (max-width: 760px) {
	.eclipse-duo-invites .duo-invite-card {
		grid-template-columns: 1fr;
	}
	.eclipse-duo-invites .duo-invite-actions {
		justify-content: flex-start;
	}
}
</style>
