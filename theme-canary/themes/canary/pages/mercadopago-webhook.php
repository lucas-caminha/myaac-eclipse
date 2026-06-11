<?php
/**
 * Mercado Pago webhook receiver for Eclipse OT donations.
 */
defined('MYAAC') or die('Direct access not allowed!');

function eclipseMpWebhookEnv(string $name, ?string $fallback = null): ?string
{
	$value = getenv($name);
	return $value === false || $value === '' ? $fallback : $value;
}

function eclipseMpWebhookJson(int $status, array $payload): void
{
	http_response_code($status);
	header('Content-Type: application/json');
	echo json_encode($payload);
}

function eclipseMpWebhookGetJson(string $url, string $accessToken): array
{
	if(!function_exists('curl_init')) {
		return ['ok' => false, 'status' => 0, 'body' => null, 'error' => 'PHP cURL extension is not available.'];
	}

	$ch = curl_init($url);
	curl_setopt_array($ch, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => [
			'Authorization: Bearer ' . $accessToken,
			'Content-Type: application/json',
		],
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

function eclipseMpWebhookPaymentId(): ?string
{
	$raw = file_get_contents('php://input');
	$body = $raw ? json_decode($raw, true) : [];

	if(isset($_GET['data_id'])) return (string)$_GET['data_id'];
	if(isset($_GET['id'])) return (string)$_GET['id'];
	if(isset($_GET['data']['id'])) return (string)$_GET['data']['id'];
	if(isset($body['data']['id'])) return (string)$body['data']['id'];
	if(isset($body['id'])) return (string)$body['id'];

	return null;
}

function eclipseMpWebhookCreditDonation(PDO $db, array $intent, array $payment): bool
{
	if($intent['status'] === 'paid') {
		return true;
	}

	$db->beginTransaction();
	try {
		$db->query(
			'UPDATE `accounts` SET `coins` = `coins` + ' . (int)$intent['coins'] .
			' WHERE `id` = ' . (int)$intent['account_id'] . ' LIMIT 1'
		);

		if($db->hasTable('coins_transactions')) {
			$db->insert('coins_transactions', [
				'account_id' => (int)$intent['account_id'],
				'type' => 1,
				'coin_type' => 1,
				'amount' => (int)$intent['coins'],
				'description' => 'Doacao Eclipse OT via Mercado Pago #' . ($payment['id'] ?? ''),
			]);
		}

		$db->update('eclipse_donation_intents', [
			'status' => 'paid',
			'gateway' => 'mercadopago',
			'gateway_reference' => (string)($payment['id'] ?? $intent['gateway_reference']),
			'updated_at' => date('Y-m-d H:i:s'),
			'confirmed_at' => date('Y-m-d H:i:s'),
			'notes' => 'Mercado Pago payment approved and coins credited.',
		], ['id' => (int)$intent['id']]);

		$db->commit();
		return true;
	}
	catch(Throwable $e) {
		$db->rollBack();
		throw $e;
	}
}

$accessToken = eclipseMpWebhookEnv('MERCADOPAGO_ACCESS_TOKEN');
if(!$accessToken) {
	eclipseMpWebhookJson(503, ['ok' => false, 'error' => 'mercadopago_not_configured']);
	return;
}

$paymentId = eclipseMpWebhookPaymentId();
if(!$paymentId) {
	eclipseMpWebhookJson(400, ['ok' => false, 'error' => 'missing_payment_id']);
	return;
}

$paymentResponse = eclipseMpWebhookGetJson('https://api.mercadopago.com/v1/payments/' . urlencode($paymentId), $accessToken);
if(!$paymentResponse['ok'] || !is_array($paymentResponse['body'])) {
	eclipseMpWebhookJson(502, ['ok' => false, 'error' => 'payment_lookup_failed']);
	return;
}

$payment = $paymentResponse['body'];
$externalReference = (string)($payment['external_reference'] ?? '');
$intentId = preg_match('/^eclipse-donation-(\d+)$/', $externalReference, $matches) ? (int)$matches[1] : 0;

if($intentId <= 0) {
	eclipseMpWebhookJson(202, ['ok' => true, 'ignored' => 'unknown_external_reference']);
	return;
}

$intent = $db->select('eclipse_donation_intents', ['id' => $intentId], 1);
if(!$intent) {
	eclipseMpWebhookJson(404, ['ok' => false, 'error' => 'intent_not_found']);
	return;
}

if(($payment['status'] ?? '') === 'approved') {
	eclipseMpWebhookCreditDonation($db, $intent, $payment);
	eclipseMpWebhookJson(200, ['ok' => true, 'credited' => true]);
	return;
}

$db->update('eclipse_donation_intents', [
	'status' => (string)($payment['status'] ?? 'unknown'),
	'gateway' => 'mercadopago',
	'gateway_reference' => (string)($payment['id'] ?? $paymentId),
	'updated_at' => date('Y-m-d H:i:s'),
	'notes' => 'Mercado Pago webhook status: ' . (string)($payment['status'] ?? 'unknown'),
], ['id' => (int)$intent['id']]);

eclipseMpWebhookJson(200, ['ok' => true, 'credited' => false, 'status' => $payment['status'] ?? null]);
