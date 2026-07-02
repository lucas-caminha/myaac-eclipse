-- Let the MyAAC downloads custom page execute the versioned theme page.
-- This keeps the page translatable through the Eclipse i18n helper.

UPDATE `myaac_pages`
SET
  `title` = 'Baixar Cliente',
  `php` = 1,
  `hide` = 0,
  `body` = '<?php require BASE . ''plugins/theme-canary/themes/canary/pages/downloads.php'';'
WHERE `name` = 'downloads';
