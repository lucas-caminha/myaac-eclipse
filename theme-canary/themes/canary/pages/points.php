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

$title = function_exists('t') ? t('points.title') : 'Support Eclipse OT';

$packages = [
	'starter' => [
		'label_key' => 'points.package.starter',
		'amount_cents' => 1000,
		'coins' => 150,
	],
	'adventurer' => [
		'label_key' => 'points.package.adventurer',
		'amount_cents' => 2500,
		'coins' => 350,
	],
	'guardian' => [
		'label_key' => 'points.package.guardian',
		'amount_cents' => 5000,
		'coins' => 800,
	],
	'eclipse' => [
		'label_key' => 'points.package.eclipse',
		'amount_cents' => 10000,
		'coins' => 1800,
		'badge_key' => 'points.best_value',
	],
	'supreme' => [
		'label_key' => 'points.package.supreme',
		'amount_cents' => 20000,
		'coins' => 4000,
	],
];

function eclipseDonationText(string $key, array $params = [], string $fallback = ''): string
{
	if(function_exists('t')) {
		$value = t($key, $params);
		if($value !== $key) {
			return $value;
		}
	}

	foreach($params as $name => $value) {
		$fallback = str_replace('{' . $name . '}', (string)$value, $fallback);
	}

	return $fallback;
}

function eclipseDonationPackageLabel(array $package): string
{
	return eclipseDonationText($package['label_key'] ?? '', [], $package['label'] ?? '');
}

function eclipseDonationPackageBadge(array $package): string
{
	if(empty($package['badge_key'])) {
		return '';
	}

	return eclipseDonationText($package['badge_key'], [], $package['badge'] ?? '');
}

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
			'message' => eclipseDonationText('points.gateway_not_configured', [], 'Mercado Pago is not configured yet.'),
		];
	}

	$intentId = (int)$intent['id'];
	$payload = [
		'transaction_amount' => round($package['amount_cents'] / 100, 2),
		'description' => eclipseDonationText('points.payment_description', ['package' => eclipseDonationPackageLabel($package)], 'Eclipse OT donation - {package}'),
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
		'message' => $response['ok'] ? eclipseDonationText('points.pix_generated', [], 'Pix generated by Mercado Pago.') : ($body['message'] ?? eclipseDonationText('points.pix_failed', [], 'Failed to generate Pix through Mercado Pago.')),
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
		'intro' => eclipseDonationText('points.step_intro', [], 'Guidance'),
		'packages' => eclipseDonationText('points.step_packages', [], 'Coins'),
		'checkout' => eclipseDonationText('points.step_checkout', [], 'Pix'),
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
	echo '<strong>' . eclipseDonationText('points.hero_title', [], 'Support Eclipse OT') . '</strong>';
	echo '<span>' . eclipseDonationText('points.hero_subtitle', [], 'Voluntary donation to keep the server evolving.') . '</span>';
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
		<h2><?= eclipseDonationText('points.before_continue', [], 'Before continuing') ?></h2>
		<p><?= eclipseDonationText('points.intro_1', [], 'This contribution is a voluntary donation to support Eclipse OT. It helps maintain infrastructure, development, maintenance and community improvements.') ?></p>
		<p><?= eclipseDonationText('points.intro_2', [], 'The Eclipse Coins shown in the next steps are an in-server thank you. They do not represent an investment, financial product, withdrawal, automatic refund or guarantee of permanent availability.') ?></p>
		<p><?= eclipseDonationText('points.intro_3', [], 'The server may go through adjustments, events, balancing and maintenance. By supporting, you declare that you are aware of the server rules and that the contribution is made to support the project.') ?></p>
		<div class="donation-actions">
			<?php if($logged): ?>
				<a class="eclipse-btn" href="<?= getLink('points') ?>?step=packages"><?= eclipseDonationText('points.continue', [], 'Continue') ?></a>
			<?php else: ?>
				<a class="eclipse-btn" href="<?= getLink('account/manage') ?>"><?= eclipseDonationText('points.login_to_continue', [], 'Log in to continue') ?></a>
			<?php endif; ?>
		</div>
	</div>
<?php
}

function eclipseDonationRenderLoginRequired(): void
{
?>
	<div class="donation-panel donation-error">
		<h2><?= eclipseDonationText('points.restricted_access', [], 'Restricted access') ?></h2>
		<p><?= eclipseDonationText('points.login_required', [], 'You need to be logged in to access the Eclipse OT support page.') ?></p>
		<div class="donation-actions">
			<a class="eclipse-btn" href="<?= getLink('account/manage') ?>"><?= eclipseDonationText('points.login_account', [], 'Log in to your account') ?></a>
		</div>
	</div>
<?php
}

function eclipseDonationRenderPackages(array $packages, bool $profileComplete): void
{
?>
	<div class="donation-panel">
		<h2><?= eclipseDonationText('points.choose_coins', [], 'Choose thank-you coins') ?></h2>
		<p><?= eclipseDonationText('points.choose_coins_hint', [], 'Select the support tier. Pix generation is integrated when the gateway is configured, and this step prepares the order.') ?></p>

		<?php if(!$profileComplete): ?>
			<div class="donation-warning">
				<strong><?= eclipseDonationText('points.pending_data', [], 'Pending data') ?></strong>
				<span><?= eclipseDonationText('points.pending_data_hint', [], 'To continue with donations, complete full name, birth date and CPF in your account information.') ?></span>
				<a href="<?= getLink('account/change-info') ?>"><?= eclipseDonationText('points.update_profile', [], 'Update profile') ?></a>
			</div>
		<?php endif; ?>

		<div class="donation-warning">
			<strong><?= eclipseDonationText('points.duo_donate', [], 'Duo Donate') ?></strong>
			<span><?= eclipseDonationText('points.duo_donate_hint', [], 'Create support with two characters, partner acceptance, a shared outfit choice and a 2-hour boost for both.') ?></span>
			<a href="<?= getLink('duo-donate') ?>"><?= eclipseDonationText('points.open_duo_donate', [], 'Open Duo Donate') ?></a>
		</div>

		<div class="donation-package-list">
			<?php foreach($packages as $key => $package): ?>
				<form class="donation-package<?= !empty($package['badge']) ? ' is-featured' : '' ?>" method="post" action="<?= getLink('points') ?>">
					<?= csrf(true) ?>
					<input type="hidden" name="step" value="checkout">
					<input type="hidden" name="package" value="<?= htmlspecialchars($key) ?>">
					<strong>
						<?= htmlspecialchars(eclipseDonationPackageLabel($package)) ?>
						<?php $badge = eclipseDonationPackageBadge($package); if($badge !== ''): ?>
							<em><?= htmlspecialchars($badge) ?></em>
						<?php endif; ?>
					</strong>
					<span class="package-coins"><?= number_format($package['coins'], 0, ',', '.') ?> Eclipse Coins</span>
					<span class="package-amount"><?= eclipseDonationMoney($package['amount_cents']) ?></span>
					<button class="eclipse-btn" type="submit" <?= $profileComplete ? '' : 'disabled' ?>><?= eclipseDonationText('points.select_package', [], 'Select') ?></button>
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
		<h2><?= eclipseDonationText('points.checkout_title', [], 'Pix checkout') ?></h2>
		<div class="donation-summary">
			<div>
				<span><?= eclipseDonationText('points.support_tier', [], 'Support tier') ?></span>
				<strong><?= htmlspecialchars(eclipseDonationPackageLabel($package)) ?></strong>
			</div>
			<div>
				<span><?= eclipseDonationText('points.contribution', [], 'Contribution') ?></span>
				<strong><?= eclipseDonationMoney($package['amount_cents']) ?></strong>
			</div>
			<div>
				<span><?= eclipseDonationText('points.thank_you', [], 'Thank you') ?></span>
				<strong><?= number_format($package['coins'], 0, ',', '.') ?> coins</strong>
			</div>
		</div>

		<div class="donation-pix-placeholder">
			<div class="pix-frame">
				<?php if(!empty($payment['qr_code_base64'])): ?>
					<img src="data:image/jpeg;base64,<?= htmlspecialchars($payment['qr_code_base64']) ?>" alt="QR Code Pix Mercado Pago">
				<?php else: ?>
					<span>QR Code Pix</span>
					<small><?= eclipseDonationGatewayEnabled() ? eclipseDonationText('points.awaiting_generation', [], 'Awaiting generation') : eclipseDonationText('points.integration_pending', [], 'Integration pending') ?></small>
				<?php endif; ?>
			</div>
			<div class="pix-copy">
				<label><?= eclipseDonationText('points.pix_copy_label', [], 'Pix copy and paste code') ?></label>
				<textarea readonly><?= htmlspecialchars($payment['qr_code'] ?? eclipseDonationText('points.pix_placeholder', [], 'The Pix code will be shown here after Mercado Pago is configured.')) ?></textarea>
			</div>
		</div>

		<?php if($intentSaved && $hasPix): ?>
			<p class="donation-status"><?= eclipseDonationText('points.status_pix_generated', ['id' => (int)$intentId], 'Intent #{id} registered. Pix generated by Mercado Pago. Coins will be credited automatically after payment confirmation.') ?></p>
		<?php elseif($intentSaved && !empty($payment['enabled']) && empty($payment['ok'])): ?>
			<p class="donation-status warning"><?= eclipseDonationText('points.status_gateway_error', ['id' => (int)$intentId, 'message' => htmlspecialchars($payment['message'] ?? eclipseDonationText('points.unknown_failure', [], 'unknown failure'))], 'Intent #{id} registered, but Mercado Pago returned an error: {message}') ?></p>
		<?php elseif($intentSaved): ?>
			<p class="donation-status"><?= eclipseDonationText('points.status_payment_not_generated', ['id' => (int)$intentId], 'Intent #{id} registered. Payment was not generated because Mercado Pago is not configured.') ?></p>
		<?php else: ?>
			<p class="donation-status warning"><?= eclipseDonationText('points.status_table_missing', [], 'The donation intents table is not available yet. Apply the SQL migration before enabling the flow.') ?></p>
		<?php endif; ?>

		<div class="donation-actions">
			<a class="eclipse-btn" href="<?= getLink('points') ?>?step=packages"><?= eclipseDonationText('points.choose_another_package', [], 'Choose another package') ?></a>
		</div>
	</div>
<?php
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	csrfProtect();
}

$step = eclipseDonationStep();
$isPostRequest = $_SERVER['REQUEST_METHOD'] === 'POST';
if($step === 'checkout' && !$isPostRequest) {
	$step = 'packages';
}

$selectedPackageKey = (string)($_POST['package'] ?? '');
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
					'payer_cpf' => null,
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

.eclipse-donation-page .donation-package.is-featured {
	background: linear-gradient(180deg, rgba(255,248,221,.92) 0%, rgba(248,219,142,.9) 100%);
	border-color: #a15f1c;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.55), 0 0 0 2px rgba(111,21,10,.12);
}

.eclipse-donation-page .donation-package strong,
.eclipse-donation-page .package-coins,
.eclipse-donation-page .package-amount {
	font: 900 13px Verdana, Arial, sans-serif;
}

.eclipse-donation-page .donation-package strong em {
	display: inline-block;
	margin-left: 8px;
	padding: 3px 7px;
	background: linear-gradient(180deg, #5a1208, #280503);
	border: 1px solid #c9842f;
	color: #fff0c5 !important;
	-webkit-text-fill-color: #fff0c5 !important;
	font: 900 10px Verdana, Arial, sans-serif;
	font-style: normal;
	text-transform: uppercase;
	vertical-align: middle;
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
