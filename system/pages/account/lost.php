<?php
/**
 * Eclipse OT lost account entry point.
 */

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Recuperar Conta';

if (!setting('core.mail_enabled')) {
	$twig->display('account/lost/unavailable.html.twig');
	return;
}

$twig->display('account/lost/form.html.twig');
