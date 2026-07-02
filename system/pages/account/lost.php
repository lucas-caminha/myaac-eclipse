<?php
/**
 * Eclipse OT lost account entry point.
 */

defined('MYAAC') or die('Direct access not allowed!');

$title = function_exists('t') ? t('account.recover_account') : 'Recuperar Conta';

if (!setting('core.mail_enabled')) {
	$twig->display('account/lost/unavailable.html.twig');
	return;
}

$twig->display('account/lost/form.html.twig');
