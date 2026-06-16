<?php
/**
 * Apply paid boosted sponsorships to boosted_boss / boosted_creature.
 *
 * CLI:
 * php /var/www/html/plugins/theme-canary/webhooks/boosted-sponsor-apply.php
 *
 * Optional HTTP:
 * /plugins/theme-canary/webhooks/boosted-sponsor-apply.php?token=...
 */
$basePath = dirname(__DIR__, 3);
chdir($basePath);

require_once 'common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';

require __DIR__ . '/../themes/canary/pages/boosted-sponsor-apply.php';
