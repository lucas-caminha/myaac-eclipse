-- Eclipse OT initial MyAAC news/content.
-- Apply with: mysql canary < sql/001-eclipse-news.sql
UPDATE myaac_news
SET title = 'Welcome to Eclipse OT',
    article_text = 'Servidor brasileiro focado em progressao de longo prazo, estabilidade, sistemas custom e comunidade ativa.',
    body = '<div class="arise-news-intro arise-news-launch">
  <h1>Welcome to Eclipse OT</h1>
  <p class="lead">O Eclipse OT nasce para quem quer evoluir de verdade: progress&atilde;o est&aacute;vel, PvP competitivo, economia duradoura, sistemas custom e uma comunidade acompanhada de perto pela equipe.</p>

  <ul class="arise-news-bulletlist">
    <li>Servidor brasileiro focado em progress&atilde;o de longo prazo, estabilidade e comunidade.</li>
    <li>Voca&ccedil;&atilde;o Monk, Task Board, Proficiency e sistemas exclusivos em evolu&ccedil;&atilde;o constante.</li>
    <li>EXP em stages: come&ccedil;o acelerado para entrar no jogo e endgame progressivo para durar.</li>
    <li>Loot 4x e Bestiary 4x para hunts mais din&acirc;micas sem quebrar a economia.</li>
    <li>B&ocirc;nus de party custom para at&eacute; 7 jogadores, incentivando hunt em grupo e organiza&ccedil;&atilde;o.</li>
    <li>Eventos mensais, boosted boss/creature, highscores, bazar, wiki e tutoriais ativos.</li>
  </ul>

  <div class="arise-news-rate-strip">
    <div><strong>EXP</strong><span>Stages progressivos</span></div>
    <div><strong>Loot 4x</strong><span>Economia controlada</span></div>
    <div><strong>Bestiary 4x</strong><span>Progresso mais fluido</span></div>
  </div>

  <div class="arise-feature-grid">
    <div><strong>Competitivo, mas justo</strong><span>Ambiente PvP ativo, balanceado e sem vantagens abusivas por pagamento.</span></div>
    <div><strong>Conte&uacute;do para durar</strong><span>Bosses, tasks, charms, highscores e progress&atilde;o pensados para manter objetivos no endgame.</span></div>
    <div><strong>Site completo</strong><span>Tema claro/escuro, PT/EN/ES, rankings, mercado de personagens, VIP & Loyalty e informa&ccedil;&otilde;es claras.</span></div>
    <div><strong>Comunidade no centro</strong><span>Discord ativo, tutoriais, suporte e melhorias constantes baseadas no feedback dos jogadores.</span></div>
  </div>

  <div class="arise-news-linkbar">
    <a href="/index.php/ots-info">Sobre o servidor</a>
    <a href="/index.php/server-guide">Guia inicial</a>
    <a href="/index.php/spells">Magias e tutoriais</a>
    <a href="https://discord.gg/nmx5V5jpkR" target="_blank" rel="noopener">Discord oficial</a>
  </div>

  <div class="arise-news-callout">
    <strong>Crie sua conta, escolha sua voca&ccedil;&atilde;o e prepare-se para a guerra.</strong>
    <span>O Eclipse OT combina hunt, PvP, bosses, party play e progresso constante em um servidor feito para crescer junto com os players.</span>
  </div>
</div>'
WHERE id = 1;
