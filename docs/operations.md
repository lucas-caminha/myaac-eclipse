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

### Dominio, DNS e HTTPS

Configuracao atual de producao:

| Item | Valor |
|------|-------|
| Dominio principal | `eclipseot.com.br` |
| Alias | `www.eclipseot.com.br` |
| IP publico atual | `143.95.209.234` |
| Porta SSH do VPS | `22022` |
| E-mail do certificado | `adm.eclipseot@gmail.com` |

Procedimento usado para ativar o dominio em producao:

1. Criar/validar os registros DNS:
   - `A @ -> 143.95.209.234`
   - `CNAME www -> eclipseot.com.br`
2. Ajustar o vhost Nginx:

```nginx
server_name eclipseot.com.br www.eclipseot.com.br;
```

3. Atualizar a URL publica do MyAAC em `/var/www/html/config.local.php`:

```php
$config['site_url'] = 'https://eclipseot.com.br/';
```

4. Testar o Nginx:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

5. Limpar o cache do MyAAC:

```bash
sudo find /var/www/html/system/cache -type f -delete
```

6. Instalar Certbot no Ubuntu quando necessario:

```bash
sudo apt-get update
sudo apt-get install -y certbot python3-certbot-nginx
```

7. Emitir e instalar o certificado:

```bash
sudo certbot --nginx -d eclipseot.com.br -d www.eclipseot.com.br --non-interactive --agree-tos -m adm.eclipseot@gmail.com --redirect
```

8. Validar:

```bash
curl -I http://eclipseot.com.br
curl -I https://eclipseot.com.br
curl -I http://www.eclipseot.com.br
curl -I https://www.eclipseot.com.br
```

Resultados esperados:

- `http://` responde `301 Moved Permanently`
- `https://` responde `200 OK`

### Observacao Sobre IPv6 e Certbot

Durante a primeira emissao do certificado, o `certbot` apresentou `ReadTimeout` ao falar com a API da Let's Encrypt, embora `curl` funcionasse normalmente.

O workaround aplicado no VPS foi priorizar IPv4 em `/etc/gai.conf`:

```bash
echo 'precedence ::ffff:0:0/96  100' | sudo tee -a /etc/gai.conf
```

Depois disso, a emissao do certificado funcionou normalmente.

Se o erro voltar a acontecer:

1. Teste conectividade com:

```bash
curl -I https://acme-v02.api.letsencrypt.org/directory
```

2. Confirme a preferencia IPv4 em `/etc/gai.conf`
3. Tente novamente o `certbot`

### Renovacao do Certificado

O `certbot` ja deixou uma tarefa automatica de renovacao. Ainda assim, vale conferir periodicamente:

```bash
sudo certbot renew --dry-run
systemctl list-timers | grep certbot
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

### Donate em Dupla

A pagina `/duo-donate` usa a mesma configuracao de Mercado Pago do fluxo `/points`, mas exige aceite do parceiro antes de gerar o Pix.

Notas:
- O jogador principal escolhe um personagem da propria conta, informa o personagem parceiro e seleciona um outfit no modal.
- O parceiro precisa estar logado na conta dona do personagem informado para aceitar o convite.
- O pagamento so pode ser gerado depois do aceite.
- Ao receber webhook `approved`, o site divide os Eclipse Coins entre as duas contas e aplica 2 horas de boost nos dois personagens (`7200` segundos em `players.xpboost_stamina`).
- O outfit escolhido fica registrado em `eclipse_duo_donation_rewards` com status `pending_server`, para entrega por script do servidor.
- A migration obrigatoria e `sql/015-add-duo-donations.sql`.

### Patrocinio de Boosted

A pagina `/boosted-sponsor` usa saldo interno de Tibia Coins:

```bash
ECLIPSE_BOOSTED_APPLY_TOKEN="token-forte-apenas-se-usar-via-http"
ECLIPSE_MONSTER_DATA_PATH="/caminho/para/data-otservbr-global/monster"
```

Notas:
- Existe 1 slot de `boss` e 1 slot de `creature` para o proximo server save.
- O custo e fixo: `250 Tibia Coins` para boss e `300 Tibia Coins` para creature.
- O alvo escolhido entra no proximo server save e depois fica 10 dias em cooldown.
- Quando `myaac_monsters` nao estiver populada com `raceid`, configure `ECLIPSE_MONSTER_DATA_PATH` apontando para a pasta `monster` do Canary/OTServBR para o aplicador resolver o `raceid` pelos arquivos Lua.
- Depois do server save, execute:

```bash
php /var/www/html/plugins/theme-canary/webhooks/boosted-sponsor-apply.php
```

- Se quiser rodar via HTTP, configure `ECLIPSE_BOOSTED_APPLY_TOKEN` e chame:

```bash
curl "https://seu-dominio.com/plugins/theme-canary/webhooks/boosted-sponsor-apply.php?token=SEU_TOKEN"
```

## Deploy de Atualizacoes

### Atualizar Tema

```bash
cd /opt/repos/myaac-eclipse

# Puxar alteracoes do repositorio
git pull origin main

# Sincronizar arquivos do tema
sudo rsync -av theme-canary/themes/canary/ /var/www/html/plugins/theme-canary/themes/canary/

# Sincronizar plugins publicos do Eclipse
sudo rsync -av plugins/ /var/www/html/plugins/

# Sincronizar paginas publicas do plugin, quando existirem
sudo rsync -av theme-canary/pages/ /var/www/html/plugins/theme-canary/pages/

# Sincronizar webhooks publicos do plugin, quando existirem
sudo rsync -av theme-canary/webhooks/ /var/www/html/plugins/theme-canary/webhooks/

# Sincronizar overrides de paginas do MyAAC, quando existirem
sudo rsync -av system/pages/account/change-info.php /var/www/html/system/pages/account/change-info.php
sudo rsync -av system/pages/account/privacy.php /var/www/html/system/pages/account/privacy.php

# Ajustar permissoes
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/themes/canary
sudo chown -R www-data:www-data /var/www/html/plugins/character-sale
sudo chown -R www-data:www-data /var/www/html/plugins/lua-monsters
sudo chown -R www-data:www-data /var/www/html/plugins/lua-spells
sudo chown -R www-data:www-data /var/www/html/plugins/lgpd-consent
sudo chown -R www-data:www-data /var/www/html/plugins/powerful-guilds
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/pages
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/webhooks
sudo chown www-data:www-data /var/www/html/system/pages/account/change-info.php
sudo chown www-data:www-data /var/www/html/system/pages/account/privacy.php

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
sudo rsync -av plugins/ /var/www/html/plugins/
sudo rsync -av theme-canary/pages/ /var/www/html/plugins/theme-canary/pages/
sudo rsync -av theme-canary/webhooks/ /var/www/html/plugins/theme-canary/webhooks/
sudo rsync -av system/pages/account/change-info.php /var/www/html/system/pages/account/change-info.php
sudo rsync -av system/pages/account/privacy.php /var/www/html/system/pages/account/privacy.php
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/themes/canary
sudo chown -R www-data:www-data /var/www/html/plugins/character-sale
sudo chown -R www-data:www-data /var/www/html/plugins/lua-monsters
sudo chown -R www-data:www-data /var/www/html/plugins/lua-spells
sudo chown -R www-data:www-data /var/www/html/plugins/lgpd-consent
sudo chown -R www-data:www-data /var/www/html/plugins/powerful-guilds
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/pages
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/webhooks
sudo chown www-data:www-data /var/www/html/system/pages/account/change-info.php
sudo chown www-data:www-data /var/www/html/system/pages/account/privacy.php

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
