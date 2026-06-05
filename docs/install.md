# Guia de Instalacao

Este guia descreve como instalar o MyAAC Eclipse OT em cima de uma instalacao existente do MyAAC com o plugin do tema Canary.

## Pre-requisitos

Antes de comecar, certifique-se de ter:

- Ubuntu 22.04+ (ou distribuicao compativel)
- Nginx configurado e rodando
- PHP 8.2-FPM instalado
- MariaDB/MySQL instalado
- MyAAC instalado em `/var/www/html`
- Plugin Canary Theme disponivel

Consulte [Requisitos do Sistema](./requirements.md) para detalhes.

## Passo 1: Clonar o Repositorio

```bash
# Criar diretorio para repositorios (se nao existir)
sudo mkdir -p /opt/repos
cd /opt/repos

# Clonar o repositorio
sudo git clone https://github.com/lucas-caminha/myaac-eclipse.git
cd myaac-eclipse
```

## Passo 2: Deploy do Tema

Copie os arquivos do tema para o diretorio de plugins do MyAAC:

```bash
# Sincronizar arquivos do tema
sudo rsync -av theme-canary/themes/canary/ /var/www/html/plugins/theme-canary/themes/canary/

# Ajustar permissoes
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/themes/canary

# Limpar cache do MyAAC
sudo find /var/www/html/system/cache -type f -delete
```

### Explicacao dos Comandos

| Comando | Descricao |
|---------|-----------|
| `rsync -av` | Copia arquivos preservando permissoes e mostrando progresso |
| `chown -R www-data` | Define o usuario do servidor web como dono |
| `find ... -delete` | Remove arquivos de cache para forcar recompilacao |

## Passo 3: Aplicar Conteudo SQL

### News de Boas-vindas

```bash
mysql canary < sql/001-eclipse-news.sql
```

### Limpeza do Menu

Remove itens desnecessarios do menu (FAQ, Forum, Gallery, etc.):

```bash
mysql canary < sql/002-clean-eclipse-menu.sql
```

### Limpar Cache Novamente

```bash
sudo find /var/www/html/system/cache -type f -delete
```

## Passo 4: Configurar Template Padrao

Defina o Canary como template padrao e desabilite a troca:

```bash
cd /var/www/html

# Definir template padrao
php8.2 aac settings:set core.template canary

# Impedir troca de template pelos usuarios
php8.2 aac settings:set core.template_allow_change false
```

## Passo 5: Configurar Nginx

Copie o exemplo de configuracao e ajuste conforme necessario:

```bash
# Copiar exemplo
sudo cp /opt/repos/myaac-eclipse/nginx/myaac-eclipse.conf.example /etc/nginx/sites-available/eclipse-ot.conf

# Editar configuracao
sudo nano /etc/nginx/sites-available/eclipse-ot.conf
```

Ajuste o `server_name` para seu dominio:

```nginx
server {
    listen 80;
    server_name seu-dominio.com www.seu-dominio.com;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location ~ /\.(env|git) {
        deny all;
    }
}
```

Ative o site e reinicie o Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/eclipse-ot.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Passo 6: Configurar config.local.php

Crie o arquivo de configuracao local baseado no exemplo:

```bash
sudo cp /opt/repos/myaac-eclipse/config.local.php.example /var/www/html/config.local.php
sudo nano /var/www/html/config.local.php
```

Edite com suas credenciais reais:

```php
<?php
return [
    'database_host' => '127.0.0.1',
    'database_port' => 3306,
    'database_name' => 'canary',
    'database_user' => 'seu_usuario',
    'database_password' => 'sua_senha_segura',
    'server_ip' => 'seu_ip_publico',
    'template' => 'canary',
];
```

**IMPORTANTE**: Nunca commite este arquivo com credenciais reais!

## Passo 7: Verificar Instalacao

1. Acesse seu dominio no navegador
2. Verifique se o tema Eclipse OT esta sendo exibido
3. Confirme que a news de boas-vindas aparece
4. Teste a criacao de conta e personagem

## Resolucao de Problemas

### Tema nao aparece

```bash
# Verificar permissoes
ls -la /var/www/html/plugins/theme-canary/themes/canary/

# Limpar cache
sudo find /var/www/html/system/cache -type f -delete

# Verificar logs
sudo tail -f /var/log/nginx/error.log
```

### Erro 502 Bad Gateway

```bash
# Verificar se PHP-FPM esta rodando
sudo systemctl status php8.2-fpm

# Reiniciar se necessario
sudo systemctl restart php8.2-fpm
```

### CSS/JS nao carrega

Verifique se o Nginx esta servindo arquivos estaticos corretamente e incremente a versao do CSS no `index.php` se necessario.

## Proximos Passos

- [Configuracao do Tema](./theme.md)
- [Operacoes e Manutencao](./operations.md)
- [Seguranca](./security.md)
