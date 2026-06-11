<?php
/**
 * Donation intent flow for Eclipse OT.
 *
 * This page intentionally stops before payment gateway integration. It creates
 * an auditable donation intent that can later receive Pix data and webhook
 * confirmation.
 */
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Account;

$title = 'Apoiar o Eclipse OT';

$packages = [
	'starter' => [
		'label' => 'Apoio Inicial',
		'amount_cents' => 1000,
		'coins' => 100,
	],
	'adventurer' => [
		'label' => 'Apoio Aventureiro',
		'amount_cents' => 2500,
		'coins' => 300,
	],
	'guardian' => [
		'label' => 'Apoio Guardião',
		'amount_cents' => 5000,
		'coins' => 700,
	],
	'eclipse' => [
		'label' => 'Apoio Eclipse',
		'amount_cents' => 10000,
		'coins' => 1500,
	],
];

function eclipseDonationMoney(int $amountCents): string
{
	return 'R$ ' . number_format($amountCents / 100, 2, ',', '.');
}

function eclipseDonationPackage(array $packages, string $key): ?array
{
	return $packages[$key] ?? null;
}

function eclipseDonationStep(): string
{
	$step = $_POST['step'] ?? $_GET['step'] ?? 'intro';
	return in_array($step, ['intro', 'packages', 'checkout'], true) ? $step : 'intro';
}

function eclipseDonationProfileComplete(Account $account): bool
{
	return strlen(trim((string)$account->rlname)) >= 3
		&& strlen(trim((string)$account->cpf)) >= 11
		&& !empty($account->birth_date);
}

function eclipseDonationEnv(string $name, ?string $fallback = null): ?string
{
	$value = getenv($name);
	return $value === false || $value === '' ? $fallback : $value;
}

function eclipseDonationWebhookUrl(): string
{
	$configured = eclipseDonationEnv('MERCADOPAGO_WEBHOOK_URL');
	return $configured ?: getLink('mercadopago-webhook');
}

function eclipseDonationGatewayEnabled(): bool
{
	return (bool)eclipseDonationEnv('MERCADOPAGO_ACCESS_TOKEN');
}

function eclipseDonationPostJson(string $url, array $payload, array $headers): array
{
	if(!function_exists('curl_init')) {
		return [
			'ok' => false,
			'status' => 0,
			'body' => null,
			'error' => 'PHP cURL extension is not available.',
		];
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

function eclipseDonationCreateMercadoPagoPix(array $intent, array $package, Account $account): array
{
	$accessToken = eclipseDonationEnv('MERCADOPAGO_ACCESS_TOKEN');
	if(!$accessToken) {
		return [
			'enabled' => false,
			'ok' => false,
			'message' => 'Mercado Pago ainda nao configurado.',
		];
	}

	$intentId = (int)$intent['id'];
	$payload = [
		'transaction_amount' => round($package['amount_cents'] / 100, 2),
		'description' => 'Doacao Eclipse OT - ' . $package['label'],
		'payment_method_id' => 'pix',
		'external_reference' => 'eclipse-donation-' . $intentId,
		'notification_url' => eclipseDonationWebhookUrl(),
		'payer' => [
			'email' => $account->email ?: ('account-' . $account->id . '@eclipse-ot.local'),
			'first_name' => (string)$account->rlname,
		],
	];

	$response = eclipseDonationPostJson(
		'https://api.mercadopago.com/v1/payments',
		$payload,
		[
			'Content-Type: application/json',
			'Authorization: Bearer ' . $accessToken,
			'X-Idempotency-Key: eclipse-donation-intent-' . $intentId,
		]
	);

	$body = is_array($response['body']) ? $response['body'] : [];
	$transactionData = $body['point_of_interaction']['transaction_data'] ?? [];

	return [
		'enabled' => true,
		'ok' => $response['ok'],
		'http_status' => $response['status'],
		'error' => $response['error'],
		'message' => $response['ok'] ? 'Pix gerado pelo Mercado Pago.' : ($body['message'] ?? 'Falha ao gerar Pix no Mercado Pago.'),
		'payment_id' => isset($body['id']) ? (string)$body['id'] : null,
		'payment_status' => $body['status'] ?? null,
		'qr_code' => $transactionData['qr_code'] ?? null,
		'qr_code_base64' => $transactionData['qr_code_base64'] ?? null,
		'ticket_url' => $transactionData['ticket_url'] ?? null,
	];
}

function eclipseDonationRenderProgress(string $step): void
{
	$steps = [
		'intro' => 'Orientação',
		'packages' => 'Coins',
		'checkout' => 'Pix',
	];

	echo '<div class="donation-progress">';
	foreach($steps as $key => $label) {
		$class = $key === $step ? 'active' : '';
		echo '<span class="' . $class . '">' . $label . '</span>';
	}
	echo '</div>';
}

function eclipseDonationRenderShellStart(string $step): void
{
	echo '<div class="eclipse-donation-page">';
	echo '<div class="donation-shell">';
	echo '<div class="donation-title">';
	echo '<strong>Apoie o Eclipse OT</strong>';
	echo '<span>Doação voluntária para manter o servidor evoluindo.</span>';
	echo '</div>';
	eclipseDonationRenderProgress($step);
}

function eclipseDonationRenderShellEnd(): void
{
	echo '</div></div>';
}

function eclipseDonationRenderIntro(bool $logged): void
{
?>
	<div class="donation-panel">
		<h2>Antes de continuar</h2>
		<p>Esta contribuição é uma doação voluntária de apoio ao Eclipse OT. Ela ajuda a manter custos de infraestrutura, desenvolvimento, manutenção e melhorias da comunidade.</p>
		<p>Os Eclipse Coins exibidos nas próximas etapas são uma forma de agradecimento dentro do servidor. Eles não representam investimento, produto financeiro, saque, reembolso automático ou garantia de disponibilidade permanente.</p>
		<p>O servidor pode passar por ajustes, eventos, balanceamentos e manutenções. Ao apoiar, você declara estar ciente das regras do servidor e de que a contribuição é feita para apoiar o projeto.</p>
		<div class="donation-actions">
			<?php if($logged): ?>
				<a class="eclipse-btn" href="<?= getLink('points') ?>?step=packages">Continuar</a>
			<?php else: ?>
				<a class="eclipse-btn" href="<?= getLink('account/manage') ?>">Entrar para continuar</a>
			<?php endif; ?>
		</div>
	</div>
<?php
}

function eclipseDonationRenderLoginRequired(): void
{
?>
	<div class="donation-panel donation-error">
		<h2>Acesso restrito</h2>
		<p>Você precisa estar logado para acessar a página de apoio ao Eclipse OT.</p>
		<div class="donation-actions">
			<a class="eclipse-btn" href="<?= getLink('account/manage') ?>">Entrar na conta</a>
		</div>
	</div>
<?php
}

function eclipseDonationRenderPackages(array $packages, bool $profileComplete): void
{
?>
	<div class="donation-panel">
		<h2>Escolha os coins de agradecimento</h2>
		<p>Selecione a faixa de apoio. A geração real do Pix ainda será integrada, então esta etapa apenas prepara o pedido.</p>

		<?php if(!$profileComplete): ?>
			<div class="donation-warning">
				<strong>Dados pendentes</strong>
				<span>Para seguir com doações futuramente, complete Nome Completo, Data de Nascimento e CPF em suas informações cadastrais.</span>
				<a href="<?= getLink('account/change-info') ?>">Atualizar cadastro</a>
			</div>
		<?php endif; ?>

		<div class="donation-package-list">
			<?php foreach($packages as $key => $package): ?>
				<form class="donation-package" method="post" action="<?= getLink('points') ?>">
					<?= csrf(true) ?>
					<input type="hidden" name="step" value="checkout">
					<input type="hidden" name="package" value="<?= htmlspecialchars($key) ?>">
					<strong><?= htmlspecialchars($package['label']) ?></strong>
					<span class="package-coins"><?= number_format($package['coins'], 0, ',', '.') ?> Eclipse Coins</span>
					<span class="package-amount"><?= eclipseDonationMoney($package['amount_cents']) ?></span>
					<button class="eclipse-btn" type="submit" <?= $profileComplete ? '' : 'disabled' ?>>Selecionar</button>
				</form>
			<?php endforeach; ?>
		</div>
	</div>
<?php
}

function eclipseDonationRenderCheckout(array $package, ?int $intentId, bool $intentSaved, ?array $payment = null): void
{
	$payment = $payment ?? [];
	$hasPix = !empty($payment['qr_code']) || !empty($payment['qr_code_base64']);
?>
	<div class="donation-panel donation-checkout">
		<h2>Finalização via Pix</h2>
		<div class="donation-summary">
			<div>
				<span>Faixa de apoio</span>
				<strong><?= htmlspecialchars($package['label']) ?></strong>
			</div>
			<div>
				<span>Contribuição</span>
				<strong><?= eclipseDonationMoney($package['amount_cents']) ?></strong>
			</div>
			<div>
				<span>Agradecimento</span>
				<strong><?= number_format($package['coins'], 0, ',', '.') ?> coins</strong>
			</div>
		</div>

		<div class="donation-pix-placeholder">
			<div class="pix-frame">
				<?php if(!empty($payment['qr_code_base64'])): ?>
					<img src="data:image/jpeg;base64,<?= htmlspecialchars($payment['qr_code_base64']) ?>" alt="QR Code Pix Mercado Pago">
				<?php else: ?>
					<span>QR Code Pix</span>
					<small><?= eclipseDonationGatewayEnabled() ? 'Aguardando geração' : 'Integração pendente' ?></small>
				<?php endif; ?>
			</div>
			<div class="pix-copy">
				<label>Código Pix copia e cola</label>
				<textarea readonly><?= htmlspecialchars($payment['qr_code'] ?? 'O código Pix será exibido aqui após configurar o Mercado Pago.') ?></textarea>
			</div>
		</div>

		<?php if($intentSaved && $hasPix): ?>
			<p class="donation-status">Intenção registrada #<?= (int)$intentId ?>. Pix gerado pelo Mercado Pago. Os coins serão creditados automaticamente após a confirmação do pagamento.</p>
		<?php elseif($intentSaved && !empty($payment['enabled']) && empty($payment['ok'])): ?>
			<p class="donation-status warning">Intenção registrada #<?= (int)$intentId ?>, mas o Mercado Pago retornou erro: <?= htmlspecialchars($payment['message'] ?? 'falha desconhecida') ?></p>
		<?php elseif($intentSaved): ?>
			<p class="donation-status">Intenção registrada #<?= (int)$intentId ?>. O pagamento ainda não foi gerado porque o Mercado Pago não está configurado.</p>
		<?php else: ?>
			<p class="donation-status warning">A tabela de intenções ainda não está disponível. Aplique a migração SQL antes de ativar o fluxo.</p>
		<?php endif; ?>

		<div class="donation-actions">
			<a class="eclipse-btn" href="<?= getLink('points') ?>?step=packages">Escolher outro pacote</a>
		</div>
	</div>
<?php
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	csrfProtect();
}

$step = eclipseDonationStep();
$selectedPackageKey = (string)($_POST['package'] ?? $_GET['package'] ?? '');
$selectedPackage = eclipseDonationPackage($packages, $selectedPackageKey);

$account = null;
$profileComplete = false;

if($logged) {
	$account = Account::find($account_logged->getId());
	$profileComplete = eclipseDonationProfileComplete($account);

	if(!$profileComplete) {
		header('Location: ' . getLink('account/change-info'));
		exit;
	}
}

if(!$logged) {
	eclipseDonationRenderShellStart($step);
	eclipseDonationRenderLoginRequired();
	eclipseDonationRenderShellEnd();
}
else {
	eclipseDonationRenderShellStart($step);
	if($step === 'packages') {
		eclipseDonationRenderPackages($packages, $profileComplete);
		eclipseDonationRenderShellEnd();
	}
	else if($step === 'checkout') {
		if(!$selectedPackage || !$profileComplete) {
			eclipseDonationRenderPackages($packages, $profileComplete);
			eclipseDonationRenderShellEnd();
		}
		else {
			$intentId = null;
			$intentSaved = false;
			$payment = null;

			if($db->hasTable('eclipse_donation_intents')) {
				$intentSaved = $db->insert('eclipse_donation_intents', [
					'account_id' => $account->id,
					'package_key' => $selectedPackageKey,
					'amount_brl_cents' => $selectedPackage['amount_cents'],
					'coins' => $selectedPackage['coins'],
					'status' => 'pending_gateway',
					'gateway' => 'pending_pix',
					'payer_name' => $account->rlname,
					'payer_cpf' => $account->cpf,
					'notes' => 'Pix gateway integration pending.',
				]);

				if($intentSaved) {
					$intentId = (int)$db->lastInsertId();
					$intent = [
						'id' => $intentId,
						'package_key' => $selectedPackageKey,
					];
					$payment = eclipseDonationCreateMercadoPagoPix($intent, $selectedPackage, $account);

					if(!empty($payment['enabled'])) {
						$db->update('eclipse_donation_intents', [
							'status' => !empty($payment['ok']) ? ($payment['payment_status'] ?: 'pending') : 'gateway_error',
							'gateway' => 'mercadopago',
							'gateway_reference' => $payment['payment_id'],
							'pix_qr_code' => $payment['qr_code_base64'],
							'pix_copy_paste' => $payment['qr_code'],
							'notes' => !empty($payment['ok']) ? 'Mercado Pago Pix generated.' : ('Mercado Pago error: ' . ($payment['message'] ?? 'unknown')),
							'updated_at' => date('Y-m-d H:i:s'),
						], ['id' => $intentId]);
					}
				}
			}

			eclipseDonationRenderCheckout($selectedPackage, $intentId, $intentSaved, $payment);
			eclipseDonationRenderShellEnd();
		}
	}
	else {
		eclipseDonationRenderIntro(true);
		eclipseDonationRenderShellEnd();
	}
}
?>

<style>
.eclipse-donation-page,
.eclipse-donation-page * {
	box-sizing: border-box;
	color: #210905 !important;
	-webkit-text-fill-color: #210905 !important;
	text-shadow: none !important;
}

.eclipse-donation-page .donation-shell {
	background: linear-gradient(180deg, #f7dfaa 0%, #dfb96f 62%, #c59143 100%);
	border: 2px solid #a56620;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.45), 0 3px 8px rgba(0,0,0,.35);
	padding: 16px;
}

.eclipse-donation-page .donation-title {
	background: linear-gradient(180deg, #40100a 0%, #180403 100%);
	border: 1px solid #b96d22;
	padding: 20px;
	text-align: center;
}

.eclipse-donation-page .donation-title strong {
	display: block;
	color: #fff0c5 !important;
	-webkit-text-fill-color: #fff0c5 !important;
	font: 900 26px Georgia, "Times New Roman", serif;
}

.eclipse-donation-page .donation-title span {
	display: block;
	margin-top: 6px;
	color: #f3d792 !important;
	-webkit-text-fill-color: #f3d792 !important;
	font: 700 12px Verdana, Arial, sans-serif;
}

.eclipse-donation-page .donation-progress {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 8px;
	margin: 14px 0;
}

.eclipse-donation-page .donation-progress span {
	padding: 10px;
	background: rgba(255,244,205,.58);
	border: 1px solid rgba(126,78,27,.55);
	text-align: center;
	font: 900 12px Verdana, Arial, sans-serif;
	text-transform: uppercase;
}

.eclipse-donation-page .donation-progress .active {
	background: linear-gradient(180deg, #09354a, #031620);
	border-color: #d4872e;
	color: #fff4cf !important;
	-webkit-text-fill-color: #fff4cf !important;
}

.eclipse-donation-page .donation-panel {
	background: rgba(255,239,190,.72);
	border: 1px solid rgba(121,73,22,.55);
	padding: 18px;
}

.eclipse-donation-page .donation-panel,
.eclipse-donation-page .donation-panel h2,
.eclipse-donation-page .donation-panel p,
.eclipse-donation-page .donation-panel span,
.eclipse-donation-page .donation-panel strong,
.eclipse-donation-page .donation-panel label,
.eclipse-donation-page .donation-panel textarea {
	color: #210905 !important;
	-webkit-text-fill-color: #210905 !important;
}

.eclipse-donation-page h2 {
	margin: 0 0 12px;
	font: 900 20px Georgia, "Times New Roman", serif;
}

.eclipse-donation-page p {
	margin: 0 0 12px;
	font: 700 13px/1.55 Verdana, Arial, sans-serif;
}

.eclipse-donation-page .donation-actions {
	margin-top: 16px;
	text-align: center;
}

.eclipse-donation-page .donation-warning {
	display: grid;
	gap: 6px;
	margin: 0 0 16px;
	padding: 12px;
	background: #fff2c9;
	border: 1px solid #a15f1c;
}

.eclipse-donation-page .donation-warning a {
	color: #71150b !important;
	-webkit-text-fill-color: #71150b !important;
	font-weight: 900;
}

#ContentColumn #News .BoxContent .eclipse-donation-page .donation-panel,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-panel h2,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-panel p,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-panel span,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-panel strong,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-panel label,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-panel textarea,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-package,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-package strong,
#ContentColumn #News .BoxContent .eclipse-donation-page .package-coins,
#ContentColumn #News .BoxContent .eclipse-donation-page .package-amount,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-summary span,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-summary strong,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-status {
	color: #210905 !important;
	-webkit-text-fill-color: #210905 !important;
	text-shadow: none !important;
}

#ContentColumn #News .BoxContent .eclipse-donation-page .eclipse-btn,
#ContentColumn #News .BoxContent .eclipse-donation-page .eclipse-btn:link,
#ContentColumn #News .BoxContent .eclipse-donation-page .eclipse-btn:visited,
#ContentColumn #News .BoxContent .eclipse-donation-page button.eclipse-btn {
	color: #fff2cf !important;
	-webkit-text-fill-color: #fff2cf !important;
	text-shadow: 0 1px 0 #000 !important;
}

#ContentColumn #News .BoxContent .eclipse-donation-page .eclipse-btn:hover,
#ContentColumn #News .BoxContent .eclipse-donation-page button.eclipse-btn:hover {
	color: #fff !important;
	-webkit-text-fill-color: #fff !important;
}

#ContentColumn #News .BoxContent .eclipse-donation-page .donation-title strong {
	color: #fff0c5 !important;
	-webkit-text-fill-color: #fff0c5 !important;
}

#ContentColumn #News .BoxContent .eclipse-donation-page .donation-title span,
#ContentColumn #News .BoxContent .eclipse-donation-page .donation-progress .active {
	color: #fff4cf !important;
	-webkit-text-fill-color: #fff4cf !important;
}

.eclipse-donation-page .donation-package-list {
	display: grid;
	gap: 12px;
}

.eclipse-donation-page .donation-package {
	display: grid;
	grid-template-columns: 1.2fr 1fr .8fr auto;
	gap: 12px;
	align-items: center;
	padding: 14px;
	background: rgba(255,248,221,.72);
	border: 1px solid rgba(137,83,33,.52);
}

.eclipse-donation-page .donation-package strong,
.eclipse-donation-page .package-coins,
.eclipse-donation-page .package-amount {
	font: 900 13px Verdana, Arial, sans-serif;
}

.eclipse-donation-page button[disabled] {
	opacity: .48;
	cursor: not-allowed;
}

.eclipse-donation-page .donation-summary {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 10px;
	margin-bottom: 16px;
}

.eclipse-donation-page .donation-summary div {
	padding: 12px;
	background: rgba(255,248,221,.72);
	border: 1px solid rgba(137,83,33,.52);
}

.eclipse-donation-page .donation-summary span {
	display: block;
	margin-bottom: 5px;
	font: 800 11px Verdana, Arial, sans-serif;
	text-transform: uppercase;
}

.eclipse-donation-page .donation-summary strong {
	font: 900 14px Verdana, Arial, sans-serif;
}

.eclipse-donation-page .donation-pix-placeholder {
	display: grid;
	grid-template-columns: 210px 1fr;
	gap: 16px;
	align-items: stretch;
}

.eclipse-donation-page .pix-frame {
	display: grid;
	place-items: center;
	min-height: 210px;
	background:
		linear-gradient(45deg, rgba(0,0,0,.05) 25%, transparent 25%),
		linear-gradient(-45deg, rgba(0,0,0,.05) 25%, transparent 25%),
		linear-gradient(45deg, transparent 75%, rgba(0,0,0,.05) 75%),
		linear-gradient(-45deg, transparent 75%, rgba(0,0,0,.05) 75%),
		#fff6d8;
	background-size: 24px 24px;
	background-position: 0 0, 0 12px, 12px -12px, -12px 0;
	border: 2px dashed #8c5b27;
	text-align: center;
}

.eclipse-donation-page .pix-frame span,
.eclipse-donation-page .pix-frame small {
	display: block;
	width: 100%;
	font-weight: 900;
}

.eclipse-donation-page .pix-frame img {
	display: block;
	width: min(100%, 210px);
	height: auto;
}

.eclipse-donation-page .pix-copy label {
	display: block;
	margin-bottom: 8px;
	font: 900 12px Verdana, Arial, sans-serif;
	text-transform: uppercase;
}

.eclipse-donation-page .pix-copy textarea {
	width: 100%;
	min-height: 210px;
	padding: 12px;
	background: #fff6d8;
	border: 1px solid #8c5b27;
	resize: vertical;
	font: 700 12px Verdana, Arial, sans-serif;
}

.eclipse-donation-page .donation-status {
	margin-top: 14px;
	padding: 12px;
	background: rgba(255,248,221,.72);
	border: 1px solid rgba(137,83,33,.52);
}

.eclipse-donation-page .donation-status.warning {
	border-color: #9b2b18;
}

@media (max-width: 760px) {
	.eclipse-donation-page .donation-package,
	.eclipse-donation-page .donation-summary,
	.eclipse-donation-page .donation-pix-placeholder {
		grid-template-columns: 1fr;
	}

	.eclipse-donation-page .donation-progress {
		gap: 4px;
	}
}
</style>
