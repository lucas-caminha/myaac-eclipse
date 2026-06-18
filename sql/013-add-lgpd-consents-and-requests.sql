-- Adds LGPD consent and privacy request tables for Eclipse OT.
-- Apply with: mysql canary < sql/013-add-lgpd-consents-and-requests.sql

CREATE TABLE IF NOT EXISTS eclipse_account_consents (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  account_id INT(11) UNSIGNED NOT NULL,
  consent_type VARCHAR(40) NOT NULL,
  consent_version VARCHAR(40) NOT NULL,
  consented_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip_address VARBINARY(16) DEFAULT NULL,
  user_agent VARCHAR(255) DEFAULT NULL,
  revoked_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY eclipse_account_consents_account_idx (account_id),
  KEY eclipse_account_consents_type_idx (consent_type, consent_version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS eclipse_privacy_requests (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  account_id INT(11) UNSIGNED NOT NULL,
  request_type ENUM('access','correction','deletion','anonymization','portability','consent_revocation','other') NOT NULL,
  status ENUM('open','in_review','completed','rejected') NOT NULL DEFAULT 'open',
  message TEXT NULL,
  response TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  completed_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY eclipse_privacy_requests_account_idx (account_id),
  KEY eclipse_privacy_requests_status_idx (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

