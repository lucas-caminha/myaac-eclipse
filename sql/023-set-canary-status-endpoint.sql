-- Set the MyAAC status endpoint for the local Canary process.
-- Apply with: mysql canary < sql/023-set-canary-status-endpoint.sql
--
-- Canary exposes the XML status protocol on statusProtocolPort. On the VPS the
-- website and game server run on the same host, so MyAAC should query loopback
-- instead of hairpinning through the public IP.

START TRANSACTION;

UPDATE `myaac_settings`
SET `value` = 'true'
WHERE `name` = 'core' AND `key` = 'status_enabled';

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
SELECT 'core', 'status_enabled', 'true'
WHERE NOT EXISTS (
  SELECT 1 FROM `myaac_settings`
  WHERE `name` = 'core' AND `key` = 'status_enabled'
);

UPDATE `myaac_settings`
SET `value` = '127.0.0.1'
WHERE `name` = 'core' AND `key` = 'status_ip';

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
SELECT 'core', 'status_ip', '127.0.0.1'
WHERE NOT EXISTS (
  SELECT 1 FROM `myaac_settings`
  WHERE `name` = 'core' AND `key` = 'status_ip'
);

UPDATE `myaac_settings`
SET `value` = '7171'
WHERE `name` = 'core' AND `key` = 'status_port';

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
SELECT 'core', 'status_port', '7171'
WHERE NOT EXISTS (
  SELECT 1 FROM `myaac_settings`
  WHERE `name` = 'core' AND `key` = 'status_port'
);

UPDATE `myaac_settings`
SET `value` = '3.0'
WHERE `name` = 'core' AND `key` = 'status_timeout';

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
SELECT 'core', 'status_timeout', '3.0'
WHERE NOT EXISTS (
  SELECT 1 FROM `myaac_settings`
  WHERE `name` = 'core' AND `key` = 'status_timeout'
);

UPDATE `myaac_settings`
SET `value` = '60'
WHERE `name` = 'core' AND `key` = 'status_interval';

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
SELECT 'core', 'status_interval', '60'
WHERE NOT EXISTS (
  SELECT 1 FROM `myaac_settings`
  WHERE `name` = 'core' AND `key` = 'status_interval'
);

DELETE newer
FROM `myaac_settings` newer
INNER JOIN `myaac_settings` older
  ON older.`name` = newer.`name`
 AND older.`key` = newer.`key`
 AND older.`id` < newer.`id`
WHERE newer.`name` = 'core'
  AND newer.`key` IN (
    'status_enabled',
    'status_ip',
    'status_port',
    'status_timeout',
    'status_interval'
  );

-- Force MyAAC to recalculate the cached status with the endpoint above.
DELETE FROM `myaac_config`
WHERE `name` LIKE 'status_%';

COMMIT;
