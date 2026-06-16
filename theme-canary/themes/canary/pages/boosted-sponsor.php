<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Account;

require_once __DIR__ . '/boosted-sponsor-common.php';

$title = 'Patrocinar Boosted';

function eclipseBoostedSponsorStep(): string
{
	$step = $_POST['step'] ?? $_GET['step'] ?? 'intro';
	return in_array($step, ['intro', 'select', 'checkout'], true) ? $step : 'intro';
}

function eclipseBoostedSponsorType(): string
{
	$type = $_POST['target_type'] ?? $_GET['target_type'] ?? $_GET['type'] ?? 'boss';
	return $type === 'creature' ? 'creature' : 'boss';
}

function eclipseBoostedSponsorRenderProgress(string $step): void
{
	$steps = [
		'intro' => 'Explicacao',
		'select' => 'Escolha',
		'checkout' => 'Confirmacao',
	];

	echo '<div class="boosted-sponsor-progress">';
	foreach($steps as $key => $label) {
		$class = $key === $step ? 'active' : '';
		echo '<span class="' . $class . '">' . $label . '</span>';
	}
	echo '</div>';
}

function eclipseBoostedSponsorRenderShellStart(string $step): void
{
	echo '<div class="eclipse-boosted-sponsor-page">';
	echo '<div class="boosted-sponsor-shell">';
	echo '<div class="boosted-sponsor-title">';
	echo '<strong>Patrocine o proximo boosted</strong>';
	echo '<span>Escolha um boss por 250 Tibia Coins ou uma criatura por 300 Tibia Coins para o proximo server save.</span>';
	echo '</div>';
	eclipseBoostedSponsorRenderProgress($step);
}

function eclipseBoostedSponsorRenderShellEnd(): void
{
	echo '</div></div>';
}

function eclipseBoostedSponsorRenderLoginRequired(): void
{
?>
	<div class="boosted-sponsor-panel boosted-sponsor-error">
		<h2>Acesso restrito</h2>
		<p>Voce precisa estar logado para patrocinar o proximo boosted do Eclipse OT.</p>
		<div class="boosted-sponsor-actions">
			<a class="eclipse-btn" href="<?= getLink('account/manage') ?>">Entrar na conta</a>
		</div>
	</div>
<?php
}

function eclipseBoostedSponsorRenderIntro(DateTimeImmutable $nextServerSave, ?array $bossSlot, ?array $creatureSlot, bool $logged): void
{
?>
	<div class="boosted-sponsor-panel">
		<h2>Como funciona</h2>
		<p>Este apoio permite definir o boss boosted ou a creature boosted do proximo server save usando Tibia Coins da propria conta.</p>
		<p>O custo e fixo: <strong>250 Tibia Coins para boss</strong> e <strong>300 Tibia Coins para creature</strong>. Depois de confirmado, o alvo entra no proximo server save e fica 10 dias em cooldown.</p>
		<p>O proximo fechamento desta rodada acontece em <strong><?= eclipseBoostedSponsorFormatDate($nextServerSave) ?></strong>.</p>

		<div class="boosted-sponsor-slot-grid">
			<div class="boosted-sponsor-slot-card<?= $bossSlot ? ' is-locked' : '' ?>">
				<small>Slot de Boss</small>
				<strong><?= $bossSlot ? htmlspecialchars($bossSlot['target_name']) : 'Disponivel' ?></strong>
				<span><?= $bossSlot ? 'Ja confirmado para o proximo server save.' : 'Livre para patrocinio.' ?></span>
			</div>
			<div class="boosted-sponsor-slot-card<?= $creatureSlot ? ' is-locked' : '' ?>">
				<small>Slot de Creature</small>
				<strong><?= $creatureSlot ? htmlspecialchars($creatureSlot['target_name']) : 'Disponivel' ?></strong>
				<span><?= $creatureSlot ? 'Ja confirmado para o proximo server save.' : 'Livre para patrocinio.' ?></span>
			</div>
		</div>

		<div class="boosted-sponsor-actions">
			<?php if($logged): ?>
				<a class="eclipse-btn" href="<?= getLink('boosted-sponsor') ?>?step=select&type=boss">Escolher boosted</a>
			<?php else: ?>
				<a class="eclipse-btn" href="<?= getLink('account/manage') ?>">Entrar para continuar</a>
			<?php endif; ?>
		</div>
	</div>
<?php
}

function eclipseBoostedSponsorRenderSelection(
	string $type,
	array $targets,
	DateTimeImmutable $nextServerSave,
	?array $slot,
	array $recentBosses,
	array $recentCreatures,
	int $balance
): void {
	$typeLabel = eclipseBoostedSponsorTypeLabel($type);
	$otherType = $type === 'boss' ? 'creature' : 'boss';
	$priceCoins = eclipseBoostedSponsorPriceCoins($type);
?>
	<div class="boosted-sponsor-panel">
		<div class="boosted-sponsor-tabs">
			<a class="<?= $type === 'boss' ? 'is-active' : '' ?>" href="<?= getLink('boosted-sponsor') ?>?step=select&type=boss">Boss</a>
			<a class="<?= $type === 'creature' ? 'is-active' : '' ?>" href="<?= getLink('boosted-sponsor') ?>?step=select&type=creature">Creature</a>
		</div>

		<div class="boosted-sponsor-summary-grid">
			<div class="boosted-sponsor-summary-card">
				<small>Proximo server save</small>
				<strong><?= eclipseBoostedSponsorFormatDate($nextServerSave) ?></strong>
				<span>So existe um slot de <?= strtolower($typeLabel) ?> por rodada.</span>
			</div>
			<div class="boosted-sponsor-summary-card">
				<small>Seu saldo atual</small>
				<strong><?= number_format($balance, 0, ',', '.') ?> Tibia Coins</strong>
				<span>Custo deste patrocinio: <?= number_format($priceCoins, 0, ',', '.') ?> Tibia Coins.</span>
			</div>
			<div class="boosted-sponsor-summary-card<?= $slot ? ' is-locked' : '' ?>">
				<small>Slot atual de <?= strtolower($typeLabel) ?></small>
				<strong><?= $slot ? htmlspecialchars($slot['target_name']) : 'Disponivel' ?></strong>
				<span><?= $slot ? 'Ja existe um patrocinio ativo para esta vaga.' : 'Ainda da tempo de escolher.' ?></span>
			</div>
		</div>

		<?php if($slot): ?>
			<div class="boosted-sponsor-warning">
				<strong>Slot temporariamente indisponivel</strong>
				<span>O slot de <?= strtolower($typeLabel) ?> para o proximo server save ja foi reservado ou pago. Voce ainda pode verificar o slot de <?= $otherType === 'boss' ? 'boss' : 'creature' ?>.</span>
			</div>
		<?php else: ?>
			<div class="boosted-sponsor-results-header">
				<div>
					<span>Categoria</span>
					<h3><?= $typeLabel ?> boosted</h3>
					<p><strong data-boosted-visible-count><?= count($targets) ?></strong> alvos elegiveis</p>
				</div>
				<div class="boosted-sponsor-actions-inline">
					<input class="eclipse-plugin-search" type="search" placeholder="Buscar nome" data-boosted-search>
				</div>
			</div>

			<div class="boosted-sponsor-grid" data-boosted-grid>
				<?php foreach($targets as $target): ?>
					<form class="boosted-sponsor-card" method="post" action="<?= getLink('boosted-sponsor') ?>" data-name="<?= htmlspecialchars(strtolower($target['name'])) ?>">
						<?= csrf(true) ?>
						<input type="hidden" name="step" value="checkout">
						<input type="hidden" name="target_type" value="<?= htmlspecialchars($type) ?>">
						<input type="hidden" name="target_id" value="<?= (int)$target['id'] ?>">
						<div class="boosted-sponsor-card-portrait"><img src="<?= htmlspecialchars($target['img_link']) ?>" alt="<?= htmlspecialchars($target['name']) ?>"></div>
						<div class="boosted-sponsor-card-body">
							<small><?= htmlspecialchars($target['category']) ?></small>
							<strong><?= htmlspecialchars($target['name']) ?></strong>
							<span>Vida <?= number_format((int)$target['health'], 0, ',', '.') ?> • EXP <?= number_format((int)$target['exp'], 0, ',', '.') ?></span>
						</div>
						<button class="eclipse-btn" type="submit">Patrocinar por <?= number_format($priceCoins, 0, ',', '.') ?> Tibia Coins</button>
					</form>
				<?php endforeach; ?>
			</div>
			<div class="eclipse-plugin-empty boosted-sponsor-empty" data-boosted-empty hidden>Nenhum alvo encontrado com esse nome.</div>

			<?php if(empty($targets)): ?>
				<div class="eclipse-plugin-empty">Nao ha alvos disponiveis para este slot no momento.</div>
			<?php endif; ?>
		<?php endif; ?>

		<div class="boosted-sponsor-history">
			<div>
				<h4>Ultimos bosses aplicados</h4>
				<ul>
					<?php foreach($recentBosses as $entry): ?>
						<li><strong><?= htmlspecialchars($entry['target_name']) ?></strong><span><?= date('d/m/Y', strtotime((string)$entry['scheduled_for_date'])) ?></span></li>
					<?php endforeach; ?>
					<?php if(empty($recentBosses)): ?><li><span>Nenhum boss patrocinado ainda.</span></li><?php endif; ?>
				</ul>
			</div>
			<div>
				<h4>Ultimas creatures aplicadas</h4>
				<ul>
					<?php foreach($recentCreatures as $entry): ?>
						<li><strong><?= htmlspecialchars($entry['target_name']) ?></strong><span><?= date('d/m/Y', strtotime((string)$entry['scheduled_for_date'])) ?></span></li>
					<?php endforeach; ?>
					<?php if(empty($recentCreatures)): ?><li><span>Nenhuma creature patrocinada ainda.</span></li><?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
<?php
}

function eclipseBoostedSponsorRenderCheckout(array $target, DateTimeImmutable $nextServerSave, ?int $orderId, bool $orderSaved, int $priceCoins, int $balanceAfter): void
{
?>
	<div class="boosted-sponsor-panel boosted-sponsor-checkout">
		<h2>Patrocinio confirmado</h2>
		<div class="boosted-sponsor-target-summary">
			<div class="boosted-sponsor-target-portrait"><img src="<?= htmlspecialchars($target['img_link']) ?>" alt="<?= htmlspecialchars($target['name']) ?>"></div>
			<div>
				<small><?= eclipseBoostedSponsorTypeLabel((string)($_POST['target_type'] ?? 'boss')) ?></small>
				<strong><?= htmlspecialchars($target['name']) ?></strong>
				<span><?= htmlspecialchars($target['category']) ?> • entra no server save de <?= $nextServerSave->format('d/m/Y') ?></span>
			</div>
		</div>

		<div class="boosted-sponsor-donation-summary">
			<div><span>Custo</span><strong><?= number_format($priceCoins, 0, ',', '.') ?> Tibia Coins</strong></div>
			<div><span>Cooldown depois da entrada</span><strong>10 dias</strong></div>
			<div><span>Saldo restante</span><strong><?= number_format($balanceAfter, 0, ',', '.') ?> Tibia Coins</strong></div>
		</div>

		<?php if($orderSaved): ?>
			<p class="boosted-sponsor-status">Pedido #<?= (int)$orderId ?> confirmado com sucesso. O alvo ja esta garantido para o proximo server save.</p>
		<?php else: ?>
			<p class="boosted-sponsor-status warning">Nao foi possivel registrar o patrocinio. Verifique se a migracao SQL ja foi aplicada.</p>
		<?php endif; ?>

		<div class="boosted-sponsor-actions">
			<a class="eclipse-btn" href="<?= getLink('boosted-sponsor') ?>?step=select&type=<?= htmlspecialchars((string)($_POST['target_type'] ?? 'boss')) ?>">Escolher outro alvo</a>
		</div>
	</div>
<?php
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	csrfProtect();
}

$step = eclipseBoostedSponsorStep();
$type = eclipseBoostedSponsorType();
$nextServerSave = eclipseBoostedSponsorNextServerSaveDate();
$scheduledForDate = eclipseBoostedSponsorDateKey($nextServerSave);

$bossSlot = $db->hasTable('eclipse_boosted_sponsorships') ? eclipseBoostedSponsorActiveSlot($db, 'boss', $scheduledForDate) : null;
$creatureSlot = $db->hasTable('eclipse_boosted_sponsorships') ? eclipseBoostedSponsorActiveSlot($db, 'creature', $scheduledForDate) : null;

$account = null;
$profileComplete = false;
$accountBalance = 0;

if($logged) {
	$account = Account::find($account_logged->getId());
	$profileComplete = eclipseBoostedSponsorProfileComplete($account);
	$accountBalance = (int)$account->getCustomField('coins');

	if(!$profileComplete) {
		header('Location: ' . getLink('account/change-info'));
		exit;
	}
}

eclipseBoostedSponsorRenderShellStart($step);

if(!$logged) {
	eclipseBoostedSponsorRenderLoginRequired();
	eclipseBoostedSponsorRenderShellEnd();
	return;
}

if(!$db->hasTable('eclipse_boosted_sponsorships')) {
	echo '<div class="boosted-sponsor-panel boosted-sponsor-error"><h2>Migracao pendente</h2><p>A tabela de patrocinio boosted ainda nao existe. Aplique a migracao SQL <code>sql/012-add-boosted-sponsorships.sql</code> antes de ativar este fluxo.</p></div>';
	eclipseBoostedSponsorRenderShellEnd();
	return;
}

if($step === 'checkout') {
	$targetId = (int)($_POST['target_id'] ?? 0);
	$target = eclipseBoostedSponsorLoadTarget($db, $type, $targetId);
	$currentSlot = $type === 'boss' ? $bossSlot : $creatureSlot;
	$priceCoins = eclipseBoostedSponsorPriceCoins($type);

	if(!$target) {
		error('O alvo selecionado nao foi encontrado.');
		$step = 'select';
	}
	else if($currentSlot) {
		error('Este slot ja foi reservado para o proximo server save.');
		$step = 'select';
	}
	else if(eclipseBoostedSponsorCooldownConflict($db, $type, $target['name'], $scheduledForDate)) {
		error('Este alvo ainda esta em cooldown e nao pode voltar agora.');
		$step = 'select';
	}
	else {
		$orderId = null;
		$orderSaved = false;
		$balanceAfter = $accountBalance;

		try {
			$db->beginTransaction();

			$slotConflict = eclipseBoostedSponsorActiveSlot($db, $type, $scheduledForDate);
			if($slotConflict) {
				throw new RuntimeException('Este slot acabou de ser reservado por outro apoio. Escolha outro tipo ou volte em alguns minutos.');
			}

			$cooldownConflict = eclipseBoostedSponsorCooldownConflict($db, $type, $target['name'], $scheduledForDate);
			if($cooldownConflict) {
				throw new RuntimeException('Este alvo entrou em cooldown enquanto voce navegava pela pagina.');
			}

			$coinsStmt = $db->prepare('SELECT coins FROM accounts WHERE id = ? FOR UPDATE');
			$coinsStmt->execute([(int)$account->id]);
			$accountCoins = $coinsStmt->fetch(PDO::FETCH_ASSOC);
			$currentCoins = (int)($accountCoins['coins'] ?? 0);
			if($currentCoins < $priceCoins) {
				throw new RuntimeException('Saldo insuficiente. Voce precisa de ' . number_format($priceCoins, 0, ',', '.') . ' Tibia Coins para este patrocinio.');
			}

			$balanceAfter = $currentCoins - $priceCoins;
			$cooldownUntil = $nextServerSave->modify('+10 days')->format('Y-m-d');

			$orderSaved = $db->insert('eclipse_boosted_sponsorships', [
				'account_id' => $account->id,
				'target_type' => $type,
				'target_monster_id' => (int)$target['id'],
				'target_name' => $target['name'],
				'target_category' => $target['category'],
				'amount_coins' => $priceCoins,
				'status' => 'paid',
				'gateway' => 'coins',
				'scheduled_for_date' => $scheduledForDate,
				'cooldown_until' => $cooldownUntil,
				'payer_name' => $account->rlname,
				'payer_cpf' => $account->cpf,
				'confirmed_at' => date('Y-m-d H:i:s'),
				'notes' => 'Boosted sponsorship purchased with Tibia Coins.',
			]);

			if(!$orderSaved) {
				throw new RuntimeException('Nao foi possivel criar o pedido de patrocinio.');
			}

			$db->prepare('UPDATE accounts SET coins = coins - ? WHERE id = ?')->execute([$priceCoins, (int)$account->id]);
			if($db->hasTable('coins_transactions')) {
				$db->insert('coins_transactions', [
					'account_id' => (int)$account->id,
					'type' => 2,
					'coin_type' => 1,
					'amount' => $priceCoins,
					'description' => 'Patrocinio de boosted ' . eclipseBoostedSponsorTypeLabel($type) . ': ' . $target['name'],
				]);
			}

			$orderId = (int)$db->lastInsertId();
			$db->commit();

			eclipseBoostedSponsorRenderCheckout($target, $nextServerSave, $orderId, true, $priceCoins, $balanceAfter);
			eclipseBoostedSponsorRenderShellEnd();
			return;
		}
		catch(Throwable $exception) {
			if($db->inTransaction()) {
				$db->rollBack();
			}
			error(htmlspecialchars($exception->getMessage()));
			$step = 'select';
		}
	}
}

if($step === 'select') {
	$targets = eclipseBoostedSponsorCandidates($db, $type, $scheduledForDate);
	$slot = $type === 'boss' ? $bossSlot : $creatureSlot;
	$recentBosses = eclipseBoostedSponsorRecentApplied($db, 'boss');
	$recentCreatures = eclipseBoostedSponsorRecentApplied($db, 'creature');

	eclipseBoostedSponsorRenderSelection($type, $targets, $nextServerSave, $slot, $recentBosses, $recentCreatures, $accountBalance);
	eclipseBoostedSponsorRenderShellEnd();
}
else {
	eclipseBoostedSponsorRenderIntro($nextServerSave, $bossSlot, $creatureSlot, true);
	eclipseBoostedSponsorRenderShellEnd();
}
?>
<style>
.eclipse-boosted-sponsor-page,
.eclipse-boosted-sponsor-page * {
	box-sizing: border-box;
	color: #210905 !important;
	-webkit-text-fill-color: #210905 !important;
	text-shadow: none !important;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-shell {
	background: linear-gradient(180deg, #f7dfaa 0%, #dfb96f 62%, #c59143 100%);
	border: 2px solid #a56620;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.45), 0 3px 8px rgba(0,0,0,.35);
	padding: 16px;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-title {
	background: linear-gradient(180deg, #40100a 0%, #180403 100%);
	border: 1px solid #b96d22;
	padding: 20px;
	text-align: center;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-title strong {
	display: block;
	color: #fff0c5 !important;
	-webkit-text-fill-color: #fff0c5 !important;
	font: 900 26px Georgia, "Times New Roman", serif;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-title span {
	display: block;
	margin-top: 6px;
	color: #f3d792 !important;
	-webkit-text-fill-color: #f3d792 !important;
	font: 700 12px Verdana, Arial, sans-serif;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-progress {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 8px;
	margin: 14px 0;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-progress span {
	padding: 10px;
	background: rgba(255,244,205,.58);
	border: 1px solid rgba(126,78,27,.55);
	text-align: center;
	font: 900 12px Verdana, Arial, sans-serif;
	text-transform: uppercase;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-progress .active {
	background: linear-gradient(180deg, #09354a, #031620);
	border-color: #d4872e;
	color: #fff4cf !important;
	-webkit-text-fill-color: #fff4cf !important;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-panel {
	display: grid;
	gap: 16px;
	background: rgba(255,239,190,.72);
	border: 1px solid rgba(121,73,22,.55);
	padding: 18px;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-panel h2,
.eclipse-boosted-sponsor-page .boosted-sponsor-panel h3,
.eclipse-boosted-sponsor-page .boosted-sponsor-panel h4,
.eclipse-boosted-sponsor-page .boosted-sponsor-panel p,
.eclipse-boosted-sponsor-page .boosted-sponsor-panel span,
.eclipse-boosted-sponsor-page .boosted-sponsor-panel strong,
.eclipse-boosted-sponsor-page .boosted-sponsor-panel small {
	color: #210905 !important;
	-webkit-text-fill-color: #210905 !important;
}

.eclipse-boosted-sponsor-page h2,
.eclipse-boosted-sponsor-page h3,
.eclipse-boosted-sponsor-page h4 {
	margin: 0;
	font-family: Georgia, "Times New Roman", serif;
}

.eclipse-boosted-sponsor-page p {
	margin: 0;
	font: 700 13px/1.55 Verdana, Arial, sans-serif;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-actions {
	text-align: center;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-slot-grid,
.eclipse-boosted-sponsor-page .boosted-sponsor-summary-grid,
.eclipse-boosted-sponsor-page .boosted-sponsor-donation-summary,
.eclipse-boosted-sponsor-page .boosted-sponsor-history {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 12px;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-donation-summary {
	grid-template-columns: repeat(3, minmax(0, 1fr));
}

.eclipse-boosted-sponsor-page .boosted-sponsor-slot-card,
.eclipse-boosted-sponsor-page .boosted-sponsor-summary-card,
.eclipse-boosted-sponsor-page .boosted-sponsor-donation-summary > div {
	display: grid;
	gap: 6px;
	padding: 14px;
	background: rgba(255,247,221,.86);
	border: 1px solid rgba(145,98,40,.52);
}

.eclipse-boosted-sponsor-page .boosted-sponsor-slot-card.is-locked,
.eclipse-boosted-sponsor-page .boosted-sponsor-summary-card.is-locked {
	background: rgba(242,223,196,.94);
	border-color: #8c3b18;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-tabs {
	display: inline-flex;
	gap: 8px;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-tabs a {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-width: 110px;
	padding: 10px 14px;
	background: rgba(255,244,205,.58);
	border: 1px solid rgba(126,78,27,.55);
	text-decoration: none;
	font: 900 12px Verdana, Arial, sans-serif;
	text-transform: uppercase;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-tabs a.is-active {
	background: linear-gradient(180deg, #09354a, #031620);
	border-color: #d4872e;
	color: #fff4cf !important;
	-webkit-text-fill-color: #fff4cf !important;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-warning,
.eclipse-boosted-sponsor-page .boosted-sponsor-status.warning {
	display: grid;
	gap: 6px;
	padding: 12px;
	background: #fff2c9;
	border: 1px solid #a15f1c;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-results-header,
.eclipse-boosted-sponsor-page .boosted-sponsor-actions-inline {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-grid {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 12px;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-card {
	display: grid;
	grid-template-columns: 82px minmax(0, 1fr);
	gap: 12px;
	align-items: center;
	padding: 12px;
	background: rgba(255,247,221,.86);
	border: 1px solid rgba(145,98,40,.52);
}

.eclipse-boosted-sponsor-page .boosted-sponsor-card button {
	grid-column: 1 / -1;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-card-portrait,
.eclipse-boosted-sponsor-page .boosted-sponsor-target-portrait {
	display: flex;
	align-items: center;
	justify-content: center;
	min-height: 82px;
	background: radial-gradient(circle at 50% 35%, rgba(255,240,197,.85), rgba(210,168,90,.94));
	border: 1px solid rgba(133,86,33,.6);
}

.eclipse-boosted-sponsor-page .boosted-sponsor-card-portrait img,
.eclipse-boosted-sponsor-page .boosted-sponsor-target-portrait img {
	max-width: 64px;
	max-height: 64px;
	image-rendering: pixelated;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-card-body,
.eclipse-boosted-sponsor-page .boosted-sponsor-target-summary {
	display: flex;
	align-items: center;
	gap: 14px;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-card-body {
	display: grid;
	gap: 4px;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-history ul {
	display: grid;
	gap: 8px;
	margin: 10px 0 0;
	padding: 0;
	list-style: none;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-history li {
	display: flex;
	justify-content: space-between;
	gap: 12px;
	padding: 10px 12px;
	background: rgba(255,247,221,.86);
	border: 1px solid rgba(145,98,40,.52);
}

.eclipse-boosted-sponsor-page .boosted-sponsor-empty.is-search-hidden,
.eclipse-boosted-sponsor-page .boosted-sponsor-card.is-search-hidden {
	display: none !important;
}

@media (max-width: 900px) {
	.eclipse-boosted-sponsor-page .boosted-sponsor-slot-grid,
	.eclipse-boosted-sponsor-page .boosted-sponsor-summary-grid,
	.eclipse-boosted-sponsor-page .boosted-sponsor-donation-summary,
	.eclipse-boosted-sponsor-page .boosted-sponsor-history,
	.eclipse-boosted-sponsor-page .boosted-sponsor-grid {
		grid-template-columns: 1fr;
	}

	.eclipse-boosted-sponsor-page .boosted-sponsor-results-header,
	.eclipse-boosted-sponsor-page .boosted-sponsor-actions-inline,
	.eclipse-boosted-sponsor-page .boosted-sponsor-target-summary {
		flex-direction: column;
		align-items: stretch;
	}
}
</style>
<script>
var boostedSearch = document.querySelector('[data-boosted-search]');
if (boostedSearch) boostedSearch.addEventListener('input', function () {
	var visible = 0;
	var searchTerm = boostedSearch.value.trim().toLocaleLowerCase('pt-BR');
	document.querySelectorAll('[data-boosted-grid] [data-name]').forEach(function (card) {
		var matches = card.dataset.name.toLocaleLowerCase('pt-BR').indexOf(searchTerm) !== -1;
		card.classList.toggle('is-search-hidden', !matches);
		if (matches) visible++;
	});
	var count = document.querySelector('[data-boosted-visible-count]');
	var empty = document.querySelector('[data-boosted-empty]');
	if (count) count.textContent = visible;
	if (empty) empty.hidden = visible !== 0;
});
</script>
