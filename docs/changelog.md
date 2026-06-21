# Changelog

Historico de alteracoes do projeto MyAAC Eclipse OT.

O formato e baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/).

---

## [Unreleased]

### Adicionado
- Pagina publica de Privacidade/LGPD e painel `/account/privacy` para consulta e solicitacoes do titular
- Migration `sql/013-add-lgpd-consents-and-requests.sql` com tabelas de consentimentos e solicitacoes LGPD
- Documentos `docs/lgpd-data-map.md` e `docs/lgpd-incident-response.md`
- Plugin `lgpd-consent` para impedir criacao de conta sem aceite da Politica de Privacidade
- Fluxo `/boosted-sponsor` para patrocinar o boss ou creature do proximo server save com Tibia Coins
- Migration `sql/012-add-boosted-sponsorships.sql` com controle de slot, cooldown e historico de patrocinio
- Migration `sql/016-add-scheduled-boosted.sql` para agendar o proximo boosted consumido pelo Canary na rotacao diaria
- Migration `sql/017-update-otbr-item-images-url.sql` para corrigir sprites de equipamentos modernos no site
- Fluxo `/duo-donate` para donate em dupla com convite, aceite do parceiro, escolha de outfit em modal, Pix Mercado Pago e boost de 2 horas
- Migration `sql/015-add-duo-donations.sql` com pedidos, tokens de aceite e recompensas de donate em dupla
- Aviso no topo de `/account/manage` para aceitar ou recusar convites pendentes de donate em dupla
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
- Imagens de itens passam a usar a base OTBR/Canary `latest_otbr`, corrigindo sprites quebrados ou incorretos em equipamentos modernos
- Cadastro passa a exibir aceite obrigatorio da Politica de Privacidade
- CPF passa a ser exibido mascarado no fluxo de atualizacao cadastral
- Novas intencoes de doacao deixam de duplicar CPF no campo `payer_cpf`
- Webhook Mercado Pago passa a dividir coins, aplicar boost e registrar recompensa de outfit para pedidos de donate em dupla
- `/boosted-sponsor` passa a gravar `scheduled_boosted` em vez de alterar o boosted atual no momento da compra
- Novo item da Loja para acesso ao patrocinio de boosted
- Busca de monstros e filtro de magias com funcionamento e contraste corrigidos
- Categorias de monstros e bosses com os ícones do projeto Tibia Monk
- Página de informações com dados do servidor mais compactos e rates em gráficos proporcionais
- Posicionamento do percentual de drop nos cards de loot do Bestiary
- Bestiário de monstros organizado por classes, carregadas dos scripts Lua do Canary
- Aba de runas simplificada sem imagens de itens
- Alinhamento e dimensoes dos sprites de boss e creature na box Boosted
- Contraste e organizacao visual do fluxo de recuperacao de conta
- Documentacao operacional expandida com a configuracao de dominio, HTTPS, Certbot, `site_url` e troubleshooting de emissao SSL

### Infraestrutura
- Dominio de producao definido como `eclipseot.com.br`
- Alias publico `www.eclipseot.com.br` apontando para o dominio principal
- Nginx de producao configurado com `server_name eclipseot.com.br www.eclipseot.com.br`
- Certificado Let's Encrypt emitido e instalado para os dois hosts
- Redirecionamento automatico de `http` para `https` habilitado
- `config.local.php` de producao atualizado para usar `https://eclipseot.com.br/`
 
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
