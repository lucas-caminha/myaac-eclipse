-- Add LGPD privacy page to the Eclipse OT Canary account menu.
-- Apply with: mysql canary < sql/014-add-privacy-menu.sql
START TRANSACTION;

UPDATE myaac_menu
SET
  name = 'Privacidade e LGPD',
  category = 2,
  ordering = 4,
  enabled = 1,
  access = 0,
  blank = 0,
  color = ''
WHERE template = 'canary'
  AND link = 'privacy';

INSERT INTO myaac_menu (template, name, link, access, blank, color, category, ordering, enabled)
SELECT 'canary', 'Privacidade e LGPD', 'privacy', 0, 0, '', 2, 4, 1
WHERE NOT EXISTS (
  SELECT 1
  FROM myaac_menu
  WHERE template = 'canary'
    AND link = 'privacy'
);

UPDATE myaac_menu
SET ordering = 5
WHERE template = 'canary'
  AND link = 'rules';

UPDATE myaac_menu
SET ordering = 6
WHERE template = 'canary'
  AND link = 'downloads';

COMMIT;
