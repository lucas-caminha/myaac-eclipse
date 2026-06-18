<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Account;

$title = 'Privacidade da Conta';
require __DIR__ . '/base.php';

if(!$logged) {
	return;
}

function eclipseMaskCpfForPrivacy($cpf)
{
	$digits = preg_replace('/\D+/', '', (string)$cpf);
	if(strlen($digits) !== 11) {
		return 'Nao informado';
	}

	return '***.***.***-' . substr($digits, -2);
}

function eclipsePrivacyValue($value, $fallback = 'Nao informado')
{
	$value = trim((string)$value);
	return $value !== '' ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $fallback;
}

$account = Account::find($account_logged->getId());
$requests = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	csrfProtect();

	$requestType = $_POST['request_type'] ?? 'other';
	$message = trim((string)($_POST['message'] ?? ''));
	$allowedTypes = ['access', 'correction', 'deletion', 'anonymization', 'portability', 'consent_revocation', 'other'];

	if(!in_array($requestType, $allowedTypes, true)) {
		$requestType = 'other';
	}

	if($db->hasTable('eclipse_privacy_requests')) {
		$db->insert('eclipse_privacy_requests', [
			'account_id' => $account->id,
			'request_type' => $requestType,
			'message' => $message,
		]);

		$twig->display('success.html.twig', [
			'title' => 'Solicitacao registrada',
			'description' => 'Recebemos sua solicitacao de privacidade. A equipe ira analisar e responder pelo canal de suporte da conta.'
		]);
	}
	else {
		$twig->display('error_box.html.twig', [
			'errors' => ['A tabela de solicitacoes LGPD ainda nao foi aplicada. Execute a migration sql/013-add-lgpd-consents-and-requests.sql.']
		]);
	}
}

if($db->hasTable('eclipse_privacy_requests')) {
	$requests = $db->query('SELECT request_type, status, created_at FROM eclipse_privacy_requests WHERE account_id = ' . (int)$account->id . ' ORDER BY created_at DESC LIMIT 5')->fetchAll();
}
?>
<div class="eclipse-privacy-page eclipse-account-privacy-page">
	<section class="privacy-hero">
		<span>Minha Conta</span>
		<h2>Privacidade da Conta</h2>
		<p>Consulte os principais dados associados a sua conta e registre solicitacoes relacionadas a LGPD.</p>
	</section>

	<section class="privacy-section">
		<h3>Dados da conta</h3>
		<div class="privacy-data-grid">
			<div><span>Conta</span><strong><?= eclipsePrivacyValue($account->name ?? $account_logged->getName()) ?></strong></div>
			<div><span>Email</span><strong><?= eclipsePrivacyValue($account->email ?? '') ?></strong></div>
			<div><span>Nome completo</span><strong><?= eclipsePrivacyValue($account->rlname ?? '') ?></strong></div>
			<div><span>Data de nascimento</span><strong><?= eclipsePrivacyValue($account->birth_date ?? '') ?></strong></div>
			<div><span>CPF</span><strong><?= eclipseMaskCpfForPrivacy($account->cpf ?? '') ?></strong></div>
			<div><span>Localizacao</span><strong><?= eclipsePrivacyValue($account->location ?? '') ?></strong></div>
		</div>
		<p class="privacy-note">CPF e dados de doacao ficam mascarados em tela. A senha nao pode ser consultada porque e armazenada em formato protegido.</p>
	</section>

	<section class="privacy-section">
		<h3>Solicitar atendimento LGPD</h3>
		<form class="privacy-request-form" method="post" action="<?= getLink('account/privacy') ?>">
			<?= csrf(true) ?>
			<label>
				Tipo de solicitacao
				<select name="request_type">
					<option value="access">Acesso aos dados</option>
					<option value="correction">Correcao de dados</option>
					<option value="deletion">Exclusao quando aplicavel</option>
					<option value="anonymization">Anonimizacao quando aplicavel</option>
					<option value="portability">Portabilidade</option>
					<option value="consent_revocation">Revogacao de consentimento opcional</option>
					<option value="other">Outro assunto</option>
				</select>
			</label>
			<label>
				Detalhes
				<textarea name="message" rows="5" maxlength="1200" placeholder="Descreva sua solicitacao sem informar senha ou Recovery Key."></textarea>
			</label>
			<button class="eclipse-btn" type="submit">Enviar solicitacao</button>
		</form>
	</section>

	<?php if(!empty($requests)): ?>
	<section class="privacy-section">
		<h3>Ultimas solicitacoes</h3>
		<table class="privacy-request-table">
			<tr><th>Tipo</th><th>Status</th><th>Data</th></tr>
			<?php foreach($requests as $request): ?>
				<tr>
					<td><?= htmlspecialchars($request['request_type']) ?></td>
					<td><?= htmlspecialchars($request['status']) ?></td>
					<td><?= htmlspecialchars($request['created_at']) ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</section>
	<?php endif; ?>
</div>

