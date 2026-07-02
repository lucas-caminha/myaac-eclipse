<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Account;

require_once __DIR__ . '/boosted-sponsor-common.php';

$title = eclipseBoostedSponsorText('boosted_sponsor.title', [], 'Sponsor Boosted');

function eclipseBoostedSponsorStep(): string
{
	$step = $_POST['step'] ?? $_GET['step'] ?? 'intro';
	return in_array($step, ['intro', 'select', 'checkout', 'confirm'], true) ? $step : 'intro';
}

function eclipseBoostedSponsorType(): string
{
	$type = $_POST['target_type'] ?? $_GET['target_type'] ?? $_GET['type'] ?? 'boss';
	return $type === 'creature' ? 'creature' : 'boss';
}

function eclipseBoostedSponsorRenderProgress(string $step): void
{
	$activeStep = $step === 'confirm' ? 'checkout' : $step;
	$steps = [
		'intro' => eclipseBoostedSponsorText('boosted_sponsor.step_intro', [], 'Explanation'),
		'select' => eclipseBoostedSponsorText('boosted_sponsor.step_select', [], 'Choice'),
		'checkout' => eclipseBoostedSponsorText('boosted_sponsor.step_confirm', [], 'Confirmation'),
	];

	echo '<div class="boosted-sponsor-progress">';
	foreach($steps as $key => $label) {
		$class = $key === $activeStep ? 'active' : '';
		echo '<span class="' . $class . '">' . $label . '</span>';
	}
	echo '</div>';
}

function eclipseBoostedSponsorRenderShellStart(string $step): void
{
	echo '<div class="eclipse-boosted-sponsor-page">';
	echo '<div class="boosted-sponsor-shell">';
	echo '<div class="boosted-sponsor-title">';
	echo '<strong>' . eclipseBoostedSponsorText('boosted_sponsor.hero_title', [], 'Sponsor the next boosted') . '</strong>';
	echo '<span>' . eclipseBoostedSponsorText('boosted_sponsor.hero_subtitle', [], 'Choose a boss for 250 Tibia Coins or a creature for 300 Tibia Coins for the next daily server rotation.') . '</span>';
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
		<h2><?= eclipseBoostedSponsorText('boosted_sponsor.restricted_access', [], 'Restricted access') ?></h2>
		<p><?= eclipseBoostedSponsorText('boosted_sponsor.login_required_1', [], 'To choose the next boosted boss or creature, you need to log in first.') ?></p>
		<p><?= eclipseBoostedSponsorText('boosted_sponsor.login_required_2', [], 'After login, the system shows available targets, the Tibia Coins cost and the current status of each slot.') ?></p>
		<div class="boosted-sponsor-actions">
			<a class="eclipse-btn" href="<?= getLink('account/manage') ?>"><?= eclipseBoostedSponsorText('boosted_sponsor.login_account', [], 'Log in to your account') ?></a>
			<a class="eclipse-btn eclipse-btn-secondary" href="<?= getLink('account/create') ?>"><?= eclipseBoostedSponsorText('boosted_sponsor.create_account', [], 'Create account') ?></a>
		</div>
	</div>
<?php
}

function eclipseBoostedSponsorRenderIntro(DateTimeImmutable $nextServerSave, ?array $bossSlot, ?array $creatureSlot, bool $logged): void
{
?>
	<div class="boosted-sponsor-panel">
		<h2><?= eclipseBoostedSponsorText('boosted_sponsor.how_it_works', [], 'How it works') ?></h2>
		<p><?= eclipseBoostedSponsorText('boosted_sponsor.intro_1', [], 'This support lets you define the boosted boss or boosted creature for the next daily rotation using Tibia Coins from your own account.') ?></p>
		<p><?= eclipseBoostedSponsorText('boosted_sponsor.intro_2', [], 'The cost is fixed: 250 Tibia Coins for boss and 300 Tibia Coins for creature. After confirmation, the target is scheduled for the next rotation and enters a 10-day cooldown after being applied.') ?></p>
		<p><?= eclipseBoostedSponsorText('boosted_sponsor.intro_3', ['date' => '<strong>' . eclipseBoostedSponsorFormatDate($nextServerSave) . '</strong>'], 'The next cutoff for this round happens at {date}.') ?></p>

		<div class="boosted-sponsor-slot-grid">
			<div class="boosted-sponsor-slot-card<?= $bossSlot ? ' is-locked' : '' ?>">
				<small><?= eclipseBoostedSponsorText('boosted_sponsor.boss_slot', [], 'Boss slot') ?></small>
				<strong><?= $bossSlot ? htmlspecialchars($bossSlot['target_name']) : eclipseBoostedSponsorText('boosted_sponsor.available', [], 'Available') ?></strong>
				<span><?= $bossSlot ? eclipseBoostedSponsorText('boosted_sponsor.boss_slot_reserved', ['coins' => number_format((int)$bossSlot['amount_coins'], 0, ',', '.')], 'This boss was secured for {coins} Tibia Coins.') : eclipseBoostedSponsorText('boosted_sponsor.free_for_sponsor', [], 'Free for sponsorship.') ?></span>
			</div>
			<div class="boosted-sponsor-slot-card<?= $creatureSlot ? ' is-locked' : '' ?>">
				<small><?= eclipseBoostedSponsorText('boosted_sponsor.creature_slot', [], 'Creature slot') ?></small>
				<strong><?= $creatureSlot ? htmlspecialchars($creatureSlot['target_name']) : eclipseBoostedSponsorText('boosted_sponsor.available', [], 'Available') ?></strong>
				<span><?= $creatureSlot ? eclipseBoostedSponsorText('boosted_sponsor.creature_slot_reserved', ['coins' => number_format((int)$creatureSlot['amount_coins'], 0, ',', '.')], 'This creature was secured for {coins} Tibia Coins.') : eclipseBoostedSponsorText('boosted_sponsor.free_for_sponsor', [], 'Free for sponsorship.') ?></span>
			</div>
		</div>

		<div class="boosted-sponsor-actions">
			<?php if($logged): ?>
				<a class="eclipse-btn" href="<?= getLink('boosted-sponsor') ?>?step=select&type=boss"><?= eclipseBoostedSponsorText('boosted_sponsor.choose_boosted', [], 'Choose boosted') ?></a>
			<?php else: ?>
				<a class="eclipse-btn" href="<?= getLink('account/manage') ?>"><?= eclipseBoostedSponsorText('boosted_sponsor.login_to_continue', [], 'Log in to continue') ?></a>
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
			<a class="<?= $type === 'boss' ? 'is-active' : '' ?>" href="<?= getLink('boosted-sponsor') ?>?step=select&type=boss"><?= eclipseBoostedSponsorText('boosted_sponsor.type_boss', [], 'Boss') ?></a>
			<a class="<?= $type === 'creature' ? 'is-active' : '' ?>" href="<?= getLink('boosted-sponsor') ?>?step=select&type=creature"><?= eclipseBoostedSponsorText('boosted_sponsor.type_creature', [], 'Creature') ?></a>
		</div>

		<div class="boosted-sponsor-summary-grid">
			<div class="boosted-sponsor-summary-card">
				<small><?= eclipseBoostedSponsorText('boosted_sponsor.next_rotation', [], 'Next rotation') ?></small>
				<strong><?= eclipseBoostedSponsorFormatDate($nextServerSave) ?></strong>
				<span><?= eclipseBoostedSponsorText('boosted_sponsor.one_slot_per_round', ['type' => strtolower($typeLabel)], 'There is only one {type} slot per round.') ?></span>
			</div>
			<div class="boosted-sponsor-summary-card">
				<small><?= eclipseBoostedSponsorText('boosted_sponsor.current_balance', [], 'Current balance') ?></small>
				<strong><?= number_format($balance, 0, ',', '.') ?> Tibia Coins</strong>
				<span><?= eclipseBoostedSponsorText('boosted_sponsor.sponsorship_cost', ['coins' => number_format($priceCoins, 0, ',', '.')], 'This sponsorship costs {coins} Tibia Coins.') ?></span>
			</div>
			<div class="boosted-sponsor-summary-card<?= $slot ? ' is-locked' : '' ?>">
				<small><?= eclipseBoostedSponsorText('boosted_sponsor.current_slot', ['type' => strtolower($typeLabel)], 'Current {type} slot') ?></small>
				<strong><?= $slot ? htmlspecialchars($slot['target_name']) : eclipseBoostedSponsorText('boosted_sponsor.available', [], 'Available') ?></strong>
				<span><?= $slot ? eclipseBoostedSponsorText('boosted_sponsor.slot_reserved', ['coins' => number_format((int)$slot['amount_coins'], 0, ',', '.')], 'This slot was secured for {coins} Tibia Coins.') : eclipseBoostedSponsorText('boosted_sponsor.still_time', [], 'There is still time to choose.') ?></span>
			</div>
		</div>

		<?php if($slot): ?>
			<div class="boosted-sponsor-warning">
				<strong><?= eclipseBoostedSponsorText('boosted_sponsor.slot_unavailable', [], 'Slot temporarily unavailable') ?></strong>
				<span><?= eclipseBoostedSponsorText('boosted_sponsor.slot_unavailable_text', ['coins' => '<strong>' . number_format((int)$slot['amount_coins'], 0, ',', '.') . ' Tibia Coins</strong>', 'name' => '<strong>' . htmlspecialchars($slot['target_name']) . '</strong>', 'type' => $otherType === 'boss' ? eclipseBoostedSponsorText('boosted_sponsor.type_boss', [], 'Boss') : eclipseBoostedSponsorText('boosted_sponsor.type_creature', [], 'Creature')], 'This slot received a donation of {coins} to boost {name}. You can still check the {type} slot.') ?></span>
			</div>
		<?php else: ?>
			<div class="boosted-sponsor-results-header">
				<div>
					<span><?= eclipseBoostedSponsorText('boosted_sponsor.category', [], 'Category') ?></span>
					<h3><?= $typeLabel ?> boosted</h3>
					<p><strong data-boosted-visible-count><?= count($targets) ?></strong> <?= eclipseBoostedSponsorText('boosted_sponsor.eligible_targets', [], 'eligible targets') ?></p>
				</div>
				<div class="boosted-sponsor-actions-inline">
					<input class="eclipse-plugin-search" type="search" placeholder="<?= htmlspecialchars(eclipseBoostedSponsorText('boosted_sponsor.search_name', [], 'Search name')) ?>" data-boosted-search>
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
							<span><?= eclipseBoostedSponsorText('boosted_sponsor.health_exp', ['health' => number_format((int)$target['health'], 0, ',', '.'), 'exp' => number_format((int)$target['exp'], 0, ',', '.')], 'Health {health} &bull; EXP {exp}') ?></span>
						</div>
						<button class="eclipse-btn" type="submit"><?= eclipseBoostedSponsorText('boosted_sponsor.sponsor_for', ['coins' => number_format($priceCoins, 0, ',', '.')], 'Sponsor for {coins} Tibia Coins') ?></button>
					</form>
				<?php endforeach; ?>
			</div>
			<div class="eclipse-plugin-empty boosted-sponsor-empty" data-boosted-empty hidden><?= eclipseBoostedSponsorText('boosted_sponsor.no_target_search', [], 'No target found with that name.') ?></div>

			<?php if(empty($targets)): ?>
				<div class="eclipse-plugin-empty"><?= eclipseBoostedSponsorText('boosted_sponsor.no_targets_available', [], 'There are no available targets for this slot right now.') ?></div>
			<?php endif; ?>
		<?php endif; ?>

		<div class="boosted-sponsor-history">
			<div>
				<h4><?= eclipseBoostedSponsorText('boosted_sponsor.recent_bosses', [], 'Latest applied bosses') ?></h4>
				<ul>
					<?php foreach($recentBosses as $entry): ?>
						<li><strong><?= htmlspecialchars($entry['target_name']) ?></strong><span><?= date('d/m/Y', strtotime((string)$entry['scheduled_for_date'])) ?></span></li>
					<?php endforeach; ?>
					<?php if(empty($recentBosses)): ?><li><span><?= eclipseBoostedSponsorText('boosted_sponsor.no_boss_history', [], 'No sponsored boss yet.') ?></span></li><?php endif; ?>
				</ul>
			</div>
			<div>
				<h4><?= eclipseBoostedSponsorText('boosted_sponsor.recent_creatures', [], 'Latest applied creatures') ?></h4>
				<ul>
					<?php foreach($recentCreatures as $entry): ?>
						<li><strong><?= htmlspecialchars($entry['target_name']) ?></strong><span><?= date('d/m/Y', strtotime((string)$entry['scheduled_for_date'])) ?></span></li>
					<?php endforeach; ?>
					<?php if(empty($recentCreatures)): ?><li><span><?= eclipseBoostedSponsorText('boosted_sponsor.no_creature_history', [], 'No sponsored creature yet.') ?></span></li><?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
<?php
}

function eclipseBoostedSponsorRenderCheckout(array $target, DateTimeImmutable $nextServerSave, ?int $orderId, bool $orderSaved, int $priceCoins, int $balanceAfter, string $sponsorName): void
{
?>
	<div class="boosted-sponsor-panel boosted-sponsor-checkout">
		<h2><?= eclipseBoostedSponsorText('boosted_sponsor.confirmed_title', [], 'Sponsorship confirmed') ?></h2>
		<div class="boosted-sponsor-target-summary">
			<div class="boosted-sponsor-target-portrait"><img src="<?= htmlspecialchars($target['img_link']) ?>" alt="<?= htmlspecialchars($target['name']) ?>"></div>
			<div>
				<small><?= eclipseBoostedSponsorTypeLabel((string)($_POST['target_type'] ?? 'boss')) ?></small>
				<strong><?= htmlspecialchars($target['name']) ?></strong>
				<span><?= eclipseBoostedSponsorText('boosted_sponsor.enters_server_save', ['category' => htmlspecialchars($target['category']), 'date' => $nextServerSave->format('d/m/Y')], '{category} &bull; enters on the {date} server save') ?></span>
			</div>
		</div>

		<div class="boosted-sponsor-donation-summary">
			<div><span><?= eclipseBoostedSponsorText('boosted_sponsor.cost', [], 'Cost') ?></span><strong><?= number_format($priceCoins, 0, ',', '.') ?> Tibia Coins</strong></div>
			<div><span><?= eclipseBoostedSponsorText('boosted_sponsor.cooldown_after_entry', [], 'Cooldown after entry') ?></span><strong><?= eclipseBoostedSponsorText('boosted_sponsor.ten_days', [], '10 days') ?></strong></div>
			<div><span><?= eclipseBoostedSponsorText('boosted_sponsor.remaining_balance', [], 'Remaining balance') ?></span><strong><?= number_format($balanceAfter, 0, ',', '.') ?> Tibia Coins</strong></div>
		</div>

		<?php if($orderSaved): ?>
			<p class="boosted-sponsor-status"><?= eclipseBoostedSponsorText('boosted_sponsor.status_confirmed_save', ['id' => (int)$orderId, 'name' => htmlspecialchars($target['name'])], 'Order #{id} confirmed successfully. Target {name} was secured for the next server save and the slot was locked for other players.') ?></p>
		<?php else: ?>
			<p class="boosted-sponsor-status warning"><?= eclipseBoostedSponsorText('boosted_sponsor.status_not_saved', [], 'Could not register the sponsorship. Check if the SQL migration has already been applied.') ?></p>
		<?php endif; ?>

		<div class="boosted-sponsor-actions">
			<a class="eclipse-btn" href="<?= getLink('boosted-sponsor') ?>?step=select&type=<?= htmlspecialchars((string)($_POST['target_type'] ?? 'boss')) ?>"><?= eclipseBoostedSponsorText('boosted_sponsor.choose_another_target', [], 'Choose another target') ?></a>
		</div>
	</div>
<?php
}

function eclipseBoostedSponsorRenderCheckoutPreview(array $target, DateTimeImmutable $nextServerSave, int $priceCoins, int $balance): void
{
?>
	<div class="boosted-sponsor-panel boosted-sponsor-checkout">
		<h2><?= eclipseBoostedSponsorText('boosted_sponsor.confirm_title', [], 'Confirm sponsorship') ?></h2>
		<div class="boosted-sponsor-target-summary">
			<div class="boosted-sponsor-target-portrait"><img src="<?= htmlspecialchars($target['img_link']) ?>" alt="<?= htmlspecialchars($target['name']) ?>"></div>
			<div>
				<small><?= eclipseBoostedSponsorTypeLabel((string)($_POST['target_type'] ?? 'boss')) ?></small>
				<strong><?= htmlspecialchars($target['name']) ?></strong>
				<span><?= eclipseBoostedSponsorText('boosted_sponsor.enters_rotation', ['category' => htmlspecialchars($target['category']), 'date' => $nextServerSave->format('d/m/Y')], '{category} &bull; enters the rotation on {date}') ?></span>
			</div>
		</div>
		<div class="boosted-sponsor-donation-summary">
			<div><span><?= eclipseBoostedSponsorText('boosted_sponsor.cost', [], 'Cost') ?></span><strong><?= number_format($priceCoins, 0, ',', '.') ?> Tibia Coins</strong></div>
			<div><span><?= eclipseBoostedSponsorText('boosted_sponsor.cooldown_after_entry', [], 'Cooldown after entry') ?></span><strong><?= eclipseBoostedSponsorText('boosted_sponsor.ten_days', [], '10 days') ?></strong></div>
			<div><span><?= eclipseBoostedSponsorText('boosted_sponsor.current_balance', [], 'Current balance') ?></span><strong><?= number_format($balance, 0, ',', '.') ?> Tibia Coins</strong></div>
		</div>
		<div class="boosted-sponsor-actions">
			<p class="boosted-sponsor-confirm-copy"><?= eclipseBoostedSponsorText('boosted_sponsor.confirm_copy', [], 'When confirmed, Tibia Coins will be charged immediately and this target will be scheduled for the next daily server rotation.') ?></p>
			<form method="post" action="<?= getLink('boosted-sponsor') ?>">
				<?= csrf(true) ?>
				<input type="hidden" name="step" value="confirm">
				<input type="hidden" name="target_type" value="<?= htmlspecialchars((string)($_POST['target_type'] ?? 'boss')) ?>">
				<input type="hidden" name="target_id" value="<?= (int)($_POST['target_id'] ?? 0) ?>">
				<button class="eclipse-btn" type="submit"><?= eclipseBoostedSponsorText('boosted_sponsor.confirm_sponsorship', [], 'Confirm sponsorship') ?></button>
				<a class="eclipse-btn eclipse-btn-secondary" href="<?= getLink('boosted-sponsor') ?>?step=select&type=<?= htmlspecialchars((string)($_POST['target_type'] ?? 'boss')) ?>"><?= eclipseBoostedSponsorText('common.back', [], 'Back') ?></a>
			</form>
		</div>
	</div>
<?php
}

function eclipseBoostedSponsorRenderCheckoutSuccess(array $target, DateTimeImmutable $nextServerSave, ?int $orderId, int $priceCoins, int $balanceAfter, string $sponsorName): void
{
?>
	<div class="boosted-sponsor-panel boosted-sponsor-checkout">
		<h2><?= eclipseBoostedSponsorText('boosted_sponsor.confirmed_title', [], 'Sponsorship confirmed') ?></h2>
		<div class="boosted-sponsor-target-summary">
			<div class="boosted-sponsor-target-portrait"><img src="<?= htmlspecialchars($target['img_link']) ?>" alt="<?= htmlspecialchars($target['name']) ?>"></div>
			<div>
				<small><?= eclipseBoostedSponsorTypeLabel((string)($_POST['target_type'] ?? 'boss')) ?></small>
				<strong><?= htmlspecialchars($target['name']) ?></strong>
				<span><?= eclipseBoostedSponsorText('boosted_sponsor.enters_rotation', ['category' => htmlspecialchars($target['category']), 'date' => $nextServerSave->format('d/m/Y')], '{category} &bull; enters the rotation on {date}') ?></span>
			</div>
		</div>
		<div class="boosted-sponsor-donation-summary">
			<div><span><?= eclipseBoostedSponsorText('boosted_sponsor.cost', [], 'Cost') ?></span><strong><?= number_format($priceCoins, 0, ',', '.') ?> Tibia Coins</strong></div>
			<div><span><?= eclipseBoostedSponsorText('boosted_sponsor.cooldown_after_entry', [], 'Cooldown after entry') ?></span><strong><?= eclipseBoostedSponsorText('boosted_sponsor.ten_days', [], '10 days') ?></strong></div>
			<div><span><?= eclipseBoostedSponsorText('boosted_sponsor.remaining_balance', [], 'Remaining balance') ?></span><strong><?= number_format($balanceAfter, 0, ',', '.') ?> Tibia Coins</strong></div>
		</div>
		<p class="boosted-sponsor-status"><?= eclipseBoostedSponsorText('boosted_sponsor.status_confirmed_rotation', ['id' => (int)$orderId, 'name' => htmlspecialchars($target['name'])], 'Order #{id} confirmed successfully. Target {name} was scheduled for the next daily rotation and the slot was locked for other players.') ?></p>
		<div class="boosted-sponsor-actions">
			<a class="eclipse-btn" href="<?= getLink('boosted-sponsor') ?>?step=select&type=<?= htmlspecialchars((string)($_POST['target_type'] ?? 'boss')) ?>"><?= eclipseBoostedSponsorText('boosted_sponsor.choose_another_target', [], 'Choose another target') ?></a>
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
	$accountBalance = (int)($account->coins ?? 0);

	if(!$profileComplete) {
		header('Location: ' . getLink('account/change-info'));
		exit;
	}
}

eclipseBoostedSponsorRenderShellStart($step);

if(!$logged) {
	eclipseBoostedSponsorRenderLoginRequired();
	eclipseBoostedSponsorRenderShellEnd();
	eclipseBoostedSponsorRenderAssets();
	return;
}

if(!$db->hasTable('eclipse_boosted_sponsorships') || !$db->hasTable('scheduled_boosted')) {
	echo '<div class="boosted-sponsor-panel boosted-sponsor-error"><h2>' . eclipseBoostedSponsorText('boosted_sponsor.migration_pending', [], 'Pending migration') . '</h2><p>' . eclipseBoostedSponsorText('boosted_sponsor.migration_pending_text', [], 'Boosted sponsorship tables are not complete yet. Apply <code>sql/012-add-boosted-sponsorships.sql</code> and <code>sql/016-add-scheduled-boosted.sql</code> before enabling this flow.') . '</p></div>';
	eclipseBoostedSponsorRenderShellEnd();
	eclipseBoostedSponsorRenderAssets();
	return;
}

if($step === 'checkout' || $step === 'confirm') {
	$targetId = (int)($_POST['target_id'] ?? 0);
	$target = eclipseBoostedSponsorLoadTarget($db, $type, $targetId);
	$currentSlot = $type === 'boss' ? $bossSlot : $creatureSlot;
	$priceCoins = eclipseBoostedSponsorPriceCoins($type);

	if(!$target) {
		error(eclipseBoostedSponsorText('boosted_sponsor.error_target_not_found', [], 'The selected target was not found.'));
		$step = 'select';
	}
	else if($currentSlot) {
		error(eclipseBoostedSponsorText('boosted_sponsor.error_slot_reserved', [], 'This slot has already been reserved for the next rotation.'));
		$step = 'select';
	}
	else if(eclipseBoostedSponsorCooldownConflict($db, $type, $target['name'], $scheduledForDate)) {
		error(eclipseBoostedSponsorText('boosted_sponsor.error_target_cooldown', [], 'This target is still on cooldown and cannot return now.'));
		$step = 'select';
	}
	else if($step === 'checkout') {
		eclipseBoostedSponsorRenderCheckoutPreview($target, $nextServerSave, $priceCoins, $accountBalance);
		eclipseBoostedSponsorRenderShellEnd();
		eclipseBoostedSponsorRenderAssets();
		return;
	}
	else {
		$orderId = null;
		$balanceAfter = $accountBalance;

		try {
			$db->beginTransaction();

			$slotConflict = eclipseBoostedSponsorActiveSlot($db, $type, $scheduledForDate);
			if($slotConflict) {
				throw new RuntimeException(eclipseBoostedSponsorText('boosted_sponsor.error_slot_just_reserved', [], 'This slot was just reserved by another support. Choose another type or come back in a few minutes.'));
			}

			$scheduledLock = $db->prepare(
				'SELECT id, boostname FROM scheduled_boosted
				  WHERE type = ? AND scheduled_for = ? AND status = "pending"
				  ORDER BY id ASC LIMIT 1 FOR UPDATE'
			);
			$scheduledLock->execute([$type, $scheduledForDate]);
			$scheduledConflict = $scheduledLock->fetch(PDO::FETCH_ASSOC);
			if($scheduledConflict) {
				throw new RuntimeException(eclipseBoostedSponsorText('boosted_sponsor.error_slot_just_scheduled', ['name' => $scheduledConflict['boostname']], 'This slot was just scheduled for {name}.'));
			}

			$cooldownConflict = eclipseBoostedSponsorCooldownConflict($db, $type, $target['name'], $scheduledForDate);
			if($cooldownConflict) {
				throw new RuntimeException(eclipseBoostedSponsorText('boosted_sponsor.error_cooldown_during_navigation', [], 'This target entered cooldown while you were browsing the page.'));
			}

			$coinsStmt = $db->prepare('SELECT coins FROM accounts WHERE id = ? FOR UPDATE');
			$coinsStmt->execute([(int)$account->id]);
			$accountCoins = $coinsStmt->fetch(PDO::FETCH_ASSOC);
			$currentCoins = (int)($accountCoins['coins'] ?? 0);
			if($currentCoins < $priceCoins) {
				throw new RuntimeException(eclipseBoostedSponsorText('boosted_sponsor.error_not_enough_balance', ['coins' => number_format($priceCoins, 0, ',', '.')], 'Not enough balance. You need {coins} Tibia Coins for this sponsorship.'));
			}

			$balanceAfter = $currentCoins - $priceCoins;
			$cooldownUntil = $nextServerSave->modify('+10 days')->format('Y-m-d');
			$targetRaceId = eclipseBoostedSponsorResolveRaceId($target);

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
				'payer_cpf' => null,
				'confirmed_at' => date('Y-m-d H:i:s'),
				'notes' => 'Boosted sponsorship purchased with Tibia Coins.',
			]);

			if(!$orderSaved) {
				throw new RuntimeException(eclipseBoostedSponsorText('boosted_sponsor.error_create_order', [], 'Could not create the sponsorship order.'));
			}

			$orderId = (int)$db->lastInsertId();
			$scheduledSaved = $db->insert('scheduled_boosted', [
				'type' => $type,
				'boostname' => $target['name'],
				'raceid' => $targetRaceId,
				'player_id' => null,
				'account_id' => (int)$account->id,
				'status' => 'pending',
				'scheduled_for' => $scheduledForDate,
				'source_order_id' => $orderId,
			]);

			if(!$scheduledSaved) {
				throw new RuntimeException(eclipseBoostedSponsorText('boosted_sponsor.error_schedule_boosted', [], 'Could not schedule the boosted for the next rotation.'));
			}

			$db->prepare('UPDATE accounts SET coins = coins - ? WHERE id = ?')->execute([$priceCoins, (int)$account->id]);
			if($db->hasTable('coins_transactions')) {
				$db->insert('coins_transactions', [
					'account_id' => (int)$account->id,
					'type' => 2,
					'coin_type' => 1,
					'amount' => $priceCoins,
					'description' => eclipseBoostedSponsorText('boosted_sponsor.transaction_description', ['type' => eclipseBoostedSponsorTypeLabel($type), 'name' => $target['name']], 'Boosted sponsorship {type}: {name}'),
				]);
			}

			$db->commit();

			eclipseBoostedSponsorRenderCheckoutSuccess($target, $nextServerSave, $orderId, $priceCoins, $balanceAfter, (string)$account_logged->getName());
			eclipseBoostedSponsorRenderShellEnd();
			eclipseBoostedSponsorRenderAssets();
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
eclipseBoostedSponsorRenderAssets();
?>
<?php
function eclipseBoostedSponsorRenderAssets(): void
{
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

.eclipse-boosted-sponsor-page .boosted-sponsor-actions form {
	display: inline-flex;
	flex-wrap: wrap;
	align-items: center;
	justify-content: center;
	gap: 10px;
	margin: 0;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-confirm-copy {
	width: 100%;
	margin-bottom: 2px;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-actions .eclipse-btn,
.eclipse-boosted-sponsor-page .boosted-sponsor-card .eclipse-btn,
.eclipse-boosted-sponsor-page .boosted-sponsor-actions-inline .eclipse-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-height: 38px;
	padding: 0 16px;
	border: 1px solid #ffe5a2;
	border-radius: 4px;
	background: linear-gradient(180deg, #a8250e 0%, #6f1105 100%);
	color: #fff4db !important;
	-webkit-text-fill-color: #fff4db !important;
	font: 900 12px Verdana, Arial, sans-serif;
	text-decoration: none;
	text-transform: uppercase;
	letter-spacing: .02em;
	text-shadow: 0 1px 1px #240401 !important;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.20), 0 2px 7px rgba(0,0,0,.28);
}

.eclipse-boosted-sponsor-page .boosted-sponsor-actions .eclipse-btn:hover,
.eclipse-boosted-sponsor-page .boosted-sponsor-card .eclipse-btn:hover,
.eclipse-boosted-sponsor-page .boosted-sponsor-actions-inline .eclipse-btn:hover {
	background: linear-gradient(180deg, #c03312 0%, #7f1606 100%);
	color: #fff !important;
	-webkit-text-fill-color: #fff !important;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-actions .eclipse-btn-secondary {
	background: linear-gradient(180deg, #2a5a74 0%, #113245 100%);
	border-color: #a9cee0;
}

.eclipse-boosted-sponsor-page .boosted-sponsor-actions .eclipse-btn-secondary:hover {
	background: linear-gradient(180deg, #32708f 0%, #14405a 100%);
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
<?php
}
