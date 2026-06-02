-- Remove unused public menu entries from the Eclipse OT Canary template menu.
-- Apply with: mysql canary < sql/002-clean-eclipse-menu.sql
DELETE FROM myaac_menu
WHERE template = 'canary'
  AND (
    link IN ('news/archive', 'change-log', 'polls', 'bans', 'forum', 'gallery', 'faq')
    OR name IN ('News Archive', 'Changelog', 'Polls', 'Bans', 'Forum', 'Gallery', 'FAQ')
  );
