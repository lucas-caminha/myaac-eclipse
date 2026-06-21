# Migracoes SQL

Este guia documenta os scripts SQL incluidos no projeto e como gerenciar migracoes de banco de dados.

## Scripts Disponiveis

| Arquivo | Descricao |
|---------|-----------|
| `sql/001-eclipse-news.sql` | Atualiza a news de boas-vindas |
| `sql/002-clean-eclipse-menu.sql` | Remove itens desnecessarios do menu |
| `sql/003-add-vip-loyalty-menu.sql` | Adiciona VIP & Loyalty ao menu Biblioteca |
| `sql/004-update-downloads-launcher.sql` | Atualiza Downloads com links do launcher |
| `sql/005-polish-downloads-page.sql` | Melhora visual e instrucoes da pagina Downloads |
| `sql/006-update-downloads-client-15-11.sql` | Atualiza Downloads para o client 15.11 |
| `sql/007-add-account-donation-profile.sql` | Adiciona campos de perfil usados em doacoes futuras |
| `sql/008-add-donation-intents.sql` | Adiciona tabela de intencoes de doacao para futuro Pix |
| `sql/012-add-boosted-sponsorships.sql` | Adiciona pedidos de patrocinio para o proximo boosted |
| `sql/013-add-lgpd-consents-and-requests.sql` | Adiciona consentimentos e solicitacoes LGPD |
| `sql/014-add-privacy-menu.sql` | Adiciona Privacidade e LGPD ao menu Conta |
| `sql/015-add-duo-donations.sql` | Adiciona pedidos e recompensas de donate em dupla |
| `sql/016-add-scheduled-boosted.sql` | Adiciona agendamentos consumidos pelo servidor para o proximo boosted |
| `sql/017-update-otbr-item-images-url.sql` | Atualiza sprites de itens para a base OTBR/Canary |

## Aplicando Migracoes

### Comando Basico

```bash
mysql canary < sql/001-eclipse-news.sql
```

### Com Usuario e Senha

```bash
mysql -u seu_usuario -p canary < sql/001-eclipse-news.sql
```

### Verificar Antes de Aplicar

```bash
# Visualizar conteudo do script
cat sql/001-eclipse-news.sql

# Testar em modo dry-run (apenas parse)
mysql --verbose canary < sql/001-eclipse-news.sql 2>&1 | head -20
```

## Detalhes das Migracoes

### 001-eclipse-news.sql

Atualiza a primeira news do MyAAC com o conteudo de boas-vindas do Eclipse OT:

```sql
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
```

**O que faz:**
- Atualiza o titulo para "Welcome to Eclipse OT"
- Define um resumo curto em `article_text`
- Adiciona HTML formatado com as features do servidor em `body`

### 002-clean-eclipse-menu.sql

Remove itens de menu que nao serao utilizados no Eclipse OT:

```sql
DELETE FROM myaac_menu
WHERE template = 'canary'
  AND (
    link IN ('news/archive', 'change-log', 'polls', 'bans', 'forum', 'gallery', 'faq')
    OR name IN ('News Archive', 'Changelog', 'Polls', 'Bans', 'Forum', 'Gallery', 'FAQ')
  );
```

**Itens removidos:**
- News Archive
- Changelog
- Polls
- Bans
- Forum
- Gallery
- FAQ

### 003-add-vip-loyalty-menu.sql

Adiciona e normaliza o item VIP & Loyalty no menu Biblioteca do template Canary:

```sql
UPDATE myaac_menu
SET category = 5,
    ordering = 0,
    enabled = 1
WHERE template = 'canary'
  AND link = 'vip-loyalty';
```

**O que faz:**
- Insere `VIP & Loyalty` se ainda nao existir
- Move o item para a categoria Biblioteca
- Reordena `Comandos e Informacoes` para ficar abaixo do VIP & Loyalty

### 004-update-downloads-launcher.sql

Atualiza a pagina publica Downloads para apontar aos arquivos oficiais do launcher:

```sql
UPDATE myaac_pages
SET title = 'Baixar Cliente'
WHERE name = 'downloads';
```

**O que faz:**
- Troca o conteudo antigo de download por links para o Eclipse Launcher
- Mantem um link secundario para baixar o cliente completo
- Desativa TinyMCE para preservar o HTML da pagina

### 005-polish-downloads-page.sql

Melhora o conteudo da pagina Downloads:

```sql
UPDATE myaac_pages
SET title = 'Baixar Cliente'
WHERE name = 'downloads';
```

**O que faz:**
- Destaca o launcher como download principal
- Mostra as versoes atuais do client e launcher
- Adiciona uma nota sobre o alerta do Windows/SmartScreen

### 007-add-account-donation-profile.sql

Adiciona campos cadastrais na tabela `accounts` para validacao de doacoes futuras:

```sql
ALTER TABLE accounts
  ADD COLUMN IF NOT EXISTS birth_date DATE NULL AFTER rlname,
  ADD COLUMN IF NOT EXISTS cpf VARCHAR(14) NOT NULL DEFAULT '' AFTER birth_date;
```

**O que faz:**
- Mantem o nome completo no campo existente `accounts.rlname`
- Adiciona `birth_date` para data de nascimento
- Adiciona `cpf` para CPF normalizado
- Depende do override `system/pages/account/change-info.php` para salvar os novos campos

### 008-add-donation-intents.sql

Cria a tabela `eclipse_donation_intents` para registrar intencoes de doacao antes da integracao com Pix:

```sql
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
  PRIMARY KEY (id)
);
```

**O que faz:**
- Registra conta, pacote, valor em centavos, coins e status da intencao
- Reserva campos para QR Code Pix, codigo copia e cola e referencia do gateway
- Mantem snapshot de nome quando necessario, mas novos registros nao devem duplicar CPF em `payer_cpf`
- Nao credita coins automaticamente enquanto a integracao de pagamento estiver pendente

### 012-add-boosted-sponsorships.sql

Cria a tabela `eclipse_boosted_sponsorships` para controlar patrocinio de `boss` e `creature` no proximo server save:

```sql
CREATE TABLE IF NOT EXISTS eclipse_boosted_sponsorships (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  account_id INT(11) UNSIGNED NOT NULL,
  target_type VARCHAR(20) NOT NULL,
  target_monster_id INT(11) UNSIGNED NOT NULL,
  target_name VARCHAR(255) NOT NULL,
  target_category VARCHAR(100) NOT NULL DEFAULT '',
  amount_coins INT UNSIGNED NOT NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'paid',
  scheduled_for_date DATE NOT NULL,
  cooldown_until DATE NOT NULL,
  reservation_expires_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
);
```

**O que faz:**
- Reserva 1 slot de boss e 1 slot de creature por proximo server save
- Registra alvo, custo em Tibia Coins e status da compra
- Impoe cooldown de 10 dias apos a entrada do alvo
- Alimenta o aplicador que atualiza `boosted_boss` e `boosted_creature`

### 015-add-duo-donations.sql

Cria as tabelas usadas pelo fluxo `/duo-donate`:

```sql
CREATE TABLE IF NOT EXISTS eclipse_duo_donation_orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  payer_account_id INT(11) UNSIGNED NOT NULL,
  partner_account_id INT(11) UNSIGNED NOT NULL,
  package_key VARCHAR(50) NOT NULL,
  total_coins INT(11) UNSIGNED NOT NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'pending_partner',
  partner_token CHAR(64) NOT NULL,
  donation_intent_id BIGINT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (id)
);
```

**O que faz:**
- Registra o jogador principal, o parceiro, o pacote, o outfit escolhido e o aceite do parceiro
- Usa `eclipse_donation_intents` para gerar Pix via Mercado Pago depois do aceite
- Divide os Eclipse Coins entre as duas contas quando o webhook confirma o pagamento
- Aplica boost de experiencia de 2 horas nos dois personagens usando `7200` segundos em `players.xpboost_stamina` e o percentual em `players.xpboost_value`
- Registra o outfit escolhido em `eclipse_duo_donation_rewards` com status `pending_server`, para entrega posterior pelo servidor

### 016-add-scheduled-boosted.sql

Cria a tabela `scheduled_boosted`, consumida pelo Canary na proxima rotacao diaria:

```sql
CREATE TABLE IF NOT EXISTS scheduled_boosted (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  type ENUM('creature', 'boss') NOT NULL,
  boostname VARCHAR(255) NOT NULL,
  raceid INT NOT NULL,
  status ENUM('pending', 'applied', 'cancelled') NOT NULL DEFAULT 'pending',
  scheduled_for DATE NOT NULL,
  source_order_id BIGINT UNSIGNED NULL,
  PRIMARY KEY (id)
);
```

**O que faz:**
- Guarda o proximo `boss` ou `creature` escolhido pelo jogador sem alterar o boosted atual
- Permite que o servidor aplique o alvo na proxima rotacao e marque o registro como `applied`
- Mantem vinculo opcional com `eclipse_boosted_sponsorships.source_order_id` para auditoria do pedido pago com Tibia Coins
- A regra de 1 slot por tipo/data continua sendo aplicada pela pagina `/boosted-sponsor` dentro da transacao

### 017-update-otbr-item-images-url.sql

Atualiza `core.item_images_url` para a base de imagens compativel com Canary/OTBR:

```sql
UPDATE myaac_settings
SET value = 'https://item-images.ots.me/latest_otbr/'
WHERE name = 'core'
  AND `key` = 'item_images_url'
  AND value IN ('https://item-images.ots.me/1092/', 'https://item-images.ots.me/1092');
```

**O que faz:**
- Corrige imagens quebradas ou incorretas para itens modernos, como equipamentos de Monk com IDs acima da base 10.92
- Mantem a migration idempotente e nao altera configuracoes customizadas que ja usam outra URL

### 013-add-lgpd-consents-and-requests.sql

Cria tabelas para registrar consentimentos e solicitacoes relacionadas a privacidade:

```sql
CREATE TABLE IF NOT EXISTS eclipse_account_consents (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  account_id INT(11) UNSIGNED NOT NULL,
  consent_type VARCHAR(40) NOT NULL,
  consent_version VARCHAR(40) NOT NULL,
  consented_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS eclipse_privacy_requests (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  account_id INT(11) UNSIGNED NOT NULL,
  request_type ENUM('access','correction','deletion','anonymization','portability','consent_revocation','other') NOT NULL,
  status ENUM('open','in_review','completed','rejected') NOT NULL DEFAULT 'open',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
```

**O que faz:**
- Prepara registro de aceite de termos, privacidade e consentimentos opcionais
- Permite que o painel `/account/privacy` registre solicitacoes LGPD
- Mantem historico minimo para auditoria e atendimento ao titular

### 014-add-privacy-menu.sql

Adiciona a pagina publica de privacidade ao menu lateral de Conta no template Canary:

```sql
INSERT INTO myaac_menu (template, name, link, access, blank, color, category, ordering, enabled)
SELECT 'canary', 'Privacidade e LGPD', 'privacy', 0, 0, '', 2, 4, 1
WHERE NOT EXISTS (...);
```

Tambem reposiciona `rules` e `downloads` para manter a ordem visual:

1. Gerenciar Conta
2. Criar Conta
3. Recuperar Conta
4. Privacidade e LGPD
5. Regras do Servidor
6. Downloads

## Criando Novas Migracoes

### Nomenclatura

Use o padrao `XXX-descricao.sql` onde:
- `XXX` = numero sequencial (001, 002, 003...)
- `descricao` = nome descritivo em kebab-case

Exemplos:
- `003-add-donation-ranks.sql`
- `004-update-highscores-config.sql`

### Estrutura Recomendada

```sql
-- Descricao da migracao
-- Autor: Seu Nome
-- Data: YYYY-MM-DD
-- Aplicar com: mysql canary < sql/XXX-descricao.sql

-- Backup opcional (descomente se necessario)
-- CREATE TABLE myaac_tabela_backup AS SELECT * FROM myaac_tabela;

-- Inicio da migracao
START TRANSACTION;

-- Suas alteracoes aqui
ALTER TABLE myaac_players ADD COLUMN custom_field VARCHAR(255) DEFAULT NULL;

UPDATE myaac_settings 
SET value = 'new_value' 
WHERE name = 'setting_name';

-- Commit
COMMIT;

-- Fim da migracao
```

### Boas Praticas

1. **Sempre teste em ambiente de desenvolvimento primeiro**
2. **Faca backup antes de aplicar em producao**
3. **Use transacoes para multiplas operacoes**
4. **Documente o que cada migracao faz**
5. **Mantenha migracoes idempotentas quando possivel**

## Migracao Idempotente

Uma migracao idempotente pode ser executada multiplas vezes sem causar erros:

```sql
-- Exemplo: Adicionar coluna apenas se nao existir
SET @dbname = 'canary';
SET @tablename = 'myaac_players';
SET @columnname = 'eclipse_rank';

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_SCHEMA = @dbname 
   AND TABLE_NAME = @tablename 
   AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE myaac_players ADD COLUMN eclipse_rank INT DEFAULT 0'
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;
```

## Revertendo Migracoes

Crie scripts de rollback correspondentes:

```sql
-- sql/001-eclipse-news.rollback.sql
UPDATE myaac_news
SET title = 'Welcome to MyAAC',
    article_text = 'Default welcome text',
    body = '<p>Default content</p>'
WHERE id = 1;
```

## Verificando Estado do Banco

### Ver estrutura de uma tabela

```bash
mysql -e "DESCRIBE myaac_news" canary
```

### Ver dados de uma tabela

```bash
mysql -e "SELECT id, title FROM myaac_news LIMIT 5" canary
```

### Ver itens do menu

```bash
mysql -e "SELECT id, name, link, template FROM myaac_menu WHERE template = 'canary'" canary
```

## Tabelas Principais do MyAAC

| Tabela | Descricao |
|--------|-----------|
| `myaac_news` | Noticias do site |
| `myaac_menu` | Itens do menu |
| `myaac_settings` | Configuracoes do sistema |
| `myaac_pages` | Paginas customizadas |
| `myaac_plugins` | Plugins instalados |
| `myaac_account_actions` | Acoes de conta |

## Proximos Passos

- [Operacoes](./operations.md)
- [Seguranca](./security.md)
