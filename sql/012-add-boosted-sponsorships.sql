-- Adds boosted sponsorship orders for the next server save.
-- Apply with: mysql canary < sql/012-add-boosted-sponsorships.sql

CREATE TABLE IF NOT EXISTS eclipse_boosted_sponsorships (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  account_id INT(11) UNSIGNED NOT NULL,
  target_type VARCHAR(20) NOT NULL,
  target_monster_id INT(11) UNSIGNED NOT NULL,
  target_name VARCHAR(255) NOT NULL,
  target_category VARCHAR(100) NOT NULL DEFAULT '',
  amount_coins INT UNSIGNED NOT NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'paid',
  gateway VARCHAR(40) DEFAULT NULL,
  gateway_reference VARCHAR(191) DEFAULT NULL,
  scheduled_for_date DATE NOT NULL,
  cooldown_until DATE NOT NULL,
  reservation_expires_at DATETIME DEFAULT NULL,
  payer_name VARCHAR(255) DEFAULT NULL,
  payer_cpf VARCHAR(14) DEFAULT NULL,
  notes VARCHAR(500) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  confirmed_at DATETIME DEFAULT NULL,
  applied_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY eclipse_boosted_sponsorships_slot_idx (target_type, scheduled_for_date, status),
  KEY eclipse_boosted_sponsorships_target_idx (target_type, target_name, cooldown_until),
  KEY eclipse_boosted_sponsorships_gateway_idx (gateway_reference)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
