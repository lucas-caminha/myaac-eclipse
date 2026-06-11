-- Adds a pending donation intent table for the future Pix donation flow.
-- Apply with: mysql canary < sql/008-add-donation-intents.sql

CREATE TABLE IF NOT EXISTS eclipse_donation_intents (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  account_id INT(11) UNSIGNED NOT NULL,
  package_key VARCHAR(50) NOT NULL,
  amount_brl_cents INT UNSIGNED NOT NULL,
  coins INT UNSIGNED NOT NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'pending_gateway',
  gateway VARCHAR(40) DEFAULT NULL,
  gateway_reference VARCHAR(191) DEFAULT NULL,
  pix_qr_code TEXT DEFAULT NULL,
  pix_copy_paste TEXT DEFAULT NULL,
  payer_name VARCHAR(255) DEFAULT NULL,
  payer_cpf VARCHAR(14) DEFAULT NULL,
  notes VARCHAR(500) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  confirmed_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY eclipse_donation_intents_account_id_idx (account_id),
  KEY eclipse_donation_intents_status_idx (status),
  KEY eclipse_donation_intents_gateway_reference_idx (gateway_reference)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
