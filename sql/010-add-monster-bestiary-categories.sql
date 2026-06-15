-- Eclipse OT: store the Bestiary class read from Canary monster scripts.
-- Run once after deploying the categorized monster plugin.

ALTER TABLE `myaac_lua_monsters`
	ADD COLUMN IF NOT EXISTS `bestiary_class` VARCHAR(100) NOT NULL DEFAULT '' AFTER `health`;
