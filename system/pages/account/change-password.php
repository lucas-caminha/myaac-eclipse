<?php
/**
 * Change password
 *
 * @package   MyAAC
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = function_exists('t') ? t('account.change_password') : 'Change Password';
require __DIR__ . '/base.php';

if(!$logged) {
	return;
}

csrfProtect();

$new_password = $_POST['new_password'] ?? null;
$new_password_confirm = $_POST['new_password_confirm'] ?? null;
$old_password = $_POST['old_password'] ?? null;

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

if(is_null($new_password) && is_null($new_password_confirm) && is_null($old_password)) {
	$twig->display('account.change-password.html.twig');
}
else {
	if(empty($new_password) || empty($new_password_confirm) || empty($old_password)){
		$errors[] = $translate('account.change_password_fill_form', [], 'Please fill in the form.');
	}

	if($new_password != $new_password_confirm) {
		$errors[] = $translate('account.change_password_mismatch', [], 'The new passwords do not match.');
	}

	if(empty($errors)) {
		if(!Validator::password($new_password)) {
			$errors[] = Validator::getLastError();
		}

		/** @var OTS_Account $account_logged */
		$old_password_hashed = encrypt((USE_ACCOUNT_SALT ? $account_logged->getCustomField('salt') : '') . $old_password);
		if($old_password_hashed != $account_logged->getPassword()) {
			$errors[] = $translate('account.current_password_incorrect', [], 'Current password is incorrect.');
		}
		else if ($old_password == $new_password) {
			$errors[] = $translate('account.change_password_same_as_old', [], 'The new password must be different from the current password.');
		}

		$hooks->trigger(HOOK_ACCOUNT_CHANGE_PASSWORD_POST);
	}

	if(!empty($errors)){
		$twig->display('error_box.html.twig', array('errors' => $errors));
		$twig->display('account.change-password.html.twig');
	}
	else {
		$org_pass = $new_password;

		if(USE_ACCOUNT_SALT) {
			$salt = generateRandomString(10, false, true, true);
			$new_password = $salt . $new_password;
			$account_logged->setCustomField('salt', $salt);
		}

		$new_password = encrypt($new_password);
		$account_logged->setPassword($new_password);
		$account_logged->save();
		$account_logged->logAction('Account password changed.');

		$message = '';
		if(setting('core.mail_enabled') && setting('core.mail_send_when_change_password')) {
			$mailBody = $twig->render('mail.password_changed.html.twig', array(
				'new_password' => $org_pass,
				'ip' => get_browser_real_ip(),
			));

			if(_mail($account_logged->getEMail(), $translate('account.password_changed_email_subject', ['server' => $config['lua']['serverName']], $config['lua']['serverName'] . ' - Changed password'), $mailBody)) {
				$message = '<br/><small>' . $translate('account.password_changed_email_sent', ['email' => '<b>' . htmlspecialchars($account_logged->getEMail(), ENT_QUOTES, 'UTF-8') . '</b>'], 'A confirmation was sent to {email}.') . '</small>';
			}
			else {
				$message = '<br/><p class="error">' . $translate('account.password_changed_email_error', [], 'An error occurred while sending the confirmation email.') . '</p>';
			}
		}

		$twig->display('success.html.twig', array(
			'title' => $translate('account.password_changed_title', [], 'Password Changed'),
			'description' => $translate('account.password_changed_description', [], 'Your password has been changed.') . $message
		));
		setSession('password', $new_password);
	}
}
