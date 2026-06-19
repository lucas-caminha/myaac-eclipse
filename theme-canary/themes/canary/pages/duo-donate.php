<?php
/**
 * Duo donation flow for Eclipse OT.
 */
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Account;

$title = 'Donate em Dupla';

function eclipseDuoPackages(): array
{
	return [
		'duo_100' => [
			'label' => 'Donate em Dupla',
			'amount_cents' => 10000,
			'total_coins' => 1800,
			'payer_coins' => 900,
			'partner_coins' => 900,
		],
		'duo_200' => [
			'label' => 'Donate em Dupla Supremo',
			'amount_cents' => 20000,
			'total_coins' => 4000,
			'payer_coins' => 2000,
			'partner_coins' => 2000,
		],
	];
}

function eclipseDuoOutfits(): array
{
	return [
		'arbalester' => ['name' => 'Arbalester', 'female' => 1450, 'male' => 1449, 'addon' => 3],
		'arena_champion' => ['name' => 'Arena Champion', 'female' => 885, 'male' => 884, 'addon' => 3],
		'armoured_archer' => ['name' => 'Armoured Archer', 'female' => 1619, 'male' => 1618, 'addon' => 3],
		'beastmaster' => ['name' => 'Beastmaster', 'female' => 636, 'male' => 637, 'addon' => 3],
		'breezy_garb' => ['name' => 'Breezy Garb', 'female' => 1246, 'male' => 1245, 'addon' => 3],
		'ceremonial_garb' => ['name' => 'Ceremonial Garb', 'female' => 694, 'male' => 695, 'addon' => 3],
		'champion' => ['name' => 'Champion', 'female' => 632, 'male' => 633, 'addon' => 3],
		'conjurer' => ['name' => 'Conjurer', 'female' => 635, 'male' => 634, 'addon' => 3],
		'dragon_knight' => ['name' => 'Dragon Knight', 'female' => 1445, 'male' => 1444, 'addon' => 3],
		'evoker' => ['name' => 'Evoker', 'female' => 724, 'male' => 725, 'addon' => 3],
		'fencer' => ['name' => 'Fencer', 'female' => 1576, 'male' => 1575, 'addon' => 3],
		'flamefury_mage' => ['name' => 'Flamefury Mage', 'female' => 1681, 'male' => 1680, 'addon' => 3],
		'forest_warden' => ['name' => 'Forest Warden', 'female' => 1416, 'male' => 1415, 'addon' => 3],
		'ghost_blade' => ['name' => 'Ghost Blade', 'female' => 1490, 'male' => 1489, 'addon' => 3],
		'grove_keeper' => ['name' => 'Grove Keeper', 'female' => 909, 'male' => 908, 'addon' => 3],
		'herbalist' => ['name' => 'Herbalist', 'female' => 1020, 'male' => 1021, 'addon' => 3],
		'jouster' => ['name' => 'Jouster', 'female' => 1332, 'male' => 1331, 'addon' => 3],
		'mercenary' => ['name' => 'Mercenary', 'female' => 1057, 'male' => 1056, 'addon' => 3],
		'nordic_chieftain' => ['name' => 'Nordic Chieftain', 'female' => 1501, 'male' => 1500, 'addon' => 3],
		'owl_keeper' => ['name' => 'Owl Keeper', 'female' => 1174, 'male' => 1173, 'addon' => 3],
		'pharaoh' => ['name' => 'Pharaoh', 'female' => 956, 'male' => 955, 'addon' => 3],
		'philosopher' => ['name' => 'Philosopher', 'female' => 874, 'male' => 873, 'addon' => 3],
		'ranger' => ['name' => 'Ranger', 'female' => 683, 'male' => 684, 'addon' => 3],
		'rune_master' => ['name' => 'Rune Master', 'female' => 1385, 'male' => 1384, 'addon' => 3],
		'sea_dog' => ['name' => 'Sea Dog', 'female' => 749, 'male' => 750, 'addon' => 3],
		'shadowlotus_disciple' => ['name' => 'Shadowlotus Disciple', 'female' => 1582, 'male' => 1581, 'addon' => 3],
		'sinister_archer' => ['name' => 'Sinister Archer', 'female' => 1103, 'male' => 1102, 'addon' => 3],
		'spirit_caller' => ['name' => 'Spirit Caller', 'female' => 698, 'male' => 699, 'addon' => 3],
		'sun_priest' => ['name' => 'Sun Priest', 'female' => 1024, 'male' => 1023, 'addon' => 3],
		'veteran_paladin' => ['name' => 'Veteran Paladin', 'female' => 1205, 'male' => 1204, 'addon' => 3],
		'void_master' => ['name' => 'Void Master', 'female' => 1203, 'male' => 1202, 'addon' => 3],
		'winged_druid' => ['name' => 'Winged Druid', 'female' => 1832, 'male' => 1831, 'addon' => 3],
		'retro_hunter' => ['name' => 'Retro Hunter', 'female' => 973, 'male' => 972, 'addon' => 0],
		'retro_knight' => ['name' => 'Retro Knight', 'female' => 971, 'male' => 970, 'addon' => 0],
		'retro_mage' => ['name' => 'Retro Mage', 'female' => 969, 'male' => 968, 'addon' => 0],
		'retro_summoner' => ['name' => 'Retro Summoner', 'female' => 965, 'male' => 964, 'addon' => 0],
		'retro_warrior' => ['name' => 'Retro Warrior', 'female' => 963, 'male' => 962, 'addon' => 0],
	];
}

function eclipseDuoMoney(int $amountCents): string
{
	return 'R$ ' . number_format($amountCents / 100, 2, ',', '.');
}

function eclipseDuoEnv(string $name, ?string $fallback = null): ?string
{
	$value = getenv($name);
	return $value === false || $value === '' ? $fallback : $value;
}

function eclipseDuoWebhookUrl(): string
{
	$configured = eclipseDuoEnv('MERCADOPAGO_WEBHOOK_URL');
	return $configured ?: getLink('mercadopago-webhook');
}

function eclipseDuoPostJson(string $url, array $payload, array $headers): array
{
	if(!function_exists('curl_init')) {
		return ['ok' => false, 'status' => 0, 'body' => null, 'error' => 'PHP cURL extension is not available.'];
	}

	$ch = curl_init($url);
	curl_setopt_array($ch, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_POSTFIELDS => json_encode($payload),
		CURLOPT_TIMEOUT => 25,
	]);
	$response = curl_exec($ch);
	$error = curl_error($ch);
	$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	return [
		'ok' => $error === '' && $status >= 200 && $status < 300,
		'status' => $status,
		'body' => $response ? json_decode($response, true) : null,
		'error' => $error,
	];
}

function eclipseDuoCreateMercadoPagoPix(array $intent, array $order, Account $account): array
{
	$accessToken = eclipseDuoEnv('MERCADOPAGO_ACCESS_TOKEN');
	if(!$accessToken) {
		return ['enabled' => false, 'ok' => false, 'message' => 'Mercado Pago ainda não configurado.'];
	}

	$intentId = (int)$intent['id'];
	$response = eclipseDuoPostJson(
		'https://api.mercadopago.com/v1/payments',
		[
			'transaction_amount' => round(((int)$order['amount_brl_cents']) / 100, 2),
			'description' => 'Donate em Dupla Eclipse OT - ' . $order['outfit_name'],
			'payment_method_id' => 'pix',
			'external_reference' => 'eclipse-donation-' . $intentId,
			'notification_url' => eclipseDuoWebhookUrl(),
			'payer' => [
				'email' => $account->email ?: ('account-' . $account->id . '@eclipse-ot.local'),
				'first_name' => (string)$account->rlname,
			],
		],
		[
			'Content-Type: application/json',
			'Authorization: Bearer ' . $accessToken,
			'X-Idempotency-Key: eclipse-duo-donation-' . (int)$order['id'] . '-' . $intentId,
		]
	);

	$body = is_array($response['body']) ? $response['body'] : [];
	$transactionData = $body['point_of_interaction']['transaction_data'] ?? [];
	return [
		'enabled' => true,
		'ok' => $response['ok'],
		'message' => $response['ok'] ? 'Pix gerado pelo Mercado Pago.' : ($body['message'] ?? 'Falha ao gerar Pix no Mercado Pago.'),
		'payment_id' => isset($body['id']) ? (string)$body['id'] : null,
		'payment_status' => $body['status'] ?? null,
		'qr_code' => $transactionData['qr_code'] ?? null,
		'qr_code_base64' => $transactionData['qr_code_base64'] ?? null,
		'ticket_url' => $transactionData['ticket_url'] ?? null,
	];
}

function eclipseDuoProfileComplete(Account $account): bool
{
	return strlen(trim((string)$account->rlname)) >= 3
		&& strlen(trim((string)$account->cpf)) >= 11
		&& !empty($account->birth_date);
}

function eclipseDuoOutfitImage(array $outfit, string $sex = 'male'): string
{
	$lookType = (int)($outfit[$sex] ?? $outfit['male']);
	$addons = (int)($outfit['addon'] ?? 0);
	return setting('core.outfit_images_url') . '?id=' . $lookType . '&addons=' . $addons . '&head=114&body=94&legs=94&feet=94';
}

function eclipseDuoFindPlayerById(PDO $db, int $playerId): ?array
{
	$stmt = $db->prepare('SELECT id, name, account_id, level, vocation, sex FROM players WHERE id = ? LIMIT 1');
	$stmt->execute([$playerId]);
	$player = $stmt->fetch(PDO::FETCH_ASSOC);
	return $player ?: null;
}

function eclipseDuoFindPlayerByName(PDO $db, string $name): ?array
{
	$stmt = $db->prepare('SELECT id, name, account_id, level, vocation, sex FROM players WHERE LOWER(name) = LOWER(?) LIMIT 1');
	$stmt->execute([trim($name)]);
	$player = $stmt->fetch(PDO::FETCH_ASSOC);
	return $player ?: null;
}

function eclipseDuoAccountPlayers(PDO $db, int $accountId): array
{
	$stmt = $db->prepare('SELECT id, name, level, vocation, sex FROM players WHERE account_id = ? ORDER BY level DESC, name ASC');
	$stmt->execute([$accountId]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function eclipseDuoOrderByToken(PDO $db, string $token): ?array
{
	if(!preg_match('/^[a-f0-9]{64}$/', $token)) {
		return null;
	}
	$stmt = $db->prepare('SELECT o.*, pp.name AS payer_player_name, sp.name AS partner_player_name FROM eclipse_duo_donation_orders o LEFT JOIN players pp ON pp.id = o.payer_player_id LEFT JOIN players sp ON sp.id = o.partner_player_id WHERE o.partner_token = ? LIMIT 1');
	$stmt->execute([$token]);
	$order = $stmt->fetch(PDO::FETCH_ASSOC);
	return $order ?: null;
}

function eclipseDuoRenderShellStart(): void
{
	echo '<div class="eclipse-duo-page"><div class="duo-shell">';
	echo '<div class="duo-title"><strong>Donate em Dupla</strong><span>Divida o apoio, escolha um outfit e libere boost de 2 horas para os dois personagens.</span></div>';
}

function eclipseDuoRenderShellEnd(): void
{
	echo '</div></div>';
}

function eclipseDuoRenderLoginRequired(): void
{
	echo '<div class="duo-panel duo-alert"><h2>Acesso restrito</h2><p>Você precisa entrar na sua conta para montar ou aceitar um donate em dupla.</p><div class="duo-actions"><a class="eclipse-btn" href="' . getLink('account/manage') . '">Entrar na conta</a><a class="eclipse-btn secondary" href="' . getLink('account/create') . '">Criar conta</a></div></div>';
}

function eclipseDuoRenderCheckout(array $order, ?array $payment): void
{
?>
	<div class="duo-panel">
		<h2>Pix do donate em dupla</h2>
		<div class="duo-summary">
			<div><span>Pacote</span><strong><?= htmlspecialchars($order['outfit_name']) ?> - <?= eclipseDuoMoney((int)$order['amount_brl_cents']) ?></strong></div>
			<div><span>Coins</span><strong><?= number_format((int)$order['payer_coins'], 0, ',', '.') ?> para cada jogador</strong></div>
			<div><span>Boost</span><strong>2 horas para os dois personagens</strong></div>
		</div>
		<?php if($payment && !empty($payment['qr_code_base64'])): ?>
			<div class="duo-pix">
				<div class="duo-qr"><img src="data:image/png;base64,<?= htmlspecialchars($payment['qr_code_base64']) ?>" alt="QR Code Pix"></div>
				<div class="duo-copy">
					<label>Pix copia e cola</label>
					<textarea readonly><?= htmlspecialchars((string)$payment['qr_code']) ?></textarea>
				</div>
			</div>
			<p class="duo-status">Depois da aprovação no Mercado Pago, o site credita os coins, aplica o boost e registra o outfit escolhido.</p>
		<?php else: ?>
			<p class="duo-status warning"><?= htmlspecialchars($payment['message'] ?? 'Pedido criado, mas o Pix ainda não foi gerado.') ?></p>
		<?php endif; ?>
	</div>
<?php
}

function eclipseDuoRenderAccept(array $order, bool $isPartner, bool $accepted): void
{
?>
	<div class="duo-panel">
		<h2>Aceite do parceiro</h2>
		<div class="duo-summary">
			<div><span>Principal</span><strong><?= htmlspecialchars($order['payer_player_name'] ?? 'Personagem') ?></strong></div>
			<div><span>Parceiro</span><strong><?= htmlspecialchars($order['partner_player_name'] ?? 'Personagem') ?></strong></div>
			<div><span>Outfit</span><strong><?= htmlspecialchars($order['outfit_name']) ?></strong></div>
		</div>
		<?php if(!$isPartner): ?>
			<p class="duo-status warning">Este convite só pode ser aceito pela conta do personagem parceiro.</p>
		<?php elseif($accepted || $order['status'] !== 'pending_partner'): ?>
			<p class="duo-status">Convite aceito. O jogador principal já pode gerar o Pix do donate em dupla.</p>
		<?php else: ?>
			<p>Ao aceitar, você confirma que quer participar deste donate em dupla. O pagamento continua sendo feito pelo jogador principal.</p>
			<form method="post" class="duo-actions">
				<?= csrf(true) ?>
				<input type="hidden" name="duo_action" value="accept">
				<input type="hidden" name="token" value="<?= htmlspecialchars($order['partner_token']) ?>">
				<button class="eclipse-btn" type="submit">Aceitar convite</button>
			</form>
		<?php endif; ?>
	</div>
<?php
}

function eclipseDuoRenderForm(array $packages, array $outfits, array $players, array $orders): void
{
	$firstOutfitKey = array_key_first($outfits);
	$firstOutfit = $outfits[$firstOutfitKey];
?>
	<div class="duo-panel">
		<h2>Monte o donate em dupla</h2>
		<p>O jogador principal cria o pedido, o parceiro aceita pela própria conta e só então o Pix pode ser gerado. Cada personagem recebe metade dos coins, 2 horas de boost e o outfit escolhido fica registrado como recompensa.</p>
		<?php if(empty($players)): ?>
			<p class="duo-status warning">Sua conta ainda não possui personagens para iniciar um donate em dupla.</p>
		<?php else: ?>
			<form method="post" class="duo-form">
				<?= csrf(true) ?>
				<input type="hidden" name="duo_action" value="create">
				<input type="hidden" name="outfit_key" id="duoOutfitKey" value="<?= htmlspecialchars($firstOutfitKey) ?>">

				<div class="duo-grid duo-grid-two">
					<label>Personagem principal
						<select name="payer_player_id" required>
							<?php foreach($players as $player): ?>
								<option value="<?= (int)$player['id'] ?>"><?= htmlspecialchars($player['name']) ?> - Level <?= (int)$player['level'] ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<label>Personagem parceiro
						<input type="text" name="partner_player_name" maxlength="29" required placeholder="Nome exato do personagem">
					</label>
				</div>

				<div class="duo-package-grid">
					<?php foreach($packages as $key => $package): ?>
						<label class="duo-package">
							<input type="radio" name="package_key" value="<?= htmlspecialchars($key) ?>" <?= $key === 'duo_100' ? 'checked' : '' ?>>
							<strong><?= htmlspecialchars($package['label']) ?></strong>
							<span><?= eclipseDuoMoney((int)$package['amount_cents']) ?></span>
							<small><?= number_format((int)$package['payer_coins'], 0, ',', '.') ?> Eclipse Coins para cada jogador</small>
						</label>
					<?php endforeach; ?>
				</div>

				<div class="duo-outfit-selected">
					<div>
						<span>Outfit escolhido</span>
						<strong id="duoSelectedOutfitName"><?= htmlspecialchars($firstOutfit['name']) ?></strong>
					</div>
					<img id="duoSelectedOutfitMale" src="<?= htmlspecialchars(eclipseDuoOutfitImage($firstOutfit, 'male')) ?>" alt="">
					<img id="duoSelectedOutfitFemale" src="<?= htmlspecialchars(eclipseDuoOutfitImage($firstOutfit, 'female')) ?>" alt="">
					<button class="eclipse-btn secondary" type="button" data-duo-open-modal>Escolher outfit</button>
				</div>

				<div class="duo-actions">
					<button class="eclipse-btn" type="submit">Criar convite</button>
				</div>
			</form>
		<?php endif; ?>
	</div>

	<?php eclipseDuoRenderOrders($orders); ?>
	<?php eclipseDuoRenderOutfitModal($outfits); ?>
<?php
}

function eclipseDuoRenderOrders(array $orders): void
{
	if(empty($orders)) {
		return;
	}
?>
	<div class="duo-panel">
		<h2>Pedidos em andamento</h2>
		<div class="duo-orders">
			<?php foreach($orders as $order): ?>
				<div class="duo-order">
					<div>
						<strong><?= htmlspecialchars($order['outfit_name']) ?></strong>
						<span><?= htmlspecialchars($order['payer_player_name'] ?? 'Principal') ?> + <?= htmlspecialchars($order['partner_player_name'] ?? 'Parceiro') ?></span>
						<small>Status: <?= htmlspecialchars($order['status']) ?></small>
					</div>
					<?php if($order['status'] === 'pending_partner' && !empty($order['can_accept'])): ?>
						<a class="eclipse-btn" href="<?= getLink('duo-donate') ?>?token=<?= htmlspecialchars($order['partner_token']) ?>">Aceitar</a>
					<?php elseif($order['status'] === 'pending_partner' && !empty($order['can_pay'])): ?>
						<input class="duo-token" readonly value="<?= getLink('duo-donate') ?>?token=<?= htmlspecialchars($order['partner_token']) ?>">
					<?php elseif($order['status'] === 'partner_accepted' && !empty($order['can_pay'])): ?>
						<form method="post">
							<?= csrf(true) ?>
							<input type="hidden" name="duo_action" value="pay">
							<input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
							<button class="eclipse-btn" type="submit">Gerar Pix</button>
						</form>
					<?php else: ?>
						<span class="duo-order-status"><?= htmlspecialchars($order['status']) ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php
}

function eclipseDuoRenderOutfitModal(array $outfits): void
{
?>
	<div class="duo-modal" data-duo-modal hidden>
		<div class="duo-modal-card">
			<div class="duo-modal-head">
				<strong>Escolha o outfit</strong>
				<button type="button" data-duo-close-modal>Fechar</button>
			</div>
			<div class="duo-outfit-grid">
				<?php foreach($outfits as $key => $outfit): ?>
					<button type="button" class="duo-outfit-card" data-outfit-key="<?= htmlspecialchars($key) ?>" data-outfit-name="<?= htmlspecialchars($outfit['name']) ?>" data-outfit-male="<?= htmlspecialchars(eclipseDuoOutfitImage($outfit, 'male')) ?>" data-outfit-female="<?= htmlspecialchars(eclipseDuoOutfitImage($outfit, 'female')) ?>">
						<span><?= htmlspecialchars($outfit['name']) ?></span>
						<img src="<?= htmlspecialchars(eclipseDuoOutfitImage($outfit, 'male')) ?>" alt="">
						<img src="<?= htmlspecialchars(eclipseDuoOutfitImage($outfit, 'female')) ?>" alt="">
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php
}

function eclipseDuoRecentOrders(PDO $db, int $accountId): array
{
	$stmt = $db->prepare('SELECT o.*, pp.name AS payer_player_name, sp.name AS partner_player_name FROM eclipse_duo_donation_orders o LEFT JOIN players pp ON pp.id = o.payer_player_id LEFT JOIN players sp ON sp.id = o.partner_player_id WHERE o.payer_account_id = ? OR o.partner_account_id = ? ORDER BY o.created_at DESC LIMIT 8');
	$stmt->execute([$accountId, $accountId]);
	$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($orders as &$order) {
		$order['can_pay'] = (int)$order['payer_account_id'] === $accountId;
		$order['can_accept'] = (int)$order['partner_account_id'] === $accountId;
	}
	return $orders;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	csrfProtect();
}

$packages = eclipseDuoPackages();
$outfits = eclipseDuoOutfits();
$action = (string)($_POST['duo_action'] ?? '');
$token = (string)($_GET['token'] ?? $_POST['token'] ?? '');
$account = $logged ? Account::find($account_logged->getId()) : null;

eclipseDuoRenderShellStart();

if(!$logged || !$account) {
	eclipseDuoRenderLoginRequired();
	eclipseDuoRenderShellEnd();
	eclipseDuoRenderAssets();
	return;
}

if(!eclipseDuoProfileComplete($account)) {
	echo '<div class="duo-panel duo-alert"><h2>Complete seu cadastro</h2><p>Antes de montar um donate em dupla, complete Nome Completo, Data de Nascimento e CPF em suas informações cadastrais.</p><div class="duo-actions"><a class="eclipse-btn" href="' . getLink('account/change-info') . '">Atualizar cadastro</a></div></div>';
	eclipseDuoRenderShellEnd();
	eclipseDuoRenderAssets();
	return;
}

if(!$db->hasTable('eclipse_duo_donation_orders') || !$db->hasTable('eclipse_duo_donation_rewards')) {
	echo '<div class="duo-panel duo-alert"><h2>Migração pendente</h2><p>Aplique a migração <strong>sql/015-add-duo-donations.sql</strong> antes de ativar o donate em dupla.</p></div>';
	eclipseDuoRenderShellEnd();
	eclipseDuoRenderAssets();
	return;
}

try {
	if($action === 'create') {
		$packageKey = (string)($_POST['package_key'] ?? '');
		$outfitKey = (string)($_POST['outfit_key'] ?? '');
		$package = $packages[$packageKey] ?? null;
		$outfit = $outfits[$outfitKey] ?? null;
		$payerPlayer = eclipseDuoFindPlayerById($db, (int)($_POST['payer_player_id'] ?? 0));
		$partnerPlayer = eclipseDuoFindPlayerByName($db, (string)($_POST['partner_player_name'] ?? ''));

		if(!$package || !$outfit || !$payerPlayer || !$partnerPlayer) {
			throw new RuntimeException('Revise os dados informados para criar o convite.');
		}
		if((int)$payerPlayer['account_id'] !== (int)$account->id) {
			throw new RuntimeException('O personagem principal precisa pertencer a sua conta.');
		}
		if((int)$partnerPlayer['id'] === (int)$payerPlayer['id']) {
			throw new RuntimeException('Escolha dois personagens diferentes.');
		}
		if((int)$partnerPlayer['account_id'] === (int)$account->id) {
			throw new RuntimeException('O parceiro precisa ser um personagem de outra conta.');
		}

		$token = bin2hex(random_bytes(32));
		$db->insert('eclipse_duo_donation_orders', [
			'payer_account_id' => (int)$account->id,
			'payer_player_id' => (int)$payerPlayer['id'],
			'partner_account_id' => (int)$partnerPlayer['account_id'],
			'partner_player_id' => (int)$partnerPlayer['id'],
			'package_key' => $packageKey,
			'amount_brl_cents' => (int)$package['amount_cents'],
			'total_coins' => (int)$package['total_coins'],
			'payer_coins' => (int)$package['payer_coins'],
			'partner_coins' => (int)$package['partner_coins'],
			'outfit_key' => $outfitKey,
			'outfit_name' => $outfit['name'],
			'male_looktype' => (int)$outfit['male'],
			'female_looktype' => (int)$outfit['female'],
			'outfit_addon' => (int)$outfit['addon'],
			'boost_seconds' => 7200,
			'boost_percent' => 50,
			'status' => 'pending_partner',
			'partner_token' => $token,
			'expires_at' => date('Y-m-d H:i:s', strtotime('+48 hours')),
		]);
		success('Convite criado. Envie o link de aceite para o parceiro.');
		$token = '';
	}
	else if($action === 'accept' || $action === 'decline') {
		$order = eclipseDuoOrderByToken($db, $token);
		if(!$order || (int)$order['partner_account_id'] !== (int)$account->id || $order['status'] !== 'pending_partner' || strtotime((string)$order['expires_at']) < time()) {
			throw new RuntimeException('Este convite não está disponível.');
		}
		if($action === 'accept') {
			$db->update('eclipse_duo_donation_orders', [
				'status' => 'partner_accepted',
				'partner_accepted_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			], ['id' => (int)$order['id']]);
			success('Convite aceito. O jogador principal já pode gerar o Pix.');
		}
		else {
			$db->update('eclipse_duo_donation_orders', [
				'status' => 'declined',
				'updated_at' => date('Y-m-d H:i:s'),
			], ['id' => (int)$order['id']]);
			success('Convite recusado.');
		}
		$token = '';
	}
	else if($action === 'pay') {
		if(!$db->hasTable('eclipse_donation_intents')) {
			throw new RuntimeException('A tabela de intenções de doação ainda não está disponível.');
		}
		$stmt = $db->prepare('SELECT * FROM eclipse_duo_donation_orders WHERE id = ? AND payer_account_id = ? LIMIT 1');
		$stmt->execute([(int)($_POST['order_id'] ?? 0), (int)$account->id]);
		$order = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$order || $order['status'] !== 'partner_accepted') {
			throw new RuntimeException('O pedido precisa estar aceito pelo parceiro antes de gerar o Pix.');
		}

		$db->insert('eclipse_donation_intents', [
			'account_id' => (int)$account->id,
			'package_key' => (string)$order['package_key'],
			'amount_brl_cents' => (int)$order['amount_brl_cents'],
			'coins' => (int)$order['total_coins'],
			'status' => 'pending_gateway',
			'gateway' => 'pending_pix',
			'payer_name' => $account->rlname,
			'payer_cpf' => null,
			'notes' => 'Duo donation order #' . (int)$order['id'],
		]);
		$intentId = (int)$db->lastInsertId();
		$payment = eclipseDuoCreateMercadoPagoPix(['id' => $intentId], $order, $account);

		$orderStatus = empty($payment['enabled']) ? 'partner_accepted' : (!empty($payment['ok']) ? 'pending_gateway' : 'gateway_error');
		$db->update('eclipse_duo_donation_orders', [
			'status' => $orderStatus,
			'donation_intent_id' => $intentId,
			'updated_at' => date('Y-m-d H:i:s'),
		], ['id' => (int)$order['id']]);

		if(!empty($payment['enabled'])) {
			$db->update('eclipse_donation_intents', [
				'status' => !empty($payment['ok']) ? ($payment['payment_status'] ?: 'pending') : 'gateway_error',
				'gateway' => 'mercadopago',
				'gateway_reference' => $payment['payment_id'],
				'pix_qr_code' => $payment['qr_code_base64'],
				'pix_copy_paste' => $payment['qr_code'],
				'notes' => !empty($payment['ok']) ? 'Mercado Pago Pix generated for duo donation.' : ('Mercado Pago error: ' . ($payment['message'] ?? 'unknown')),
				'updated_at' => date('Y-m-d H:i:s'),
			], ['id' => $intentId]);
		}

		$order['id'] = (int)$order['id'];
		eclipseDuoRenderCheckout($order, $payment);
		eclipseDuoRenderShellEnd();
		eclipseDuoRenderAssets();
		return;
	}
}
catch(Throwable $exception) {
	error(htmlspecialchars($exception->getMessage()));
}

if($token !== '') {
	$order = eclipseDuoOrderByToken($db, $token);
	if($order && strtotime((string)$order['expires_at']) >= time()) {
		eclipseDuoRenderAccept($order, (int)$order['partner_account_id'] === (int)$account->id, $order['status'] !== 'pending_partner');
	}
	else {
		echo '<div class="duo-panel duo-alert"><h2>Convite inválido</h2><p>Este convite não existe ou já expirou.</p></div>';
	}
}
else {
	$orders = eclipseDuoRecentOrders($db, (int)$account->id);
	$players = eclipseDuoAccountPlayers($db, (int)$account->id);
	eclipseDuoRenderForm($packages, $outfits, $players, $orders);
}

eclipseDuoRenderShellEnd();
eclipseDuoRenderAssets();
?>
<?php
function eclipseDuoRenderAssets(): void
{
?>
<style>
.eclipse-duo-page,
.eclipse-duo-page * {
	box-sizing: border-box;
	color: #210905 !important;
	-webkit-text-fill-color: #210905 !important;
	text-shadow: none !important;
}
.eclipse-duo-page .duo-shell {
	background: linear-gradient(180deg, #f7dfaa 0%, #dfb96f 62%, #c59143 100%);
	border: 2px solid #a56620;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.45), 0 3px 8px rgba(0,0,0,.35);
	padding: 16px;
}
.eclipse-duo-page .duo-title {
	background: linear-gradient(180deg, #40100a 0%, #180403 100%);
	border: 1px solid #b96d22;
	padding: 20px;
	text-align: center;
}
.eclipse-duo-page .duo-title strong {
	display: block;
	color: #fff0c5 !important;
	-webkit-text-fill-color: #fff0c5 !important;
	font: 900 26px Georgia, "Times New Roman", serif;
}
.eclipse-duo-page .duo-title span {
	display: block;
	margin-top: 6px;
	color: #f3d792 !important;
	-webkit-text-fill-color: #f3d792 !important;
	font: 700 12px Verdana, Arial, sans-serif;
}
.eclipse-duo-page .duo-panel {
	margin-top: 14px;
	padding: 16px;
	background: rgba(255,244,205,.76);
	border: 1px solid rgba(126,78,27,.55);
}
.eclipse-duo-page h2 {
	margin: 0 0 12px;
	font: 900 20px Georgia, "Times New Roman", serif;
}
.eclipse-duo-page p {
	margin: 0 0 12px;
	font: 700 13px/1.55 Verdana, Arial, sans-serif;
}
.eclipse-duo-page label {
	display: grid;
	gap: 6px;
	font: 900 12px Verdana, Arial, sans-serif;
}
.eclipse-duo-page input,
.eclipse-duo-page select,
.eclipse-duo-page textarea {
	width: 100%;
	padding: 9px 10px;
	background: #fff6d8 !important;
	border: 1px solid #8c5b27;
	color: #210905 !important;
	-webkit-text-fill-color: #210905 !important;
	font: 700 12px Verdana, Arial, sans-serif;
}
.eclipse-duo-page select option {
	background: #fff6d8 !important;
	color: #210905 !important;
	-webkit-text-fill-color: #210905 !important;
	text-shadow: none !important;
	font: 700 12px Verdana, Arial, sans-serif;
}
.eclipse-duo-page select:focus,
.eclipse-duo-page select option:checked,
.eclipse-duo-page select option:hover {
	background: #f2cf83 !important;
	color: #210905 !important;
	-webkit-text-fill-color: #210905 !important;
}
.eclipse-duo-page .duo-grid {
	display: grid;
	gap: 12px;
}
.eclipse-duo-page .duo-grid-two,
.eclipse-duo-page .duo-summary {
	grid-template-columns: repeat(2, minmax(0, 1fr));
}
.eclipse-duo-page .duo-package-grid {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 12px;
	margin-top: 14px;
}
.eclipse-duo-page .duo-package {
	position: relative;
	padding: 14px;
	background: rgba(255,248,221,.72);
	border: 1px solid rgba(137,83,33,.52);
	cursor: pointer;
}
.eclipse-duo-page .duo-package input {
	position: absolute;
	top: 12px;
	right: 12px;
	width: auto;
}
.eclipse-duo-page .duo-package strong,
.eclipse-duo-page .duo-package span,
.eclipse-duo-page .duo-package small {
	display: block;
	margin-right: 28px;
}
.eclipse-duo-page .duo-package strong {
	font: 900 15px Georgia, "Times New Roman", serif;
}
.eclipse-duo-page .duo-package span {
	margin-top: 6px;
	font: 900 16px Verdana, Arial, sans-serif;
}
.eclipse-duo-page .duo-package small {
	margin-top: 4px;
	font: 800 11px Verdana, Arial, sans-serif;
}
.eclipse-duo-page .duo-outfit-selected {
	display: grid;
	grid-template-columns: 1fr 76px 76px auto;
	gap: 12px;
	align-items: center;
	margin-top: 14px;
	padding: 12px;
	background: rgba(255,248,221,.72);
	border: 1px solid rgba(137,83,33,.52);
}
.eclipse-duo-page .duo-outfit-selected span,
.eclipse-duo-page .duo-summary span {
	display: block;
	margin-bottom: 5px;
	font: 800 11px Verdana, Arial, sans-serif;
	text-transform: uppercase;
}
.eclipse-duo-page .duo-outfit-selected strong,
.eclipse-duo-page .duo-summary strong {
	font: 900 14px Verdana, Arial, sans-serif;
}
.eclipse-duo-page .duo-outfit-selected img {
	max-height: 72px;
	object-fit: contain;
}
.eclipse-duo-page .duo-actions {
	display: flex;
	gap: 10px;
	flex-wrap: wrap;
	justify-content: center;
	margin-top: 16px;
}
.eclipse-duo-page .eclipse-btn,
.eclipse-duo-page button.eclipse-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-height: 34px;
	padding: 8px 16px;
	background: linear-gradient(180deg, #a83b12 0%, #661003 100%);
	border: 1px solid #f0a748;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.35), 0 2px 4px rgba(0,0,0,.35);
	color: #fff2cf !important;
	-webkit-text-fill-color: #fff2cf !important;
	text-shadow: 0 1px 0 #000 !important;
	font: 900 12px Verdana, Arial, sans-serif;
	text-transform: uppercase;
	text-decoration: none;
	cursor: pointer;
}
.eclipse-duo-page .eclipse-btn.secondary {
	background: linear-gradient(180deg, #6b4a2f 0%, #362011 100%);
}
.eclipse-duo-page .duo-summary {
	display: grid;
	gap: 10px;
	margin-bottom: 14px;
}
.eclipse-duo-page .duo-summary div,
.eclipse-duo-page .duo-status,
.eclipse-duo-page .duo-order {
	padding: 12px;
	background: rgba(255,248,221,.72);
	border: 1px solid rgba(137,83,33,.52);
}
.eclipse-duo-page .duo-status.warning {
	border-color: #9b2b18;
}
.eclipse-duo-page .duo-orders {
	display: grid;
	gap: 10px;
}
.eclipse-duo-page .duo-order {
	display: grid;
	grid-template-columns: 1fr auto;
	gap: 12px;
	align-items: center;
}
.eclipse-duo-page .duo-order strong,
.eclipse-duo-page .duo-order span,
.eclipse-duo-page .duo-order small {
	display: block;
}
.eclipse-duo-page .duo-token {
	min-width: 280px;
}
.eclipse-duo-page .duo-pix {
	display: grid;
	grid-template-columns: 210px 1fr;
	gap: 16px;
}
.eclipse-duo-page .duo-qr {
	display: grid;
	place-items: center;
	background: #fff6d8;
	border: 2px dashed #8c5b27;
	min-height: 210px;
}
.eclipse-duo-page .duo-qr img {
	width: min(100%, 210px);
	height: auto;
}
.eclipse-duo-page .duo-copy textarea {
	min-height: 210px;
	resize: vertical;
}
.eclipse-duo-page .duo-modal[hidden] {
	display: none;
}
.eclipse-duo-page .duo-modal {
	position: fixed;
	inset: 0;
	z-index: 9999;
	display: grid;
	place-items: center;
	padding: 24px;
	background: rgba(0,0,0,.72);
}
.eclipse-duo-page .duo-modal-card {
	width: min(980px, 96vw);
	max-height: 88vh;
	overflow: auto;
	background: linear-gradient(180deg, #f7dfaa 0%, #dfb96f 100%);
	border: 2px solid #a56620;
	padding: 14px;
}
.eclipse-duo-page .duo-modal-head {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 12px;
}
.eclipse-duo-page .duo-modal-head strong {
	font: 900 20px Georgia, "Times New Roman", serif;
}
.eclipse-duo-page .duo-modal-head button {
	padding: 6px 10px;
	background: #3a1208;
	border: 1px solid #c9842f;
	color: #fff0c5 !important;
	-webkit-text-fill-color: #fff0c5 !important;
	font-weight: 900;
	cursor: pointer;
}
.eclipse-duo-page .duo-outfit-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
	gap: 10px;
}
.eclipse-duo-page .duo-outfit-card {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 6px;
	align-items: end;
	padding: 10px;
	background: rgba(255,248,221,.72);
	border: 1px solid rgba(137,83,33,.52);
	cursor: pointer;
}
.eclipse-duo-page .duo-outfit-card span {
	grid-column: 1 / -1;
	font: 900 12px Verdana, Arial, sans-serif;
}
.eclipse-duo-page .duo-outfit-card img {
	max-height: 72px;
	object-fit: contain;
	justify-self: center;
}
@media (max-width: 760px) {
	.eclipse-duo-page .duo-grid-two,
	.eclipse-duo-page .duo-package-grid,
	.eclipse-duo-page .duo-summary,
	.eclipse-duo-page .duo-outfit-selected,
	.eclipse-duo-page .duo-order,
	.eclipse-duo-page .duo-pix {
		grid-template-columns: 1fr;
	}
}
</style>
<script>
(function() {
	var root = document.querySelector('.eclipse-duo-page');
	if(!root) return;
	var modal = root.querySelector('[data-duo-modal]');
	var input = root.querySelector('#duoOutfitKey');
	var name = root.querySelector('#duoSelectedOutfitName');
	var male = root.querySelector('#duoSelectedOutfitMale');
	var female = root.querySelector('#duoSelectedOutfitFemale');
	var open = root.querySelector('[data-duo-open-modal]');
	var close = root.querySelector('[data-duo-close-modal]');
	if(open && modal) {
		open.addEventListener('click', function() { modal.hidden = false; });
	}
	if(close && modal) {
		close.addEventListener('click', function() { modal.hidden = true; });
	}
	if(modal) {
		modal.addEventListener('click', function(event) {
			if(event.target === modal) modal.hidden = true;
		});
		modal.querySelectorAll('[data-outfit-key]').forEach(function(button) {
			button.addEventListener('click', function() {
				if(input) input.value = button.getAttribute('data-outfit-key');
				if(name) name.textContent = button.getAttribute('data-outfit-name');
				if(male) male.src = button.getAttribute('data-outfit-male');
				if(female) female.src = button.getAttribute('data-outfit-female');
				modal.hidden = true;
			});
		});
	}
})();
</script>
<?php
}
