# Changelog

Historico de alteracoes do projeto MyAAC Eclipse OT.

O formato e baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/).

---

## [Unreleased]

### Adicionado
- Documentacao completa do projeto
- Override de `/highscores` com suporte a categorias extras dinamicas quando as colunas existirem no banco, incluindo Charm Points, Loyalty Points, Achievement Points, Bosstiary Points, Task Points e Prey Wildcards

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
