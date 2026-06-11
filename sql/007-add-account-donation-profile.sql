-- Adds account profile fields required before future donations.
-- Full name already uses the existing accounts.rlname column.

ALTER TABLE accounts
  ADD COLUMN IF NOT EXISTS birth_date DATE NULL AFTER rlname,
  ADD COLUMN IF NOT EXISTS cpf VARCHAR(14) NOT NULL DEFAULT '' AFTER birth_date;
