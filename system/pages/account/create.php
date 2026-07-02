<?php
/**
 * Create account
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\CreateCharacter;
use MyAAC\Models\AccountAction;
use MyAAC\Models\AccountEmailVerify;

defined('MYAAC') or die('Direct access not allowed!');
$title = function_exists('t') ? t('account.create_account') : 'Create account';

if (setting('core.account_country'))
	require SYSTEM . 'countries.conf.php';

if($logged)
{
	echo function_exists('t') ? t('account.logout_before_create') : 'Log out of the current account before creating a new one.';
	return;
}

csrfProtect();

if(setting('core.account_create_character_create')) {
	$createCharacter = new CreateCharacter();
}

$account_type = 'número';
if (config('account_login_by_email')) {
	$account_type = 'email';
}
else {
	if(USE_ACCOUNT_NAME) {
		$account_type = 'nome';
	}
}

function eclipseCreateAccountEscape($value)
{
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function eclipseCreateAccountVocationGuide($vocationId)
{
	global $config;

	$vocationName = isset($config['vocations'][$vocationId]) ? $config['vocations'][$vocationId] : 'Aventureiro';
	$slug = strtolower(preg_replace('/[^a-z0-9]+/i', '', $vocationName));
	$guides = [
		'knight' => [
			'image' => 'knightbanner.png',
			'name' => 'Cavaleiro',
			'role' => 'Defensor de linha de frente',
			'description' => 'Feito para combate corpo a corpo, armaduras pesadas e pressão constante no centro da batalha.',
		],
		'paladin' => [
			'image' => 'paladinbanner.png',
			'name' => 'Paladino',
			'role' => 'Híbrido de distância',
			'description' => 'Um atirador flexível que mistura dano à distância, sobrevivência e magia útil.',
		],
		'monk' => [
			'image' => 'monkbanner.png',
			'name' => 'Monge',
			'role' => 'Espírito marcial',
			'description' => 'Um lutador disciplinado que combina força física com técnicas espirituais.',
		],
		'sorcerer' => [
			'image' => 'sorcererbanner.png',
			'name' => 'Feiticeiro',
			'role' => 'Dano elemental',
			'description' => 'Focado em magias destrutivas, dano explosivo e poder ofensivo.',
		],
		'druid' => [
			'image' => 'druidbanner.png',
			'name' => 'Druida',
			'role' => 'Suporte e cura',
			'description' => 'Um conjurador ligado à natureza, com cura, suporte e utilidade confiável para o grupo.',
		],
	];

	foreach ($guides as $key => $guide) {
		if (strpos($slug, $key) !== false) {
			return $guide;
		}
	}

	return [
		'image' => '',
		'name' => $vocationName,
		'role' => 'Aventureiro de Eclipse',
		'description' => 'Seu personagem está pronto para iniciar a jornada, evoluir e disputar seu espaço no servidor.',
	];
}

function eclipseRenderCreateAccountSuccess($twig, $accountType, $accountValue, $characterEnabled, $characterName, $characterVocation)
{
	global $template_path;

	$translate = function ($key, array $params = [], $fallback = '') {
		if (function_exists('t')) {
			$value = t($key, $params);
			if ($value !== $key) {
				return $value;
			}
		}

		foreach ($params as $name => $value) {
			$fallback = str_replace('{' . $name . '}', (string)$value, $fallback);
		}

		return $fallback;
	};

	$accountTypeLabel = $accountType === 'nome'
		? $translate('account.account_name', [], 'Account name')
		: $translate('account.account_type_label', ['type' => ucfirst($accountType)], ucfirst($accountType) . ' da conta');
	$accountTypeText = $accountType === 'nome'
		? $translate('account.account_name_short', [], 'account name')
		: eclipseCreateAccountEscape($accountType);
	$accountValueText = eclipseCreateAccountEscape($accountValue);
	$characterNameText = eclipseCreateAccountEscape($characterName ?: $translate('account.initial_character', [], 'Initial character'));
	$serverName = eclipseCreateAccountEscape(configLua('serverName'));
	$characterCard = '';
	$serverHeroUrl = eclipseCreateAccountEscape($template_path . '/images/header/bgs/arise-red-fortress.png');
	$heroImage = '<img class="eclipse-create-success-art eclipse-create-success-server-art" src="' . $serverHeroUrl . '" alt="">';
	$intro = $characterEnabled
		? $translate('account.success_intro_with_character', [], 'Your account and character were created. Now log in to the game and choose your starting outfit.')
		: $translate('account.success_intro_account_only', [], 'Your account was created. Open the account panel whenever you want to create your first character.');

	if ($characterEnabled) {
		$guide = eclipseCreateAccountVocationGuide($characterVocation);
		$vocationKeyByImage = [
			'knightbanner.png' => 'knight',
			'paladinbanner.png' => 'paladin',
			'monkbanner.png' => 'monk',
			'sorcererbanner.png' => 'sorcerer',
			'druidbanner.png' => 'druid',
		];
		if (isset($vocationKeyByImage[$guide['image']])) {
			$vocationKey = $vocationKeyByImage[$guide['image']];
			$guide['name'] = $translate('account.vocation.' . $vocationKey . '.name', [], $guide['name']);
			$guide['role'] = $translate('account.vocation.' . $vocationKey . '.role', [], $guide['role']);
			$guide['description'] = $translate('account.vocation.' . $vocationKey . '.description', [], $guide['description']);
		}
		$bannerStyle = '';
		$vocationImage = '';
		if (!empty($guide['image'])) {
			$vocationBannerUrl = eclipseCreateAccountEscape($template_path . '/images/vocations/' . $guide['image']);
			$vocationImageStyle = '--eclipse-create-vocation-image: url(' . $vocationBannerUrl . ');';
			$bannerStyle = ' style="' . $vocationImageStyle . '"';
			$vocationImage = '<img class="eclipse-create-success-art" src="' . $vocationBannerUrl . '" alt="">';
		}

		$characterCard = '
			<section class="eclipse-create-success-card eclipse-create-success-character">
				<h3 class="eclipse-create-success-card-title">' . eclipseCreateAccountEscape($translate('account.character_created', [], 'Character created')) . '</h3>
				<div class="eclipse-create-success-banner"' . $bannerStyle . '>
					' . $vocationImage . '
					<div>
						<span>' . eclipseCreateAccountEscape($translate('account.chosen_vocation', [], 'Chosen vocation')) . '</span>
						<strong>' . $characterNameText . '</strong>
						<small>' . eclipseCreateAccountEscape($guide['name']) . ' - ' . eclipseCreateAccountEscape($guide['role']) . '</small>
					</div>
				</div>
				<p>' . $translate('account.character_created_text', ['name' => '<strong>' . $characterNameText . '</strong>'], 'The character {name} was created. Choose the outfit when you enter the game for the first time.') . '</p>
				<p class="eclipse-create-success-vocation-desc">' . eclipseCreateAccountEscape($guide['description']) . '</p>
			</section>';
	}

	$description = '
		<div class="eclipse-create-success">
			<section class="eclipse-create-success-hero">
				' . $heroImage . '
				<span>' . eclipseCreateAccountEscape($translate('account.server_banner', [], 'Server banner')) . '</span>
				<h2>' . eclipseCreateAccountEscape($translate('account.welcome_to_server', ['server' => $serverName], 'Welcome to {server}')) . '</h2>
				<p>' . eclipseCreateAccountEscape($intro) . '</p>
			</section>
			<div class="eclipse-create-success-grid">
				<section class="eclipse-create-success-card eclipse-create-success-account">
					<h3 class="eclipse-create-success-card-title">' . eclipseCreateAccountEscape($translate('account.account_created', [], 'Account created')) . '</h3>
					<span>' . eclipseCreateAccountEscape($accountTypeLabel) . '</span>
					<strong>' . $accountValueText . '</strong>
					<p>' . eclipseCreateAccountEscape($translate('account.success_credentials_needed', ['account_type' => $accountTypeText], 'You will need your {account_type} and password to play Eclipse OT.')) . '</p>
					<p>' . eclipseCreateAccountEscape($translate('account.success_keep_safe', [], 'Keep this information in a safe place and never share it with anyone.')) . '</p>
				</section>
				' . $characterCard . '
			</div>
			<div class="eclipse-create-success-next">
				<strong>' . eclipseCreateAccountEscape($translate('account.see_you', [], 'See you in Eclipse OT!')) . '</strong>
				<span>' . eclipseCreateAccountEscape($translate('account.next_step_play', [], 'Open the client, log in with your account and begin your journey.')) . '</span>
			</div>
		</div>';

	$twig->display('success.html.twig', [
		'title' => $translate('account.account_created', [], 'Account created'),
		'description' => $description,
		'custom_buttons' => $characterEnabled ? '' : null,
	]);
}

$errors = array();
$save = isset($_POST['save']) && $_POST['save'] == 1;
if($save)
{
	$cooldown = setting('core.account_create_ip_block_cooldown');;
	if ($cooldown > 0) {
		$accountAction = AccountAction::where('ip', get_browser_real_ip())->where('action', 'Account created.')->where('date', '>=', time() - ($cooldown * 60))->first();

		if ($accountAction) {
			$minute = ($cooldown > 1 ? 'minutos' : 'minuto');
			$errors['account'] = "Você precisa aguardar $cooldown $minute antes de criar outra conta.";
		}
	}

	if(!config('account_login_by_email')) {
		if(USE_ACCOUNT_NAME) {
			$account_name = $_POST['account'];
		}
		else {
			$account_id = $_POST['account'];
		}
	}

	$email = $_POST['email'];
	$password = $_POST['password'];
	$password_confirm = $_POST['password_confirm'];

	// account
	if(!config('account_login_by_email')) {
		if (isset($account_id)) {
			if (!Validator::accountId($account_id)) {
				$errors['account'] = Validator::getLastError();
			}
		} else if (!Validator::accountName($account_name))
			$errors['account'] = Validator::getLastError();
	}

	// email
	if(!Validator::email($email))
		$errors['email'] = Validator::getLastError();

	// country
	$country = '';
	if (setting('core.account_country'))
	{
		$country = $_POST['country'];
		if(!isset($country))
			$errors['country'] = function_exists('t') ? t('account.country_required') : 'Country was not provided.';
		elseif(!isset($config['countries'][$country]))
			$errors['country'] = function_exists('t') ? t('account.country_invalid') : 'Invalid country.';
	}

	// password
	if(empty($password)) {
		$errors['password'] = function_exists('t') ? t('account.js_password_required') : 'Enter the password for your new account.';
	}
	elseif($password != $password_confirm) {
		$errors['password'] = function_exists('t') ? t('account.passwords_do_not_match') : 'Passwords do not match.';
	}
	else if(!Validator::password($password)) {
		$errors['password'] = Validator::getLastError();
	}

	// check if account name is not equal to password
	if(!config('account_login_by_email') && USE_ACCOUNT_NAME && strtoupper($account_name) == strtoupper($password)) {
		$errors['password'] = function_exists('t') ? t('account.password_same_as_account') : 'Password cannot be the same as the account name.';
	}

	if(setting('core.account_mail_unique'))
	{
		$test_email_account = new OTS_Account();
		$test_email_account->findByEMail($email);
		if($test_email_account->isLoaded())
			$errors['email'] = 'Já existe uma conta com este email.';
	}

	$account_db = new OTS_Account();
	if (config('account_login_by_email')) {
		$account_db->findByEMail($email);
	}
	else {
		if(USE_ACCOUNT_NAME) {
			$account_db->find($account_name);
		}
		else {
			$account_db->load($account_id);
		}
	}

	if($account_db->isLoaded()) {
		if (config('account_login_by_email') && !setting('core.account_mail_unique')) {
			$errors['account'] = 'Já existe uma conta com este email.';
		}
		else if (!config('account_login_by_email')) {
			if (USE_ACCOUNT_NAME)
				$errors['account'] = 'Já existe uma conta com este nome.';
			else
				$errors['account'] = 'Já existe uma conta com este ID.';
		}
	}

	if(!isset($_POST['accept_rules']) || $_POST['accept_rules'] !== 'true')
		$errors['accept_rules'] = 'Você precisa aceitar as regras do ' . $config['lua']['serverName'] . ' para criar uma conta.';

	$params = array(
		'account' => $account_db,
		'email' => $email,
		'country' => $country,
		'password' => $password,
		'password_confirm' => $password_confirm,
		'accept_rules' => isset($_POST['accept_rules']) && $_POST['accept_rules'] === 'true',
	);

	if (!config('account_login_by_email')) {
		if (USE_ACCOUNT_NAME) {
			$params['account_name'] = $_POST['account'];
		} else {
			$params['account_id'] = $_POST['account'];
		}
	}

	/**
	 * two hooks for compatibility
	 */
	$hooks->trigger(HOOK_ACCOUNT_CREATE_AFTER_SUBMIT, $params);
	if (!$hooks->trigger(HOOK_ACCOUNT_CREATE_POST, $params)) {
		return;
	}

	if(setting('core.account_create_character_create')) {
		$character_name = isset($_POST['name']) ? trim(stripslashes($_POST['name'])) : null;
		$character_sex = isset($_POST['sex']) ? (int)$_POST['sex'] : null;
		$character_vocation = isset($_POST['vocation']) ? (int)$_POST['vocation'] : null;
		$character_town = isset($_POST['town']) ? (int)$_POST['town'] : null;

		$createCharacter->check($character_name, $character_sex, $character_vocation, $character_town, $errors);
	}

	if(empty($errors))
	{
		$hasBeenCreatedByEMail = false;

		$new_account = new OTS_Account();
		if (config('account_login_by_email')) {
			$new_account->createWithEmail($email);
			$hasBeenCreatedByEMail = true;
		}
		else {
			if(USE_ACCOUNT_NAME)
				$new_account->create($account_name);
			else
				$new_account->create(NULL, $account_id);
		}

		if(USE_ACCOUNT_SALT)
		{
			$salt = generateRandomString(10, false, true, true);
			$password = $salt . $password;
		}

		$new_account->setPassword(encrypt($password));
		$new_account->setEMail($email);

		$settingAccountPremiumDays = setting('core.account_premium_days');
		if($settingAccountPremiumDays && $settingAccountPremiumDays > 0) {
			$new_account->setPremDays($settingAccountPremiumDays);

			if (!isCanary()) {
				$lastDay = 0;
				if($settingAccountPremiumDays != 0 && $settingAccountPremiumDays != OTS_Account::GRATIS_PREMIUM_DAYS) {
					$lastDay = time();
				}

				$new_account->setLastLogin($lastDay);
			}
		}

		$new_account->save();

		$hooks->trigger(HOOK_ACCOUNT_CREATE_AFTER_SAVED, ['account' => $new_account]);

		if(USE_ACCOUNT_SALT)
			$new_account->setCustomField('salt', $salt);

		$new_account->setCustomField('created', time());
		$new_account->logAction('Account created.');

		if(setting('core.account_country')) {
			$new_account->setCustomField('country', $country);
		}

		$accountDefaultPremiumPoints = setting('core.account_premium_points');
		if($accountDefaultPremiumPoints > 0) {
			$new_account->setCustomField('premium_points', $accountDefaultPremiumPoints);
		}

		$accountDefaultCoins = setting('core.account_coins');
		if(HAS_ACCOUNT_COINS && $accountDefaultCoins > 0) {
			$new_account->setCustomField('coins', $accountDefaultCoins);
		}

		$accountDefaultCoinsTransferable = setting('core.account_coins_transferable');
		if((HAS_ACCOUNT_COINS_TRANSFERABLE || HAS_ACCOUNT_TRANSFERABLE_COINS) && $accountDefaultCoinsTransferable > 0) {
			$new_account->setCustomField(ACCOUNT_COINS_TRANSFERABLE_COLUMN, $accountDefaultCoinsTransferable);
		}

		$tmp_account = $email;
		if (!config('account_login_by_email')) {
			$tmp_account = (USE_ACCOUNT_NAME ? $account_name : $account_id);
		}

		if(setting('core.mail_enabled') && setting('core.account_mail_verify'))
		{
			$hash = md5(generateRandomString(16, true, true) . $email);

			AccountEmailVerify::create([
				'account_id' => $new_account->getId(),
				'hash' => $hash,
				'sent_at' => time(),
			]);

			$verify_url = getLink('account/confirm-email/' . $hash);
			$body_html = $twig->render('mail.account.verify.html.twig', array(
				'account' => $tmp_account,
				'verify_url' => generateLink($verify_url, $verify_url, true)
			));

			if(_mail($email, (function_exists('t') ? t('account.verify_email_subject', ['server' => $config['lua']['serverName']]) : 'New account on ' . $config['lua']['serverName']), $body_html))
			{
				warning(function_exists('t') ? t('account.verify_email_warning', ['email' => $email]) : "Before logging in, confirm your email. The verification link was sent to $email. If it does not arrive, also check your spam folder.");
			}
			else
			{
				error(function_exists('t') ? t('account.email_create_error') : 'There was an error sending the email. The account was not created. Try again.');
				$new_account->delete();

				return;
			}
		}
		else
		{
			if(setting('core.account_create_auto_login')) {
				if ($hasBeenCreatedByEMail) {
					$_POST['account_login'] = $email;
				}
				else {
					$_POST['account_login'] = USE_ACCOUNT_NAME ? $account_name : $account_id;
				}

				$_POST['password_login'] = $password_confirm;

				require PAGES . 'account/login.php';
				header('Location: ' . getLink('account/manage'));
			}

			if(setting('core.mail_enabled') && setting('core.account_welcome_mail'))
			{
				$mailBody = $twig->render('account.welcome_mail.html.twig', array(
					'account' => $tmp_account
				));

				if(_mail($email, (function_exists('t') ? t('account.welcome_email_subject', ['server' => $config['lua']['serverName']]) : 'Your account on ' . $config['lua']['serverName']), $mailBody))
					echo '<br /><small>' . (function_exists('t') ? t('account.welcome_email_sent', ['email' => '<b>' . $email . '</b>']) : 'This information was sent to ' . '<b>' . $email . '</b>' . '.') . '</small>';
				else {
					error(function_exists('t') ? t('account.email_send_error') : 'There was an error sending the email.');
				}
			}
		}

		if(setting('core.account_create_character_create')) {
			// character creation
			ob_start();
			$character_created = $createCharacter->doCreate($character_name, $character_sex, $character_vocation, $character_town, $new_account, $errors);
			$characterCreationOutput = ob_get_clean();
			if (!$character_created) {
				echo $characterCreationOutput;
				error('There was an error creating your character. Please create your character later in account management page.');
				error(implode(' ', $errors));
				return;
			}
		}

		eclipseRenderCreateAccountSuccess($twig, $account_type, $tmp_account, setting('core.account_create_character_create'), $character_name ?? '', $character_vocation ?? null);

		return;
	}
}

$country_recognized = null;
if(setting('core.account_country_recognize')) {
	$country_session = getSession('country');
	if($country_session !== false) { // get from session
		$country_recognized = $country_session;
	}
	else {
		ini_set('default_socket_timeout', 5);

		$info = json_decode(@file_get_contents('https://ipinfo.io/' . get_browser_real_ip() . '/geo'), true);
		if(isset($info['country'])) {
			$country_recognized = strtolower($info['country']);
			setSession('country', $country_recognized);
		}
	}
}

if(!empty($errors))
	$twig->display('error_box.html.twig', array('errors' => $errors));

if (setting('core.account_country')) {
	$countries = array();
	foreach (setting('core.account_countries_most_popular') ?? [] as $c)
		$countries[$c] = $config['countries'][$c];

	$countries['--'] = '----------';
	foreach ($config['countries'] as $code => $c)
		$countries[$code] = $c;
}

$twig->display('account.create.js.html.twig');

$params = array(
	'account' => isset($_POST['account']) ? $_POST['account'] : '',
	'email' => isset($_POST['email']) ? $_POST['email'] : '',
	'countries' => isset($countries) ? $countries : null,
	'accept_rules' => isset($_POST['accept_rules']) ? $_POST['accept_rules'] : false,
	'country_recognized' => $country_recognized,
	'country' => isset($country) ? $country : null,
	'errors' => $errors,
	'save' => $save
);

if($save && setting('core.account_create_character_create')) {
	$params = array_merge($params, array(
		'name' => $character_name,
		'sex' => $character_sex,
		'vocation' => $character_vocation,
		'town' => $character_town
	));
}

$twig->display('account.create.html.twig', $params);

