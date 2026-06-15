-- Eclipse OT: split Bestiary creatures and Bosstiary bosses.
-- Safe to run more than once.

ALTER TABLE `myaac_lua_monsters`
	ADD COLUMN IF NOT EXISTS `bosstiary_class` VARCHAR(20) NOT NULL DEFAULT '' AFTER `bestiary_class`;

INSERT INTO `myaac_menu` (`template`,`name`,`link`,`access`,`blank`,`color`,`category`,`ordering`,`enabled`)
SELECT 'canary','Bosses','bosses',0,0,'',5,3,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `myaac_menu` WHERE `template`='canary' AND `link`='bosses');

UPDATE `myaac_menu`
SET `ordering` = 4
WHERE `template` = 'canary' AND `link` = 'spells';
