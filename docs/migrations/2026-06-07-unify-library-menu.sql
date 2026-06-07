-- Unifies the Canary Library menu into a single Server Info/Commands entry.
-- The label uses UNHEX to avoid terminal encoding issues when applied over SSH.

SET @label := CONVERT(UNHEX('436f6d616e646f73206520496e666f726d61c3a7c3b56573') USING utf8mb4);

DELETE FROM myaac_menu
WHERE template = 'canary'
  AND link IN ('monsters', 'spells', 'commands', 'exp-table');

UPDATE myaac_menu
SET name = @label,
    link = 'ots-info',
    category = 5,
    ordering = 0,
    enabled = 1
WHERE template = 'canary'
  AND link = 'ots-info';

INSERT INTO myaac_menu (template, name, link, category, ordering, enabled, access, blank, color)
SELECT 'canary', @label, 'ots-info', 5, 0, 1, 0, 0, ''
WHERE NOT EXISTS (
  SELECT 1
  FROM myaac_menu
  WHERE template = 'canary'
    AND link = 'ots-info'
);
