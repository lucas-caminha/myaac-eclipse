<?php
defined('MYAAC') or die('Direct access not allowed!');

require_once __DIR__ . '/boosted-sponsor-common.php';

function eclipseBoostedSponsorApplyJson(int $status, array $payload): void
{
	http_response_code($status);
	header('Content-Type: application/json');
	echo json_encode($payload);
}

$isCli = PHP_SAPI === 'cli';
$token = $_SERVER['HTTP_X_ECLIPSE_TOKEN'] ?? '';
$expectedToken = eclipseBoostedSponsorEnv('ECLIPSE_BOOSTED_APPLY_TOKEN');

if(!$isCli) {
	if(!$expectedToken || !hash_equals($expectedToken, (string)$token)) {
		eclipseBoostedSponsorApplyJson(403, ['ok' => false, 'error' => 'forbidden']);
		return;
	}
}

$today = date('Y-m-d');
$stmt = $db->prepare(
	'SELECT * FROM eclipse_boosted_sponsorships
	  WHERE status = "paid" AND scheduled_for_date <= :today
	  ORDER BY scheduled_for_date ASC, id ASC'
);
$stmt->execute([':today' => $today]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

$applied = [];
$errors = [];

foreach($orders as $order) {
	try {
		$db->beginTransaction();

		$lock = $db->prepare('SELECT * FROM eclipse_boosted_sponsorships WHERE id = ? FOR UPDATE');
		$lock->execute([(int)$order['id']]);
		$lockedOrder = $lock->fetch(PDO::FETCH_ASSOC);
		if(!$lockedOrder || $lockedOrder['status'] !== 'paid') {
			$db->rollBack();
			continue;
		}

		$target = eclipseBoostedSponsorLoadTarget($db, $lockedOrder['target_type'], (int)$lockedOrder['target_monster_id']);
		if(!$target) {
			$db->update('eclipse_boosted_sponsorships', [
				'status' => 'apply_error',
				'updated_at' => date('Y-m-d H:i:s'),
				'notes' => 'Could not load the selected target during apply step.',
			], ['id' => (int)$lockedOrder['id']]);
			$db->commit();
			$errors[] = ['id' => (int)$lockedOrder['id'], 'error' => 'target_not_found'];
			continue;
		}

		eclipseBoostedSponsorApplyToBoostedTable($db, $lockedOrder, $target);
		$db->update('eclipse_boosted_sponsorships', [
			'status' => 'applied',
			'updated_at' => date('Y-m-d H:i:s'),
			'applied_at' => date('Y-m-d H:i:s'),
			'notes' => 'Boosted target applied to live table.',
		], ['id' => (int)$lockedOrder['id']]);

		$db->commit();
		$applied[] = [
			'id' => (int)$lockedOrder['id'],
			'type' => $lockedOrder['target_type'],
			'target' => $lockedOrder['target_name'],
		];
	}
	catch(Throwable $e) {
		if($db->inTransaction()) {
			$db->rollBack();
		}
		$errors[] = ['id' => (int)$order['id'], 'error' => $e->getMessage()];
	}
}

eclipseBoostedSponsorApplyJson(200, [
	'ok' => true,
	'applied_count' => count($applied),
	'applied' => $applied,
	'errors' => $errors,
]);
