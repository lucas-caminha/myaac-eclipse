<?php
/**
 * Account management override for Eclipse OT.
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = function_exists('t') ? t('layout.my_account') : 'My Account';
require SYSTEM . 'pages/account/login.php';
require SYSTEM . 'pages/account/base.php';

if(!$logged) {
	return;
}

if(isset($_REQUEST['redirect']))
{
	$redirect = urldecode((string)$_REQUEST['redirect']);
	$redirectParts = parse_url($redirect);
	$baseParts = parse_url(BASE_URL);
	$baseScheme = strtolower((string)($baseParts['scheme'] ?? ''));
	$baseHost = strtolower((string)($baseParts['host'] ?? ''));
	$basePort = isset($baseParts['port']) ? (int)$baseParts['port'] : null;
	$basePath = '/' . ltrim((string)($baseParts['path'] ?? '/'), '/');
	$basePath = rtrim($basePath, '/');
	$basePath = $basePath === '' ? '/' : $basePath;
	$isInternalRedirect = false;

	if($redirectParts !== false) {
		if(isset($redirectParts['scheme']) || isset($redirectParts['host'])) {
			$redirectScheme = strtolower((string)($redirectParts['scheme'] ?? ''));
			$redirectHost = strtolower((string)($redirectParts['host'] ?? ''));
			$redirectPort = isset($redirectParts['port']) ? (int)$redirectParts['port'] : null;
			$redirectPath = '/' . ltrim((string)($redirectParts['path'] ?? '/'), '/');

			$isInternalRedirect = $redirectScheme === $baseScheme
				&& $redirectHost === $baseHost
				&& $redirectPort === $basePort
				&& str_starts_with($redirectPath, $basePath);
		}
		else {
			$relativePath = (string)($redirectParts['path'] ?? '');
			$isInternalRedirect = $relativePath !== '' && !str_starts_with($relativePath, '//');
			if($isInternalRedirect) {
				$redirect = BASE_URL . ltrim($redirect, '/');
			}
		}
	}

	if(!$isInternalRedirect) {
		error('Fatal error: Cannot redirect outside the website.');
		return;
	}

	$twig->display('account.redirect.html.twig', [
		'redirect' => $redirect
	]);
	return;
}

csrfProtect();

function eclipseManageYesNo(bool $enabled): string
{
	if (function_exists('t')) {
		return $enabled ? t('common.yes') : t('common.no');
	}

	return $enabled ? 'Yes' : 'No';
}

function eclipseManagePercent(float $value): string
{
	return rtrim(rtrim(number_format($value, 2, ',', '.'), '0'), ',');
}

function eclipseManageDisplayValue($value, string $fallback = 'Not informed'): string
{
	$value = trim((string)$value);
	return $value !== '' ? $value : $fallback;
}

function eclipseManageMaskCpf($cpf): string
{
	$digits = preg_replace('/\D+/', '', (string)$cpf);
	if(strlen($digits) !== 11) {
		return function_exists('t') ? t('account.not_informed') : 'Not informed';
	}

	return '***.***.***-' . substr($digits, -2);
}

$groups = new OTS_Groups_List();

/**
 * @var OTS_Account $account_logged
 */
$premDays = $account_logged->getPremDays();

$freePremium = isset($config['lua']['freePremium']) && getBoolean($config['lua']['freePremium']) || $premDays == OTS_Account::GRATIS_PREMIUM_DAYS;
$dayOrDays = function_exists('t') ? ($premDays == 1 ? t('account.day') : t('account.days')) : ($premDays == 1 ? 'day' : 'days');

$vipSystemEnabled = isset($config['lua']['vipSystemEnabled']) && getBoolean($config['lua']['vipSystemEnabled']);
$premiumLabel = $vipSystemEnabled ? 'VIP' : (function_exists('t') ? t('account.premium_account') : 'Premium Account');

if ($freePremium && !$vipSystemEnabled) {
	$account_status = '<b><span style="color: green">' . (function_exists('t') ? t('account.free_premium_account') : 'Free Premium Account') . '</span></b>';
} else if(!$account_logged->isPremium()) {
	$account_status = '<b><span style="color: red">' . (function_exists('t') ? t('account.free_account_alt') : 'Free account') . '</span></b>';
} else {
	$account_status = '<b><span style="color: green">' . (function_exists('t') ? t('account.premium_remaining', ['label' => $premiumLabel, 'days' => $premDays, 'day_label' => $dayOrDays]) : $premiumLabel . ', ' . $premDays . ' ' . $dayOrDays . ' remaining') . '</span></b>';
}

$recovery_key = $account_logged->getCustomField('key');
if(empty($recovery_key))
	$account_registered = '<b><span style="color: red">' . (function_exists('t') ? t('common.no') : 'No') . '</span></b>';
else
{
	if(setting('core.account_generate_new_reckey') && setting('core.mail_enabled'))
		$account_registered = '<b><span style="color: green">' . (function_exists('t') ? t('common.yes') : 'Yes') . ' ( <a href="' . getLink('account/register-new') . '"> ' . (function_exists('t') ? t('account.buy_new_recovery_key') : 'Buy new Recovery Key') . ' </a> )</span></b>';
	else
		$account_registered = '<b><span style="color: green">' . (function_exists('t') ? t('common.yes') : 'Yes') . '</span></b>';
}

$account_created = $account_logged->getCreated();
$account_email = $account_logged->getEMail();
$email_new_time = $account_logged->getCustomField("email_new_time");
if($email_new_time > 1)
	$email_new = $account_logged->getCustomField("email_new");
$account_rlname = $account_logged->getRLName();
$account_location = $account_logged->getLocation();
$account_birth_date = $account_logged->getCustomField('birth_date');
$account_cpf = $account_logged->getCustomField('cpf');
$account_lastlogin = (int)$account_logged->getCustomField('web_lastlogin');
if($account_logged->isBanned())
	if($account_logged->getBanTime() > 0)
		$welcome_message = '<span style="color: red">' . (function_exists('t') ? t('account.banned_until', ['date' => date("j F Y, G:i:s", $account_logged->getBanTime())]) : 'Your account is banned until ' . date("j F Y, G:i:s", $account_logged->getBanTime()) . '.') . '</span>';
	else
		$welcome_message = '<span style="color: red">' . (function_exists('t') ? t('account.banned_permanently') : 'Your account is permanently banned.') . '</span>';
else
	$welcome_message = function_exists('t') ? t('account.welcome_message') : 'Welcome to your account!';

$email_change = '';
$email_request = false;
if($email_new_time > 1)
{
	if($email_new_time < time())
		$email_change = '<br>(' . (function_exists('t') ? t('account.email_change_ready', ['email' => '<b>' . $email_new . '</b>']) : 'You can accept ' . '<b>' . $email_new . '</b>' . ' as the new email.') . ')';
	else
	{
		$email_change = ' <br>' . (function_exists('t') ? t('account.email_change_pending', ['date' => date("j F Y", $email_new_time)]) : 'You will be able to accept the new email after ' . date("j F Y", $email_new_time) . '.');
		$email_request = true;
	}
}

$duo_invites = [];
if($db->hasTable('eclipse_duo_donation_orders')) {
	$duoInviteStmt = $db->prepare(
		'SELECT o.id, o.amount_brl_cents, o.partner_coins, o.outfit_name, o.expires_at, o.partner_token,
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
	$duoInviteStmt->execute([(int)$account_logged->getId(), 'pending_partner']);
	$duo_invites = $duoInviteStmt->fetchAll(PDO::FETCH_ASSOC);
}

$actions = $account_logged->getActionsLog(1000);

/** @var OTS_Players_List $account_players */
$account_players = $account_logged->getPlayersList();
$account_players->orderBy('id');

$players_count = 0;
$highest_player = null;
$online_players_count = 0;
foreach($account_players as $player) {
	$players_count++;
	if(method_exists($player, 'isOnline') && $player->isOnline()) {
		$online_players_count++;
	}
	if($highest_player === null || $player->getLevel() > $highest_player->getLevel()) {
		$highest_player = $player;
	}
}

$loyaltyEnabled = getBoolean($config['lua']['loyaltyEnabled'] ?? true);
$loyaltyCreationDay = (int)($config['lua']['loyaltyPointsPerCreationDay'] ?? 1);
$loyaltyPremiumSpent = (int)($config['lua']['loyaltyPointsPerPremiumDaySpent'] ?? 4);
$loyaltyPremiumPurchased = (int)($config['lua']['loyaltyPointsPerPremiumDayPurchased'] ?? 4);
$loyaltyMultiplier = (float)($config['lua']['loyaltyBonusPercentageMultiplier'] ?? 1.0);
$vipBonusExp = (int)($config['lua']['vipBonusExp'] ?? 0);
$vipBonusLoot = (int)($config['lua']['vipBonusLoot'] ?? 0);
$vipBonusSkill = (int)($config['lua']['vipBonusSkill'] ?? 0);
$vipAutoLootOnly = getBoolean($config['lua']['vipAutoLootVipOnly'] ?? false);
$vipStayOnline = getBoolean($config['lua']['vipStayOnline'] ?? false);
$vipKeepHouse = getBoolean($config['lua']['vipKeepHouse'] ?? false);
$vipFamiliarReduction = (int)($config['lua']['vipFamiliarTimeCooldownReduction'] ?? 0);
$loyaltyPoints = null;

if($db->hasTableAndColumns('accounts', ['creation', 'premdays', 'premdays_purchased'])) {
	$loyaltyExpression = '(GREATEST(FLOOR((UNIX_TIMESTAMP() - `creation`) / 86400), 0) * ' . $loyaltyCreationDay .
		' + GREATEST(`premdays_purchased` - `premdays`, 0) * ' . $loyaltyPremiumSpent .
		' + `premdays_purchased` * ' . $loyaltyPremiumPurchased . ')';
	$loyaltyRow = $db->query(
		'SELECT ' . $loyaltyExpression . ' AS loyalty_points FROM `accounts` WHERE `id` = ' . (int)$account_logged->getId() . ' LIMIT 1'
	)->fetch();
	if($loyaltyRow !== false) {
		$loyaltyPoints = (int)$loyaltyRow['loyalty_points'];
	}
}

$vip_benefits = [
	[
		'icon' => 'PremiumIcon-Stamina.png',
		'label' => function_exists('t') ? t('account.experience') : 'Experience',
		'value' => '+' . $vipBonusExp . '%',
		'description' => function_exists('t') ? t('account.vip_exp_description') : 'Bonus applied automatically to VIP accounts.'
	],
	[
		'icon' => 'PremiumIcon-TrackLoot.png',
		'label' => 'Loot',
		'value' => '+' . $vipBonusLoot . '%',
		'description' => function_exists('t') ? t('account.vip_loot_description') : 'Improves creature rewards according to the current configuration.'
	],
	[
		'icon' => 'PremiumIcon-Trainingstatues.png',
		'label' => 'Skills',
		'value' => '+' . $vipBonusSkill . '%',
		'description' => function_exists('t') ? t('account.vip_skills_description') : 'Speeds up skill and magic level training while active.'
	],
	[
		'icon' => 'PremiumIcon-QuickLoot.png',
		'label' => function_exists('t') ? t('account.vip_autoloot') : 'VIP Auto Loot',
		'value' => eclipseManageYesNo($vipAutoLootOnly),
		'description' => function_exists('t') ? t('account.vip_autoloot_description') : 'Shows whether Auto Loot is reserved for VIP accounts.'
	],
	[
		'icon' => 'PremiumIcon-Login.png',
		'label' => function_exists('t') ? t('account.protected_idle') : 'Protected idle',
		'value' => eclipseManageYesNo($vipStayOnline),
		'description' => function_exists('t') ? t('account.vip_idle_description') : 'Allows higher tolerance while standing idle online.'
	],
	[
		'icon' => 'PremiumIcon-House.png',
		'label' => function_exists('t') ? t('account.keep_house') : 'Keep house',
		'value' => eclipseManageYesNo($vipKeepHouse),
		'description' => function_exists('t') ? t('account.vip_house_description') : 'Protects the house while the account keeps active VIP.'
	],
	[
		'icon' => 'PremiumIcon-Summons.png',
		'label' => function_exists('t') ? t('account.familiar') : 'Familiar',
		'value' => '-' . $vipFamiliarReduction . ' min',
		'description' => function_exists('t') ? t('account.vip_familiar_description') : 'Familiar cooldown reduction configured on the server.'
	],
	[
		'icon' => 'PremiumIcon-Loyalty.png',
		'label' => 'Loyalty',
		'value' => $loyaltyEnabled ? ($loyaltyPoints === null ? (function_exists('t') ? t('common.active') : 'Active') : number_format($loyaltyPoints, 0, ',', '.') . ' pts') : (function_exists('t') ? t('common.disabled') : 'Disabled'),
		'description' => $loyaltyEnabled ? (function_exists('t') ? t('account.loyalty_earning_hint', ['points' => $loyaltyCreationDay]) : 'Earns ' . $loyaltyCreationDay . ' point(s)/account day and bonuses for premium days.') : (function_exists('t') ? t('account.loyalty_disabled_hint') : 'Loyalty system disabled on the server.')
	],
	[
		'icon' => 'PremiumIcon-Analytics.png',
		'label' => function_exists('t') ? t('account.multiplier') : 'Multiplier',
		'value' => eclipseManagePercent($loyaltyMultiplier) . 'x',
		'description' => function_exists('t') ? t('account.loyalty_multiplier_hint') : 'Multiplier applied to the skill bonus from Loyalty.'
	],
];

$public_info_cards = [
	[
		'label' => function_exists('t') ? t('account.real_name') : 'Real name',
		'value' => eclipseManageDisplayValue($account_rlname),
		'hint' => function_exists('t') ? t('account.real_name_hint') : 'Used for support and account validation.'
	],
	[
		'label' => function_exists('t') ? t('account.location') : 'Location',
		'value' => eclipseManageDisplayValue($account_location),
		'hint' => function_exists('t') ? t('account.location_hint') : 'Public information shown on the account profile.'
	],
	[
		'label' => function_exists('t') ? t('account.characters') : 'Characters',
		'value' => function_exists('t') ? t('account.characters_created_count', ['count' => $players_count]) : $players_count . ' created',
		'hint' => function_exists('t') ? t('account.characters_online_now', ['count' => $online_players_count]) : $online_players_count . ' online now.'
	],
	[
		'label' => function_exists('t') ? t('account.highest_character') : 'Highest character',
		'value' => $highest_player === null ? (function_exists('t') ? t('account.no_character') : 'No character') : $highest_player->getName() . ' - level ' . $highest_player->getLevel(),
		'hint' => function_exists('t') ? t('account.highest_character_hint') : 'Based on this account characters.'
	],
	[
		'label' => function_exists('t') ? t('account.account_created_date') : 'Account created',
		'value' => date('d/m/Y', $account_created),
		'hint' => function_exists('t') ? t('account.account_age_loyalty_hint') : 'Account age also feeds Loyalty.'
	],
	[
		'label' => 'Recovery Key',
		'value' => empty($recovery_key) ? (function_exists('t') ? t('account.pending') : 'Pending') : (function_exists('t') ? t('account.registered') : 'Registered'),
		'hint' => empty($recovery_key) ? (function_exists('t') ? t('account.recovery_key_missing_hint') : 'Register to protect the account.') : (function_exists('t') ? t('account.recovery_key_registered_hint') : 'Your account has a recovery key.')
	],
];

$account_players = $account_logged->getPlayersList();
$account_players->orderBy('id');

$twig->display('account.management.html.twig', [
	'welcome_message' => $welcome_message,
	'recovery_key' => $recovery_key,
	'email_change' => $email_change,
	'email_request' => $email_request,
	'email_new_time' => $email_new_time,
	'email_new' => isset($email_new) ? $email_new : '',
	'account' => (USE_ACCOUNT_NAME ? $account_logged->getName() : (USE_ACCOUNT_NUMBER ? $account_logged->getNumber() : $account_logged->getId())),
	'account_email' => $account_email,
	'account_created' => $account_created,
	'account_web_lastlogin' => $account_lastlogin,
	'account_status' => $account_status,
	'account_registered' => $account_registered,
	'account_rlname' => $account_rlname,
	'account_location' => $account_location,
	'account_birth_date' => $account_birth_date,
	'account_cpf_masked' => eclipseManageMaskCpf($account_cpf),
	'players_count' => $players_count,
	'online_players_count' => $online_players_count,
	'vip_benefits' => $vip_benefits,
	'public_info_cards' => $public_info_cards,
	'actions' => $actions,
	'players' => $account_players,
	'duo_invites' => $duo_invites
]);
