-- Update the public Downloads page for the Eclipse OT Tibia 15.11 client package.
-- Apply with: mysql canary < sql/006-update-downloads-client-15-11.sql
UPDATE myaac_pages
SET
  body = REPLACE(
    REPLACE(body, '15.00.249ccc-r2', '15.11.c9d1cf-r1'),
    'eclipse-client-15.00.249ccc.zip',
    'eclipse-client-15.11.c9d1cf.zip'
  )
WHERE name = 'downloads';
