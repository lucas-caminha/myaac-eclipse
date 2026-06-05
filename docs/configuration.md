# Configuracao

Este guia detalha todas as opcoes de configuracao disponiveis no MyAAC Eclipse OT.

## Arquivos de Configuracao

O tema possui dois arquivos principais de configuracao:

| Arquivo | Proposito |
|---------|-----------|
| `config.ini` | Configuracoes visuais basicas (cores, boxes, imagens) |
| `config.php` | Configuracoes avancadas PHP (menus, carousel, social) |

## config.ini

Localizado em `theme-canary/themes/canary/config.ini`:

```ini
; Cores das bordas
darkborder = "#D4C0A1"
lightborder = "#F1E0C6"
vdarkborder = "#505050"

; Boxes laterais ativos
; Opcoes: newcomer, gallery, premium, poll, highscores, networks, 
;         discord, donate, searchchar, rashid, boosted, rank
boxes = "donate,boosted,rank,discord"

; Imagem de fundo
background_image = "arise-red-fortress.png"

; Logo do site
logo_image = "logo-eclipse-transparent.png"

; ID da imagem da galeria (do banco de dados)
gallery_image_id_from_database = 1
```

### Boxes Disponiveis

| Box | Descricao |
|-----|-----------|
| `newcomer` | Lista de novos jogadores |
| `gallery` | Galeria de imagens |
| `premium` | Informacoes premium |
| `poll` | Enquetes |
| `highscores` | Rankings |
| `networks` | Redes sociais |
| `discord` | Widget Discord |
| `donate` | Botao de doacao |
| `searchchar` | Busca de personagem |
| `rashid` | Localizacao do NPC Rashid |
| `boosted` | Criatura/Boss boosted |
| `rank` | Ranking de jogadores |

## config.php

Localizado em `theme-canary/themes/canary/config.php`:

```php
<?php
// Cor padrao dos links do menu
$config['menu_default_links_color'] = '#ffffff';

// Horario do server save
$config['server_save'] = '05:00:00';

// Animacao do menu
$config['allow_menu_animated'] = true;

// Logo principal
$config['logo_image'] = 'logo-eclipse-transparent.png';
```

### Barra de Status e Redes Sociais

```php
// Habilitar barra de status
$config['status_bar'] = true;

// Links das redes sociais (deixe vazio para ocultar)
$config['discord_link'] = 'https://discord.gg/seu-servidor';
$config['whatsapp_link'] = '';
$config['instagram_link'] = '';
$config['facebook_link'] = '';
$config['x_link'] = '';

// Colapsar barra de status
$config['collapse_status'] = true;
```

### Carousel (Slider de Imagens)

```php
// Habilitar carousel
$config['carousel_status'] = true;

// Imagens do carousel (em images/carousel/)
$config['carousel'] = [
    'carousel_1' => 'runemaster_small.jpg',
    'carousel_2' => 'merrygarb_small.jpg',
    'carousel_3' => 'mothcape_small.jpg',
];
```

### Banner Promocional

```php
// Habilitar banner
$config['banner_status'] = false;

// Imagem do banner (em images/carousel/)
$config['banner_image'] = '500x660.png';

// Link do banner
$config['banner_link'] = '#';
```

### Categorias do Menu

```php
$config['menu_categories'] = [
    MENU_CATEGORY_NEWS       => ['id' => 'news',           'name' => 'Latest News'],
    MENU_CATEGORY_ACCOUNT    => ['id' => 'account',        'name' => 'Account'],
    MENU_CATEGORY_COMMUNITY  => ['id' => 'community',      'name' => 'Community'],
    MENU_CATEGORY_LIBRARY    => ['id' => 'library',        'name' => 'Library'],
    7 => ['id' => 'charactertrade', 'name' => 'Char Bazaar'],
    MENU_CATEGORY_SHOP       => ['id' => 'shops',          'name' => 'Shop'],
];

// Itens do menu (carregados de menus.php)
$config['menus'] = require __DIR__ . '/menus.php';
```

## config.local.php (MyAAC)

Arquivo de configuracao principal do MyAAC em `/var/www/html/config.local.php`:

```php
<?php
return [
    // Conexao com banco de dados
    'database_host' => '127.0.0.1',
    'database_port' => 3306,
    'database_name' => 'canary',
    'database_user' => 'seu_usuario',
    'database_password' => 'sua_senha',
    
    // IP do servidor de jogo
    'server_ip' => '123.456.789.0',
    
    // Template ativo
    'template' => 'canary',
];
```

**ATENCAO**: Este arquivo contem credenciais sensiveis e nunca deve ser commitado!

## Configuracoes via CLI

O MyAAC oferece comandos CLI para gerenciar configuracoes:

```bash
cd /var/www/html

# Ver valor de uma configuracao
php8.2 aac settings:get core.template

# Definir valor
php8.2 aac settings:set core.template canary

# Listar todas as configuracoes
php8.2 aac settings:list
```

### Configuracoes Comuns

```bash
# Template padrao
php8.2 aac settings:set core.template canary

# Impedir troca de template
php8.2 aac settings:set core.template_allow_change false

# Nome do servidor
php8.2 aac settings:set core.server_name "Eclipse OT"

# URL do site
php8.2 aac settings:set core.site_url "https://seu-dominio.com"
```

## Personalizando Cores

Edite `arise-overrides.css` para customizar cores:

```css
:root {
    --eclipse-primary: #8b0000;    /* Vermelho escuro */
    --eclipse-secondary: #1a1a1a;  /* Preto */
    --eclipse-accent: #ffd700;     /* Dourado */
    --eclipse-text: #ffffff;       /* Branco */
}
```

## Proximos Passos

- [Personalizacao do Tema](./theme.md)
- [Migrações SQL](./sql-migrations.md)
