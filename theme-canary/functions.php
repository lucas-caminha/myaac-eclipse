<?php
defined('MYAAC') or die('Direct access not allowed!');

function isVipSystemEnabled(): bool {
	return getBoolean(configLua('vipSystemEnabled'));
}

function eclipse_i18n_supported_locales(): array {
	return ['pt-BR', 'en', 'es'];
}

function eclipse_i18n_default_locale(): string {
	return 'pt-BR';
}

function eclipse_i18n_normalize_locale(?string $locale): string {
	if ($locale === null || $locale === '') {
		return eclipse_i18n_default_locale();
	}

	$locale = str_replace('_', '-', trim($locale));
	$map = [
		'pt' => 'pt-BR',
		'pt-br' => 'pt-BR',
		'en-us' => 'en',
		'en-gb' => 'en',
		'en' => 'en',
		'es-es' => 'es',
		'es-mx' => 'es',
		'es' => 'es',
	];

	$key = strtolower($locale);
	return in_array($locale, eclipse_i18n_supported_locales(), true)
		? $locale
		: ($map[$key] ?? eclipse_i18n_default_locale());
}

function eclipse_i18n_detect_locale(): string {
	$requested = $_GET['lang'] ?? $_COOKIE['eclipse_lang'] ?? null;

	if (!$requested && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $part) {
			$candidate = trim(explode(';', $part)[0]);
			$normalized = eclipse_i18n_normalize_locale($candidate);
			if ($normalized !== eclipse_i18n_default_locale() || stripos($candidate, 'pt') === 0) {
				return $normalized;
			}
		}
	}

	return eclipse_i18n_normalize_locale(is_string($requested) ? $requested : null);
}

function eclipse_i18n_locale(): string {
	static $locale = null;

	if ($locale === null) {
		$locale = eclipse_i18n_detect_locale();
		if (isset($_GET['lang']) && !headers_sent()) {
			setcookie('eclipse_lang', $locale, [
				'expires' => time() + 60 * 60 * 24 * 365,
				'path' => BASE_DIR . '/',
				'samesite' => 'Lax',
			]);
			$_COOKIE['eclipse_lang'] = $locale;
		}
	}

	return $locale;
}

function eclipse_i18n_load_catalog(string $locale): array {
	static $catalogs = [];

	if (!isset($catalogs[$locale])) {
		$file = __DIR__ . '/themes/canary/lang/' . $locale . '.php';
		$catalogs[$locale] = file_exists($file) ? require $file : [];

		$publicFile = __DIR__ . '/themes/canary/lang/public/' . $locale . '.php';
		if (file_exists($publicFile)) {
			$catalogs[$locale] = array_replace($catalogs[$locale], require $publicFile);
		}
	}

	return $catalogs[$locale];
}

function eclipse_i18n_translate(string $key, array $params = [], ?string $locale = null): string {
	$locale = $locale ? eclipse_i18n_normalize_locale($locale) : eclipse_i18n_locale();
	$catalog = eclipse_i18n_load_catalog($locale);
	$fallback = $locale === eclipse_i18n_default_locale() ? [] : eclipse_i18n_load_catalog(eclipse_i18n_default_locale());

	$text = $catalog[$key] ?? $fallback[$key] ?? $key;
	foreach ($params as $name => $value) {
		$text = str_replace('{' . $name . '}', (string) $value, $text);
	}

	return $text;
}

function eclipse_i18n_url(?string $url = null, ?string $locale = null): string {
	$locale = eclipse_i18n_normalize_locale($locale ?? eclipse_i18n_locale());
	$url = $url ?: ($_SERVER['REQUEST_URI'] ?? '/');
	$parts = parse_url($url);
	$query = [];

	if (!empty($parts['query'])) {
		parse_str($parts['query'], $query);
	}

	$query['lang'] = $locale;
	$path = $parts['path'] ?? '';
	$result = $path . '?' . http_build_query($query);

	if (!empty($parts['fragment'])) {
		$result .= '#' . $parts['fragment'];
	}

	return $result;
}

function eclipse_i18n_menu_label(array $menu): string {
	$link = trim((string) ($menu['link'] ?? ''), '/');
	$key = 'menu.' . str_replace(['/', '-'], ['.', '_'], $link);

	$translated = eclipse_i18n_translate($key);
	if ($translated !== $key) {
		return $translated;
	}

	return eclipse_i18n_translate('menu.label.' . md5((string) ($menu['name'] ?? '')), [], null);
}

if (!function_exists('t')) {
	function t(string $key, array $params = [], ?string $locale = null): string {
		return eclipse_i18n_translate($key, $params, $locale);
	}
}
