CREATE TABLE IF NOT EXISTS `myaac_character_offers` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`player_id` INT NOT NULL,
	`seller_account_id` INT UNSIGNED NOT NULL,
	`price` INT UNSIGNED NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE KEY `character_offer_player` (`player_id`),
	KEY `character_offer_seller` (`seller_account_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;
