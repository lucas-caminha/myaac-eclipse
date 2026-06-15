-- Eclipse OT: plugin tables and public menu entries.
-- Safe to run more than once.

CREATE TABLE IF NOT EXISTS `myaac_lua_spells` (`id` INT NOT NULL AUTO_INCREMENT,`name` VARCHAR(255) NOT NULL,`words` VARCHAR(255) NOT NULL DEFAULT '',`group` VARCHAR(255) NOT NULL DEFAULT '',`type` TINYINT NOT NULL DEFAULT 0,`level` INT NOT NULL DEFAULT 0,`maglevel` INT NOT NULL DEFAULT 0,`mana` INT NOT NULL DEFAULT 0,`soul` TINYINT NOT NULL DEFAULT 0,`conjure_id` INT NOT NULL DEFAULT 0,`conjure_count` TINYINT NOT NULL DEFAULT 0,`reagent` INT NOT NULL DEFAULT 0,`rune_id` INT NOT NULL DEFAULT 0,`premium` TINYINT NOT NULL DEFAULT 0,`vocations` VARCHAR(100) NOT NULL DEFAULT '',`hide` TINYINT NOT NULL DEFAULT 0,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `myaac_lua_monsters` (`id` INT NOT NULL AUTO_INCREMENT,`name` VARCHAR(255) NOT NULL,`mana` INT NOT NULL DEFAULT 0,`exp` INT NOT NULL DEFAULT 0,`health` INT NOT NULL DEFAULT 0,`bestiary_class` VARCHAR(100) NOT NULL DEFAULT '',`outfit` VARCHAR(255) NOT NULL DEFAULT '',`speed_lvl` INT NOT NULL DEFAULT 1,`use_haste` TINYINT NOT NULL DEFAULT 0,`summonable` TINYINT NOT NULL DEFAULT 0,`convinceable` TINYINT NOT NULL DEFAULT 0,`rewardboss` TINYINT NOT NULL DEFAULT 0,`voices` VARCHAR(3000) NOT NULL DEFAULT '',`immunities` VARCHAR(255) NOT NULL DEFAULT '',`elements` VARCHAR(1000) NOT NULL DEFAULT '',`defense` INT NOT NULL DEFAULT 0,`armor` INT NOT NULL DEFAULT 0,`race` VARCHAR(255) NOT NULL DEFAULT '',`loot` VARCHAR(5000) NOT NULL DEFAULT '',`summons` VARCHAR(1000) NOT NULL DEFAULT '',`flags` VARCHAR(1000) NOT NULL DEFAULT '',`hide` TINYINT NOT NULL DEFAULT 0,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS `myaac_character_offers` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,`player_id` INT NOT NULL,`seller_account_id` INT UNSIGNED NOT NULL,`price` INT UNSIGNED NOT NULL,`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`),UNIQUE KEY `character_offer_player` (`player_id`),KEY `character_offer_seller` (`seller_account_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `myaac_menu` (`template`,`name`,`link`,`access`,`blank`,`color`,`category`,`ordering`,`enabled`)
SELECT 'canary','Character Market','character-sale',0,0,'',3,1,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `myaac_menu` WHERE `template`='canary' AND `link`='character-sale');

INSERT INTO `myaac_menu` (`template`,`name`,`link`,`access`,`blank`,`color`,`category`,`ordering`,`enabled`)
SELECT 'canary','Monsters','monsters',0,0,'',5,2,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `myaac_menu` WHERE `template`='canary' AND `link`='monsters');
INSERT INTO `myaac_menu` (`template`,`name`,`link`,`access`,`blank`,`color`,`category`,`ordering`,`enabled`)
SELECT 'canary','Spells','spells',0,0,'',5,3,1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `myaac_menu` WHERE `template`='canary' AND `link`='spells');
