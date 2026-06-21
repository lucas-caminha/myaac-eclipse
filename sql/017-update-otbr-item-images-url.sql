-- Use the OTBR/Canary item image set instead of the legacy Tibia 10.92 sprite set.
-- The old 1092 path does not contain modern Canary item IDs such as monk equipment.

UPDATE `myaac_settings`
SET `value` = 'https://item-images.ots.me/latest_otbr/'
WHERE `name` = 'core'
  AND `key` = 'item_images_url'
  AND `value` IN ('https://item-images.ots.me/1092/', 'https://item-images.ots.me/1092');

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
SELECT 'core', 'item_images_url', 'https://item-images.ots.me/latest_otbr/'
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1
  FROM `myaac_settings`
  WHERE `name` = 'core'
    AND `key` = 'item_images_url'
);
