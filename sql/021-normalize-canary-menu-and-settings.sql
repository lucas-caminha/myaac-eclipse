-- Normalize the Eclipse OT Canary menu and required site settings.
-- Apply with: mysql canary < sql/021-normalize-canary-menu-and-settings.sql
--
-- This migration captures the production fixes applied during the VPS install:
-- - the complete left menu for the Canary template;
-- - Canary as the locked default template;
-- - gifts/shop enabled so the shop menu category is rendered.

START TRANSACTION;

DELETE FROM `myaac_menu`
WHERE `template` = 'canary';

INSERT INTO `myaac_menu` (`template`, `name`, `link`, `access`, `blank`, `color`, `category`, `ordering`, `enabled`) VALUES
('canary', 'Últimas Notícias', 'news', 0, 0, '', 1, 1, 1),
('canary', 'Agenda de Eventos', 'event-schedule', 0, 0, '', 1, 2, 1),

('canary', 'Gerenciar Conta', 'account/manage', 0, 0, '', 2, 1, 1),
('canary', 'Criar Conta', 'account/create', 0, 0, '', 2, 2, 1),
('canary', 'Recuperar Conta', 'account/lost', 0, 0, '', 2, 3, 1),
('canary', 'Privacidade e LGPD', 'privacy', 0, 0, '', 2, 4, 1),
('canary', 'Regras do Servidor', 'rules', 0, 0, '', 2, 5, 1),
('canary', 'Downloads', 'downloads', 0, 0, '', 2, 6, 1),

('canary', 'Personagens', 'characters', 0, 0, '', 3, 1, 1),
('canary', 'Mercado de Personagens', 'character-sale', 0, 0, '', 3, 2, 1),
('canary', 'Quem Está Online?', 'online', 0, 0, '', 3, 3, 1),
('canary', 'Highscores', 'highscores', 0, 0, '', 3, 4, 1),
('canary', 'Últimas Mortes', 'last-kills', 0, 0, '', 3, 5, 1),
('canary', 'Casas', 'houses', 0, 0, '', 3, 6, 1),
('canary', 'Guildas', 'guilds', 0, 0, '', 3, 7, 1),
('canary', 'Equipe de Suporte', 'team', 0, 0, '', 3, 8, 1),

('canary', 'VIP & Loyalty', 'vip-loyalty', 0, 0, '', 5, 1, 1),
('canary', 'Monstros', 'monsters', 0, 0, '', 5, 2, 1),
('canary', 'Bosses', 'bosses', 0, 0, '', 5, 3, 1),
('canary', 'Magias', 'spells', 0, 0, '', 5, 4, 1),
('canary', 'Comandos e Informações', 'ots-info', 0, 0, '', 5, 5, 1),

('canary', 'Comprar Points', 'points', 0, 0, '', 6, 1, 1),
('canary', 'Patrocinar Boosted', 'boosted-sponsor', 0, 0, '', 6, 2, 1);

INSERT INTO `myaac_config` (`name`, `value`) VALUES
('template', 'canary'),
('core.template', 'canary'),
('template_allow_change', '0'),
('core.template_allow_change', '0'),
('gifts_system', '1'),
('core.gifts_system', '1')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

UPDATE `myaac_settings`
SET `value` = 'canary'
WHERE (`name` = 'core' AND `key` = 'template')
   OR (`name` = 'template' AND `key` = '');

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
SELECT 'core', 'template', 'canary'
WHERE NOT EXISTS (
  SELECT 1 FROM `myaac_settings`
  WHERE `name` = 'core' AND `key` = 'template'
);

UPDATE `myaac_settings`
SET `value` = '0'
WHERE (`name` = 'core' AND `key` = 'template_allow_change')
   OR (`name` = 'template_allow_change' AND `key` = '');

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
SELECT 'core', 'template_allow_change', '0'
WHERE NOT EXISTS (
  SELECT 1 FROM `myaac_settings`
  WHERE `name` = 'core' AND `key` = 'template_allow_change'
);

UPDATE `myaac_settings`
SET `value` = '1'
WHERE (`name` = 'core' AND `key` = 'gifts_system')
   OR (`name` = 'gifts_system' AND `key` = '');

INSERT INTO `myaac_settings` (`name`, `key`, `value`)
SELECT 'core', 'gifts_system', '1'
WHERE NOT EXISTS (
  SELECT 1 FROM `myaac_settings`
  WHERE `name` = 'core' AND `key` = 'gifts_system'
);

COMMIT;
