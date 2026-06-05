# Migracoes SQL

Este guia documenta os scripts SQL incluidos no projeto e como gerenciar migracoes de banco de dados.

## Scripts Disponiveis

| Arquivo | Descricao |
|---------|-----------|
| `sql/001-eclipse-news.sql` | Atualiza a news de boas-vindas |
| `sql/002-clean-eclipse-menu.sql` | Remove itens desnecessarios do menu |

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
