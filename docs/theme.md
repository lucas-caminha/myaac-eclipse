# Tema Canary - Personalizacao

O tema Canary e um tema moderno baseado em Bootstrap 4, altamente personalizavel. Este guia explica como modificar e estender o tema para o Eclipse OT.

## Estrutura do Tema

```
theme-canary/themes/canary/
├── bootstrap/              # Framework Bootstrap 4
│   ├── css/               # CSS compilado
│   └── js/                # JavaScript
├── boxes/                  # Widgets laterais
│   ├── templates/         # Templates Twig dos boxes
│   ├── boosted.php
│   ├── discord.php
│   ├── donate.php
│   └── ...
├── css/                    # Estilos adicionais
├── fonts/                  # Fontes (FontAwesome, etc.)
├── images/                 # Imagens do tema
│   ├── account/           # Icones de conta
│   ├── carousel/          # Imagens do slider
│   ├── content/           # Icones de conteudo
│   └── ...
├── config.ini              # Configuracao visual
├── config.php              # Configuracao PHP
├── basic.css               # CSS base do tema
├── basic.js                # JavaScript base
├── arise-overrides.css     # Overrides Eclipse OT
└── *.html.twig             # Templates Twig
```

## Templates Twig

O MyAAC usa Twig como engine de templates. Os principais templates sao:

| Template | Descricao |
|----------|-----------|
| `account.login.html.twig` | Pagina de login |
| `account.create.html.twig` | Criacao de conta |
| `account.management.html.twig` | Gerenciamento de conta |
| `account.characters.create.html.twig` | Criacao de personagem |
| `characters.html.twig` | Visualizacao de personagem |
| `highscores.html.twig` | Ranking |
| `canary.login-box.html.twig` | Box de login lateral |
| `canary.download-box.html.twig` | Box de download |

### Exemplo: Modificando o Template de Login

```twig
{# account.login.html.twig #}
{% extends 'base.html.twig' %}

{% block content %}
<div class="eclipse-login-container">
    <h1>{{ lang.login_to_account }}</h1>
    
    <form method="post" action="{{ site_url }}/?p=account&action=login">
        <div class="form-group">
            <label>{{ lang.account_email }}</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>{{ lang.password }}</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-eclipse">
            {{ lang.login }}
        </button>
    </form>
</div>
{% endblock %}
```

## CSS Customizado

### arise-overrides.css

Este arquivo contem os overrides especificos do Eclipse OT:

```css
/* Variaveis de cores */
:root {
    --eclipse-red: #8b0000;
    --eclipse-dark: #1a1a1a;
    --eclipse-gold: #ffd700;
    --eclipse-text: #d4c0a1;
}

/* Background do site */
body {
    background-image: url('images/arise-red-fortress.png');
    background-size: cover;
    background-attachment: fixed;
}

/* Logo centralizado */
.logo-container {
    text-align: center;
    margin-bottom: 20px;
}

/* Botoes personalizados */
.btn-eclipse {
    background: linear-gradient(180deg, var(--eclipse-red), #5a0000);
    border: 1px solid var(--eclipse-gold);
    color: var(--eclipse-text);
    transition: all 0.3s ease;
}

.btn-eclipse:hover {
    background: linear-gradient(180deg, #a00000, var(--eclipse-red));
    box-shadow: 0 0 10px var(--eclipse-gold);
}

/* Layout expandido */
.main-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* News cards */
.news-card {
    background: rgba(26, 26, 26, 0.9);
    border: 1px solid var(--eclipse-red);
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

/* Menu hover */
.menu-item:hover {
    background: var(--eclipse-red);
    color: var(--eclipse-gold);
}
```

### Adicionando Novos Estilos

1. Crie ou edite `arise-overrides.css`
2. Adicione seus estilos
3. Incremente a versao no carregamento (para cache busting):

```php
// No index.php ou template base
<link rel="stylesheet" href="arise-overrides.css?v=10">
```

## Boxes Laterais

### Criando um Novo Box

1. Crie o arquivo PHP em `boxes/`:

```php
<?php
// boxes/server-status.php

if (!defined('MYAAC')) {
    exit;
}

$online_count = $db->query("SELECT COUNT(*) FROM players_online")->fetchColumn();

echo $twig->render('boxes/templates/server-status.html.twig', [
    'online' => $online_count,
    'max_online' => $config['server_max_online'] ?? 100
]);
```

2. Crie o template Twig em `boxes/templates/`:

```twig
{# boxes/templates/server-status.html.twig #}
<div class="box server-status">
    <div class="box-header">
        <i class="fa fa-server"></i> Server Status
    </div>
    <div class="box-content">
        <p>Players Online: <strong>{{ online }}</strong> / {{ max_online }}</p>
        <div class="progress">
            <div class="progress-bar bg-success" 
                 style="width: {{ (online / max_online * 100)|round }}%">
            </div>
        </div>
    </div>
</div>
```

3. Adicione ao `config.ini`:

```ini
boxes = "donate,boosted,rank,discord,server-status"
```

## Imagens

### Substituindo Imagens

| Imagem | Localizacao | Tamanho Recomendado |
|--------|-------------|---------------------|
| Logo | `images/logo-eclipse-transparent.png` | 300x100 px |
| Background | `images/arise-red-fortress.png` | 1920x1080 px |
| Favicon | `images/favicon.ico` | 32x32 px |
| Carousel | `images/carousel/*.jpg` | 800x400 px |

### Adicionando Novas Imagens

1. Coloque a imagem na pasta apropriada em `images/`
2. Referencie no CSS ou template:

```css
.custom-banner {
    background-image: url('images/custom-banner.png');
}
```

```twig
<img src="{{ template_path }}/images/custom-image.png" alt="Descricao">
```

## JavaScript

### basic.js

Contem funcionalidades interativas do tema:

```javascript
// Exemplo: Menu dropdown animado
$(document).ready(function() {
    $('.menu-category').hover(
        function() {
            $(this).find('.submenu').slideDown(200);
        },
        function() {
            $(this).find('.submenu').slideUp(200);
        }
    );
});
```

### Adicionando JavaScript Personalizado

Adicione ao final de `basic.js`:

```javascript
// Custom Eclipse OT functionality
$(document).ready(function() {
    // Efeito de brilho no logo
    $('.logo-image').hover(function() {
        $(this).addClass('glow-effect');
    }, function() {
        $(this).removeClass('glow-effect');
    });
    
    // Contador de players online em tempo real
    setInterval(function() {
        $.get('/api/online-count', function(data) {
            $('#online-count').text(data.count);
        });
    }, 30000);
});
```

## Botoes

O tema inclui varios templates de botoes em arquivos `buttons.*.html.twig`:

- `buttons.login.html.twig`
- `buttons.logout.html.twig`
- `buttons.submit.html.twig`
- `buttons.back.html.twig`
- `buttons.cancel.html.twig`
- E outros...

### Customizando Botoes

```twig
{# buttons.submit.html.twig #}
<button type="submit" class="btn btn-eclipse btn-lg">
    <i class="fa fa-check"></i> {{ label|default('Submit') }}
</button>
```

## Boas Praticas

1. **Nunca edite arquivos do Bootstrap diretamente** - Use overrides
2. **Use variaveis CSS** - Facilita mudancas globais de cores
3. **Teste em multiplos navegadores** - Chrome, Firefox, Safari
4. **Otimize imagens** - Use ferramentas como TinyPNG
5. **Versione seus CSS/JS** - Para evitar problemas de cache
6. **Faca backup antes de mudancas grandes**

## Proximos Passos

- [Migrações SQL](./sql-migrations.md)
- [Operacoes](./operations.md)
