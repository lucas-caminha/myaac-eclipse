-- Add Monk to the MyAAC character creation vocation samples.
-- Apply with: mysql canary < sql/022-add-monk-character-sample.sql
--
-- MyAAC renders the vocation choices from core.character_samples, not from
-- vocations.xml directly. The server already ships Monk as base vocation 9
-- and a "Monk Sample" player, so the AAC must map 9 to that sample.

START TRANSACTION;

DELETE FROM `myaac_settings`
WHERE `name` = 'core'
  AND `key` = 'character_samples';

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
VALUES (
  'core',
  'character_samples',
  '1=Sorcerer Sample
2=Druid Sample
3=Paladin Sample
4=Knight Sample
9=Monk Sample'
);

-- Keep the legacy flat config table aligned on installations that still read it.
INSERT INTO `myaac_config` (`name`, `value`)
VALUES (
  'core.character_samples',
  '1=Sorcerer Sample
2=Druid Sample
3=Paladin Sample
4=Knight Sample
9=Monk Sample'
)
ON DUPLICATE KEY UPDATE
  `value` = VALUES(`value`);

COMMIT;
