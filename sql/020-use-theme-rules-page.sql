-- Point the Rules page to the Canary theme PHP file so the page can use
-- the shared visual components and language catalogs.
UPDATE `myaac_pages`
SET
  `title` = 'Regras do Servidor',
  `php` = 1,
  `hide` = 0,
  `body` = '<?php require BASE . ''plugins/theme-canary/themes/canary/pages/rules.php'';'
WHERE `name` = 'rules';
