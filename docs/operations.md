# Operacoes e Manutencao

Este guia documenta as operacoes do dia a dia e procedimentos de manutencao do MyAAC Eclipse OT.

## Paths Importantes

| Descricao | Caminho |
|-----------|---------|
| Raiz do site | `/var/www/html` |
| Tema Canary | `/var/www/html/plugins/theme-canary/themes/canary` |
| Configuracao MyAAC | `/var/www/html/config.local.php` |
| Cache MyAAC | `/var/www/html/system/cache` |
| Logs Nginx | `/var/log/nginx/` |
| Logs PHP | `/var/log/php8.2-fpm.log` |
| Repositorio | `/opt/repos/myaac-eclipse` |

## Operacoes Comuns

### Limpar Cache

Sempre limpe o cache apos mudancas em templates ou configuracoes:

```bash
sudo find /var/www/html/system/cache -type f -delete
```

### Reiniciar Servicos

```bash
# Nginx
sudo systemctl reload nginx

# PHP-FPM
sudo systemctl restart php8.2-fpm

# MariaDB
sudo systemctl restart mariadb
```

### Verificar Status dos Servicos

```bash
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mariadb
```

### Mercado Pago Pix

A pagina `/points` ja esta preparada para gerar Pix pelo Mercado Pago quando as variaveis estiverem configuradas no ambiente do PHP-FPM/Apache:

```bash
MERCADOPAGO_ACCESS_TOKEN="APP_USR-..."
MERCADOPAGO_WEBHOOK_URL="https://seu-dominio.com/plugins/theme-canary/webhooks/mercadopago.php"
```

Notas:
- Sem `MERCADOPAGO_ACCESS_TOKEN`, o fluxo registra a intencao de doacao, mas nao chama gateway externo.
- `MERCADOPAGO_WEBHOOK_URL` e opcional; se nao for informado, o site usa `getLink('mercadopago-webhook')`.
- Para producao, prefira configurar `MERCADOPAGO_WEBHOOK_URL` apontando para o endpoint cru `/plugins/theme-canary/webhooks/mercadopago.php`.
- O webhook consulta o pagamento no Mercado Pago e so credita coins quando o status retornado for `approved`.
- Depois de alterar variaveis de ambiente, reinicie o servico PHP/Apache usado pelo site.

## Deploy de Atualizacoes

### Atualizar Tema

```bash
cd /opt/repos/myaac-eclipse

# Puxar alteracoes do repositorio
git pull origin main

# Sincronizar arquivos do tema
sudo rsync -av theme-canary/themes/canary/ /var/www/html/plugins/theme-canary/themes/canary/

# Sincronizar paginas publicas do plugin, quando existirem
sudo rsync -av theme-canary/pages/ /var/www/html/plugins/theme-canary/pages/

# Sincronizar webhooks publicos do plugin, quando existirem
sudo rsync -av theme-canary/webhooks/ /var/www/html/plugins/theme-canary/webhooks/

# Sincronizar overrides de paginas do MyAAC, quando existirem
sudo rsync -av system/pages/account/change-info.php /var/www/html/system/pages/account/change-info.php

# Ajustar permissoes
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/themes/canary
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/pages
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/webhooks
sudo chown www-data:www-data /var/www/html/system/pages/account/change-info.php

# Limpar cache
sudo find /var/www/html/system/cache -type f -delete

# Recarregar Nginx (se houver mudancas de config)
sudo systemctl reload nginx
```

### Script de Deploy Automatizado

Crie um script para simplificar o processo:

```bash
#!/bin/bash
# /opt/repos/myaac-eclipse/deploy.sh

set -euo pipefail

echo "=== Deploy Eclipse OT ==="

cd /opt/repos/myaac-eclipse
git pull origin main

echo "Sincronizando tema..."
sudo rsync -av theme-canary/themes/canary/ /var/www/html/plugins/theme-canary/themes/canary/
sudo rsync -av theme-canary/pages/ /var/www/html/plugins/theme-canary/pages/
sudo rsync -av theme-canary/webhooks/ /var/www/html/plugins/theme-canary/webhooks/
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/themes/canary
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/pages
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/webhooks

echo "Limpando cache..."
sudo find /var/www/html/system/cache -type f -delete

echo "Recarregando Nginx..."
sudo systemctl reload nginx

echo "=== Deploy concluido! ==="
```

## Cache Busting

Se assets CSS/JS parecem desatualizados no navegador:

### Opcao 1: Incrementar Query String

Edite o arquivo que carrega o CSS e incremente a versao:

```html
<!-- Antes -->
<link rel="stylesheet" href="arise-overrides.css?v=9">

<!-- Depois -->
<link rel="stylesheet" href="arise-overrides.css?v=10">
```

### Opcao 2: Hard Refresh no Navegador

- **Windows/Linux**: `Ctrl + Shift + R`
- **Mac**: `Cmd + Shift + R`

### Opcao 3: Limpar Cache do Navegador

Ou instrua os usuarios a limparem o cache do navegador.

## Monitoramento

### Verificar Logs de Erro

```bash
# Nginx
sudo tail -f /var/log/nginx/error.log

# PHP-FPM
sudo tail -f /var/log/php8.2-fpm.log

# Todos os logs de uma vez
sudo tail -f /var/log/nginx/*.log /var/log/php8.2-fpm.log
```

### Verificar Uso de Disco

```bash
# Espaco geral
df -h

# Tamanho das pastas do site
du -sh /var/www/html/*

# Tamanho do cache
du -sh /var/www/html/system/cache
```

### Verificar Memoria e CPU

```bash
# Visao geral
htop

# Uso de memoria
free -h

# Processos PHP
ps aux | grep php
```

## Scripts do Servidor de Jogo

### Reiniciar Canary

```bash
# Usando o script de exemplo
sudo cp scripts/restart-canary.sh.example /usr/local/bin/restart-canary.sh
sudo chmod +x /usr/local/bin/restart-canary.sh
sudo restart-canary.sh
```

Conteudo do script:

```bash
#!/usr/bin/env bash
set -euo pipefail
systemctl restart canary
sleep 3
systemctl --no-pager --full status canary | sed -n '1,25p'
```

### Verificar Status do Canary

```bash
sudo systemctl status canary
```

## Backup

### Backup do Banco de Dados

```bash
# Backup completo
mysqldump -u root canary > /backup/canary_$(date +%Y%m%d_%H%M%S).sql

# Backup compactado
mysqldump -u root canary | gzip > /backup/canary_$(date +%Y%m%d_%H%M%S).sql.gz
```

### Backup dos Arquivos

```bash
# Backup do tema customizado
tar -czf /backup/theme_$(date +%Y%m%d).tar.gz /var/www/html/plugins/theme-canary/themes/canary/

# Backup completo do site (sem cache)
tar --exclude='system/cache/*' -czf /backup/myaac_$(date +%Y%m%d).tar.gz /var/www/html/
```

### Restaurar Backup

```bash
# Restaurar banco de dados
mysql canary < /backup/canary_20260602.sql

# Restaurar arquivos
tar -xzf /backup/theme_20260602.tar.gz -C /
```

## Troubleshooting

### Erro 502 Bad Gateway

```bash
# Verificar PHP-FPM
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm

# Verificar socket
ls -la /run/php/php8.2-fpm.sock
```

### Erro 403 Forbidden

```bash
# Verificar permissoes
sudo chown -R www-data:www-data /var/www/html
sudo find /var/www/html -type d -exec chmod 755 {} \;
sudo find /var/www/html -type f -exec chmod 644 {} \;
```

### CSS/JS Nao Carrega

1. Verifique o console do navegador (F12)
2. Limpe o cache do MyAAC
3. Incremente a versao do CSS
4. Verifique os logs do Nginx

### Tema Nao Aparece

```bash
# Verificar se os arquivos existem
ls -la /var/www/html/plugins/theme-canary/themes/canary/

# Verificar configuracao
cat /var/www/html/config.local.php | grep template

# Verificar no banco
mysql -e "SELECT * FROM myaac_settings WHERE name = 'template'" canary
```

## Proximos Passos

- [Seguranca](./security.md)
- [Changelog](./changelog.md)
