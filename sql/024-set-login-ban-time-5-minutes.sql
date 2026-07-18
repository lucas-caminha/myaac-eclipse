-- Reduce MyAAC failed-login lockout from 30 minutes to 5 minutes.
-- Apply with: mysql canary < sql/024-set-login-ban-time-5-minutes.sql

START TRANSACTION;

UPDATE `myaac_settings`
SET `value` = '5'
WHERE `name` = 'core' AND `key` = 'account_login_ban_time';

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
SELECT 'core', 'account_login_ban_time', '5'
WHERE NOT EXISTS (
  SELECT 1 FROM `myaac_settings`
  WHERE `name` = 'core' AND `key` = 'account_login_ban_time'
);

DELETE newer
FROM `myaac_settings` newer
INNER JOIN `myaac_settings` older
  ON older.`name` = newer.`name`
 AND older.`key` = newer.`key`
 AND older.`id` < newer.`id`
WHERE newer.`name` = 'core'
  AND newer.`key` = 'account_login_ban_time';

COMMIT;
