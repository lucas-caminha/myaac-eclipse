-- Highlight the Eclipse OT starter guide in the Canary library menu.
-- Apply with: mysql canary < sql/026-highlight-server-guide-menu.sql

START TRANSACTION;

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
  AND `link` <> 'server-guide'
  AND @server_guide_menu_exists = 0;

INSERT INTO `myaac_menu` (`template`, `name`, `link`, `access`, `blank`, `color`, `category`, `ordering`, `enabled`)
SELECT 'canary', 'Guia Inicial', 'server-guide', 0, 0, 'ffe681', 5, 1, 1
WHERE @server_guide_menu_exists = 0;

UPDATE `myaac_menu`
SET
  `name` = 'Guia Inicial',
  `access` = 0,
  `blank` = 0,
  `color` = 'ffe681',
  `category` = 5,
  `ordering` = 1,
  `enabled` = 1
WHERE `template` = 'canary'
  AND `link` = 'server-guide';

COMMIT;
