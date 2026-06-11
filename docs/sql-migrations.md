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
- Mantem snapshot de nome e CPF para conferencia futura
- Nao credita coins automaticamente enquanto a integracao de pagamento estiver pendente

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
