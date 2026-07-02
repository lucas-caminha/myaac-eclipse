<?php
defined('MYAAC') or die('Direct access not allowed!');

use Twig\TwigFunction;

require_once dirname(__DIR__) . '/functions.php';

$twig = $params['twig'] ?? null;
if ($twig) {
	$twig->addFunction(new TwigFunction('t', function (string $key, array $params = [], ?string $locale = null): string {
		return eclipse_i18n_translate($key, $params, $locale);
	}));

	$twig->addFunction(new TwigFunction('locale', function (): string {
		return eclipse_i18n_locale();
	}));

	$twig->addFunction(new TwigFunction('localeUrl', function (?string $url = null, ?string $locale = null): string {
		return eclipse_i18n_url($url, $locale);
	}));

	$twig->addGlobal('current_locale', eclipse_i18n_locale());
	$twig->addGlobal('supported_locales', eclipse_i18n_supported_locales());
}
