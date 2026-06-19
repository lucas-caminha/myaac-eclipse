-- Adds scheduled boosted slots consumed by the Canary server on the next rotation.
-- Apply with: mysql canary < sql/016-add-scheduled-boosted.sql

CREATE TABLE IF NOT EXISTS scheduled_boosted (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  type ENUM('creature', 'boss') NOT NULL,
  boostname VARCHAR(255) NOT NULL,
  raceid INT NOT NULL,
  player_id INT NULL,
  account_id INT UNSIGNED NULL,
  status ENUM('pending', 'applied', 'cancelled') NOT NULL DEFAULT 'pending',
  scheduled_for DATE NOT NULL,
  source_order_id BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  applied_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY scheduled_boosted_lookup_idx (type, scheduled_for, status),
  KEY scheduled_boosted_account_idx (account_id),
  KEY scheduled_boosted_order_idx (source_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
