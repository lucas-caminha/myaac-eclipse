-- Add the Eclipse OT new player guide page and menu entry.
-- Apply with: mysql canary < sql/025-add-server-guide-page.sql

START TRANSACTION;

INSERT INTO `myaac_pages` (`name`, `title`, `body`, `date`, `player_id`, `php`, `enable_tinymce`, `access`, `hide`)
VALUES (
  'server-guide',
  'Guia Inicial',
  '<?php require BASE . ''plugins/theme-canary/themes/canary/pages/server-guide.php'';',
  UNIX_TIMESTAMP(),
  0,
  1,
  0,
  0,
  0
)
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `body` = VALUES(`body`),
  `php` = VALUES(`php`),
  `enable_tinymce` = VALUES(`enable_tinymce`),
  `access` = VALUES(`access`),
  `hide` = VALUES(`hide`);

SET @server_guide_menu_exists := (
  SELECT COUNT(*)
  FROM `myaac_menu`
  WHERE `template` = 'canary'
    AND `link` = 'server-guide'
);

UPDATE `myaac_menu`
SET `ordering` = `ordering` + 1
WHERE `template` = 'canary'
  AND `category` = 5
  AND `ordering` >= 1
  AND @server_guide_menu_exists = 0;

INSERT INTO `myaac_menu` (`template`, `name`, `link`, `access`, `blank`, `color`, `category`, `ordering`, `enabled`)
SELECT 'canary', 'Guia Inicial', 'server-guide', 0, 0, '', 5, 1, 1
WHERE @server_guide_menu_exists = 0;

UPDATE `myaac_menu`
SET
  `name` = 'Guia Inicial',
  `access` = 0,
  `blank` = 0,
  `color` = '',
  `category` = 5,
  `ordering` = 1,
  `enabled` = 1
WHERE `template` = 'canary'
  AND `link` = 'server-guide';

COMMIT;
