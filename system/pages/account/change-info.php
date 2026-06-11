<?php
/**
 * Change info
 *
 * Eclipse OT override: keeps MyAAC public information behavior and adds
 * donation profile fields used for future payment validation.
 */

use MyAAC\Models\Account;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Change Info';
require __DIR__ . '/base.php';

if(!$logged) {
	return;
}

csrfProtect();

if(setting('core.account_country'))
	require SYSTEM . 'countries.conf.php';

function eclipseNormalizeCpf($cpf)
{
	return preg_replace('/\D+/', '', (string)$cpf);
}

function eclipseIsValidCpf($cpf)
{
	$cpf = eclipseNormalizeCpf($cpf);

	if(strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
		return false;
	}

	for($t = 9; $t < 11; $t++) {
		$sum = 0;
		for($i = 0; $i < $t; $i++) {
			$sum += (int)$cpf[$i] * (($t + 1) - $i);
		}

		$digit = ((10 * $sum) % 11) % 10;
		if((int)$cpf[$t] !== $digit) {
			return false;
		}
	}

	return true;
}

function eclipseIsValidBirthDate($date)
{
	$parsed = DateTime::createFromFormat('Y-m-d', $date);
	return $parsed && $parsed->format('Y-m-d') === $date && $parsed <= new DateTime('today');
}

$account = Account::find($account_logged->getId());

$show_form = true;
$new_rlname = isset($_POST['info_rlname']) ? trim(htmlspecialchars(stripslashes($_POST['info_rlname']))) : '';
$new_birth_date = isset($_POST['info_birth_date']) ? trim(stripslashes($_POST['info_birth_date'])) : '';
$new_cpf = isset($_POST['info_cpf']) ? eclipseNormalizeCpf($_POST['info_cpf']) : '';
$new_location = isset($_POST['info_location']) ? htmlspecialchars(stripslashes($_POST['info_location'])) : '';
$new_country = isset($_POST['info_country']) ? htmlspecialchars(stripslashes($_POST['info_country'])) : '';

if(isset($_POST['changeinfosave']) && $_POST['changeinfosave'] == 1) {
	if(strlen($new_rlname) < 3) {
		$errors[] = 'Informe seu nome completo.';
	}

	if(!eclipseIsValidBirthDate($new_birth_date)) {
		$errors[] = 'Informe uma data de nascimento valida.';
	}

	if(!eclipseIsValidCpf($new_cpf)) {
		$errors[] = 'Informe um CPF valido.';
	}

	if(setting('core.account_country') && !isset($config['countries'][$new_country])) {
		$errors[] = 'Country is not correct.';
	}

	if(empty($errors)) {
		$account->rlname = $new_rlname;
		$account->birth_date = $new_birth_date;
		$account->cpf = $new_cpf;
		$account->location = $new_location;
		$account->country = $new_country;
		$account->save();

		$log = 'Changed registration information.';
		if(setting('core.account_country')) {
			$log .= ' Country set to <b>' . $config['countries'][$new_country] . '</b>.';
		}

		$account_logged->logAction($log);
		$twig->display('success.html.twig', array(
			'title' => 'Informações Atualizadas',
			'description' => 'Suas informações cadastrais foram atualizadas.'
		));
		$show_form = false;
	}
	else {
		$twig->display('error_box.html.twig', array('errors' => $errors));
	}
}

if($show_form) {
	$account_rlname = $account->rlname;
	$account_birth_date = $account->birth_date;
	$account_cpf = $account->cpf;
	$account_location = $account->location;

	if(setting('core.account_country')) {
		$account_country = $account->country;

		$countries = array();
		foreach(array('pl', 'se', 'br', 'us', 'gb',) as $country)
			$countries[$country] = $config['countries'][$country];

		$countries['--'] = '----------';

		foreach($config['countries'] as $code => $country)
			$countries[$code] = $country;
	}

	$twig->display('account.change-info.html.twig', array(
		'countries' => $countries ?? [],
		'account_rlname' => $account_rlname,
		'account_birth_date' => $account_birth_date,
		'account_cpf' => $account_cpf,
		'account_location' => $account_location,
		'account_country' => $account_country ?? ''
	));
}
?>
