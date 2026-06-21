<?php
/**
 * Account management override for Eclipse OT.
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Minha Conta';
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
	return $enabled ? 'Sim' : 'Não';
}

function eclipseManagePercent(float $value): string
{
	return rtrim(rtrim(number_format($value, 2, ',', '.'), '0'), ',');
}

function eclipseManageDisplayValue($value, string $fallback = 'Não informado'): string
{
	$value = trim((string)$value);
	return $value !== '' ? $value : $fallback;
}

function eclipseManageMaskCpf($cpf): string
{
	$digits = preg_replace('/\D+/', '', (string)$cpf);
	if(strlen($digits) !== 11) {
		return 'Não informado';
	}

	return '***.***.***-' . substr($digits, -2);
}

$groups = new OTS_Groups_List();

/**
 * @var OTS_Account $account_logged
 */
$premDays = $account_logged->getPremDays();

$freePremium = isset($config['lua']['freePremium']) && getBoolean($config['lua']['freePremium']) || $premDays == OTS_Account::GRATIS_PREMIUM_DAYS;
$dayOrDays = ($premDays == 1 ? 'dia' : 'dias');

$vipSystemEnabled = isset($config['lua']['vipSystemEnabled']) && getBoolean($config['lua']['vipSystemEnabled']);
$premiumLabel = $vipSystemEnabled ? 'VIP' : 'Conta Premium';

if ($freePremium && !$vipSystemEnabled) {
	$account_status = '<b><span style="color: green">Conta Premium grátis</span></b>';
} else if(!$account_logged->isPremium()) {
	$account_status = '<b><span style="color: red">Conta gratuita</span></b>';
} else {
	$account_status = '<b><span style="color: green">' . $premiumLabel . ', restam ' . $premDays . ' '.$dayOrDays.'</span></b>';
}

$recovery_key = $account_logged->getCustomField('key');
if(empty($recovery_key))
	$account_registered = '<b><span style="color: red">Não</span></b>';
else
{
	if(setting('core.account_generate_new_reckey') && setting('core.mail_enabled'))
		$account_registered = '<b><span style="color: green">Sim ( <a href="' . getLink('account/register-new') . '"> Comprar nova Recovery Key </a> )</span></b>';
	else
		$account_registered = '<b><span style="color: green">Sim</span></b>';
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
		$welcome_message = '<span style="color: red">Sua conta está banida até '.date("j F Y, G:i:s", $account_logged->getBanTime()).'.</span>';
	else
		$welcome_message = '<span style="color: red">Sua conta está banida permanentemente.</span>';
else
	$welcome_message = 'Bem-vindo à sua conta!';

$email_change = '';
$email_request = false;
if($email_new_time > 1)
{
	if($email_new_time < time())
		$email_change = '<br>(Você pode aceitar <b>'.$email_new.'</b> como novo email.)';
	else
	{
		$email_change = ' <br>Você poderá aceitar o <b>novo email após '.date("j F Y", $email_new_time).".</b>";
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
		'label' => 'Experiência',
		'value' => '+' . $vipBonusExp . '%',
		'description' => 'Bônus aplicado automaticamente para contas VIP.'
	],
	[
		'icon' => 'PremiumIcon-TrackLoot.png',
		'label' => 'Loot',
		'value' => '+' . $vipBonusLoot . '%',
		'description' => 'Melhora a recompensa de criaturas conforme a configuração atual.'
	],
	[
		'icon' => 'PremiumIcon-Trainingstatues.png',
		'label' => 'Skills',
		'value' => '+' . $vipBonusSkill . '%',
		'description' => 'Acelera treino de skills e magic level quando ativo.'
	],
	[
		'icon' => 'PremiumIcon-QuickLoot.png',
		'label' => 'Auto Loot VIP',
		'value' => eclipseManageYesNo($vipAutoLootOnly),
		'description' => 'Indica se o Auto Loot está reservado para contas VIP.'
	],
	[
		'icon' => 'PremiumIcon-Login.png',
		'label' => 'Idle protegido',
		'value' => eclipseManageYesNo($vipStayOnline),
		'description' => 'Permite maior tolerância ao ficar parado online.'
	],
	[
		'icon' => 'PremiumIcon-House.png',
		'label' => 'Manter house',
		'value' => eclipseManageYesNo($vipKeepHouse),
		'description' => 'Protege a casa enquanto a conta mantém VIP ativo.'
	],
	[
		'icon' => 'PremiumIcon-Summons.png',
		'label' => 'Familiar',
		'value' => '-' . $vipFamiliarReduction . ' min',
		'description' => 'Redução de cooldown de familiar configurada no servidor.'
	],
	[
		'icon' => 'PremiumIcon-Loyalty.png',
		'label' => 'Loyalty',
		'value' => $loyaltyEnabled ? ($loyaltyPoints === null ? 'Ativo' : number_format($loyaltyPoints, 0, ',', '.') . ' pts') : 'Desativado',
		'description' => $loyaltyEnabled ? 'Ganha ' . $loyaltyCreationDay . ' ponto(s)/dia de conta e bônus por dias premium.' : 'Sistema de Loyalty desativado no servidor.'
	],
	[
		'icon' => 'PremiumIcon-Analytics.png',
		'label' => 'Multiplicador',
		'value' => eclipseManagePercent($loyaltyMultiplier) . 'x',
		'description' => 'Multiplicador aplicado ao bônus de skills por Loyalty.'
	],
];

$public_info_cards = [
	[
		'label' => 'Nome real',
		'value' => eclipseManageDisplayValue($account_rlname),
		'hint' => 'Usado em atendimento e validações da conta.'
	],
	[
		'label' => 'Localização',
		'value' => eclipseManageDisplayValue($account_location),
		'hint' => 'Informação pública exibida no perfil da conta.'
	],
	[
		'label' => 'Data de nascimento',
		'value' => eclipseManageDisplayValue($account_birth_date),
		'hint' => 'Dado privado usado para segurança cadastral.'
	],
	[
		'label' => 'CPF',
		'value' => eclipseManageMaskCpf($account_cpf),
		'hint' => 'Sempre exibido mascarado.'
	],
	[
		'label' => 'Personagens',
		'value' => $players_count . ' criado(s)',
		'hint' => $online_players_count . ' online agora.'
	],
	[
		'label' => 'Maior personagem',
		'value' => $highest_player === null ? 'Nenhum personagem' : $highest_player->getName() . ' - level ' . $highest_player->getLevel(),
		'hint' => 'Baseado nos personagens desta conta.'
	],
	[
		'label' => 'Conta criada',
		'value' => date('d/m/Y', $account_created),
		'hint' => 'Tempo de conta também alimenta o Loyalty.'
	],
	[
		'label' => 'Recovery Key',
		'value' => empty($recovery_key) ? 'Pendente' : 'Registrada',
		'hint' => empty($recovery_key) ? 'Cadastre para proteger a conta.' : 'Sua conta possui chave de recuperação.'
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
