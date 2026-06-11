<?php
/**
 * Raw Mercado Pago webhook endpoint.
 *
 * Public URL after deploy:
 * /plugins/theme-canary/webhooks/mercadopago.php
 */
$basePath = dirname(__DIR__, 3);
chdir($basePath);

require_once 'common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';

require __DIR__ . '/../themes/canary/pages/mercadopago-webhook.php';
