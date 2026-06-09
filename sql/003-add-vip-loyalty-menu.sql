-- Add VIP & Loyalty to the Eclipse OT Canary template library menu.
-- Apply with: mysql canary < sql/003-add-vip-loyalty-menu.sql
START TRANSACTION;

UPDATE myaac_menu
SET
  name = 'VIP & Loyalty',
  category = 5,
  ordering = 0,
  enabled = 1,
  access = 0,
  blank = 0,
  color = ''
WHERE template = 'canary'
  AND link = 'vip-loyalty';

INSERT INTO myaac_menu (template, name, link, access, blank, color, category, ordering, enabled)
SELECT 'canary', 'VIP & Loyalty', 'vip-loyalty', 0, 0, '', 5, 0, 1
WHERE NOT EXISTS (
  SELECT 1
  FROM myaac_menu
  WHERE template = 'canary'
    AND link = 'vip-loyalty'
);

UPDATE myaac_menu
SET ordering = 1
WHERE template = 'canary'
  AND link = 'ots-info';

COMMIT;
