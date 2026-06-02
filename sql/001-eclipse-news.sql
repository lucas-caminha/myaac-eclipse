-- Eclipse OT initial MyAAC news/content.
-- Apply with: mysql canary < sql/001-eclipse-news.sql
UPDATE myaac_news
SET title = 'Welcome to Eclipse OT',
    article_text = 'A dark custom PvP world built around boss gates, guild rivalry and long-term progression.',
    body = '<div class="arise-news-intro">
  <h1>Welcome to Eclipse OT</h1>
  <p class="lead">A custom PvP world forged in shadow, boss gates, guild rivalry and long-term character progression.</p>
  <div class="arise-feature-grid">
    <div><strong>Eclipse Gates</strong><span>Boss access organized by tiers, with clear goals from early game to endgame.</span></div>
    <div><strong>Brazilian PvP</strong><span>Fast access, active war potential and rules tuned for competitive play.</span></div>
    <div><strong>Daily Objectives</strong><span>Daily bosses, tasks and rewards planned for the closed beta.</span></div>
    <div><strong>Long-Term Economy</strong><span>Rates are accelerated, but rare rewards and boss drops are designed to last.</span></div>
  </div>
</div>'
WHERE id = 1;
