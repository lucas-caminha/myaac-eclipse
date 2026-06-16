# Changelog

Historico de alteracoes do projeto MyAAC Eclipse OT.

O formato e baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/).

---

## [Unreleased]

### Adicionado
- Página de detalhes para personagens anunciados no mercado, com atributos, progressão, dados de combate, skills, equipamentos e histórico recente
- Página `/bosses` separada por classes Bane, Archfoe e Nemesis do Bosstiary
- Biblioteca pública de monstros e magias carregada dos dados do Canary
- Mercado transacional de personagens com pagamento em coins
- Ranking de guildas poderosas na página de notícias
- Migration `sql/009-add-game-library-plugins.sql` e documentação de plugins
- Documentacao completa do projeto
- Override de `/highscores` com suporte a categorias extras dinamicas, incluindo Bestiary/Charm Points, Loyalty Points, Achievement Points, Bosstiary/Boss Points, Task Points e Prey Wildcards
- Fundos por vocacao e identificacao VIP nos personagens da box lateral de Highscores
- Interface em portugues para `/account/lost`, incluindo o estado de recuperacao por e-mail indisponivel

### Alterado
- Busca de monstros e filtro de magias com funcionamento e contraste corrigidos
- Categorias de monstros e bosses com os ícones do projeto Tibia Monk
- Página de informações com dados do servidor mais compactos e rates em gráficos proporcionais
- Posicionamento do percentual de drop nos cards de loot do Bestiary
- Bestiário de monstros organizado por classes, carregadas dos scripts Lua do Canary
- Aba de runas simplificada sem imagens de itens
- Alinhamento e dimensoes dos sprites de boss e creature na box Boosted
- Contraste e organizacao visual do fluxo de recuperacao de conta

---

## [1.1.0] - 2026-06-02

### Adicionado
- SQL migration `sql/002-clean-eclipse-menu.sql` para limpeza do menu
- Override de tema para `news.html.twig` para ocultar links de comentarios do forum

### Alterado
- Melhorado comportamento de hover do submenu para evitar artefatos de background JavaScript

### Removido
- Itens do menu publico: News Archive, Changelog, Polls, Bans, Forum, Gallery e FAQ

---

## [1.0.0] - 2026-06-02

### Adicionado
- Identidade visual Eclipse OT (rebrand de Arise OT)
- Background dark fantasy vermelho/preto
- Logo transparente Eclipse OT
- Favicon e touch icon personalizados
- Camada CSS customizada (`arise-overrides.css`)
- Conteudo inicial de news MyAAC

### Alterado
- Largura do layout da pagina expandida para monitores grandes
- Logo centralizado acima do box de login

---

## [0.1.0] - 2026-05-25

### Adicionado
- Instalacao inicial do MyAAC no VPS
- Tema Canary instalado e habilitado
- Correcao do asset jQuery faltando para interacoes do menu

### Alterado
- Template switching ocultado
- Canary definido como template padrao

---

## Como Atualizar Este Changelog

Ao fazer alteracoes no projeto, adicione uma entrada na secao `[Unreleased]` seguindo este formato:

```markdown
### Adicionado
- Nova feature ou arquivo

### Alterado
- Mudancas em funcionalidades existentes

### Corrigido
- Correcoes de bugs

### Removido
- Features ou arquivos removidos

### Seguranca
- Correcoes de vulnerabilidades
```

Quando uma nova versao for lancada, mova os itens de `[Unreleased]` para uma nova secao com a versao e data.
