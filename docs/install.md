# Guia de Instalacao em VPS

Este guia documenta o fluxo usado para instalar o site MyAAC Eclipse OT junto do servidor Canary em uma VPS Ubuntu 24.04. Ele assume os repositorios:

- Site: `https://github.com/lucas-caminha/myaac-eclipse`
- Servidor: `https://github.com/lucas-caminha/eclipse-ot-server`

Use valores proprios para IP, dominio, senhas e credenciais. Nao commite `config.local.php`, `config.lua`, senhas, dumps ou dados reais.

## 1. Pacotes Base

```bash
apt update
apt install -y git curl ca-certificates unzip zip tar rsync nginx mariadb-server \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl \
  php8.3-gd php8.3-zip php8.3-intl php8.3-bcmath php8.3-readline composer \
  cmake ninja-build build-essential autoconf libtool pkg-config ccache ufw
```

Para compilar o Canary em Ubuntu 24.04, instale tambem GCC/G++ 14 e vcpkg quando necessario:

```bash
apt install -y gcc-14 g++-14 linux-headers-generic
git clone https://github.com/microsoft/vcpkg.git /opt/vcpkg
/opt/vcpkg/bootstrap-vcpkg.sh
```

## 2. Estrutura de Diretorios

```bash
mkdir -p /opt/otserver /opt/repos /opt/scripts /opt/otserver/backups
git clone https://github.com/lucas-caminha/eclipse-ot-server.git /opt/otserver/canary
git clone https://github.com/lucas-caminha/myaac-eclipse.git /opt/repos/myaac-eclipse
```

## 3. Banco de Dados

Crie o banco e o usuario do servidor. Guarde a senha fora do Git.

```bash
DBPASS='troque-esta-senha'
mysql -uroot <<SQL
CREATE DATABASE IF NOT EXISTS canary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'canary'@'localhost' IDENTIFIED BY '${DBPASS}';
GRANT ALL PRIVILEGES ON canary.* TO 'canary'@'localhost';
FLUSH PRIVILEGES;
SQL

printf '%s\n' "$DBPASS" > /root/.canary_db_password
chmod 600 /root/.canary_db_password
mysql -ucanary -p"$DBPASS" canary < /opt/otserver/canary/schema.sql
```

## 4. Instalar MyAAC Base

Instale o MyAAC original em `/var/www/html` e remova `login.php`. O login do cliente deve ficar no login-server/Canary, nao no MyAAC.

```bash
rm -rf /var/www/html
git clone -b develop https://github.com/slawkens/myaac.git /var/www/html
cd /var/www/html
composer install --no-dev --optimize-autoloader
rm -f login.php
mkdir -p system/cache system/logs system/sessions
chown -R www-data:www-data /var/www/html
```

Se estiver usando o bootstrap do quickstart do servidor, rode-o com as variaveis do banco para criar as tabelas/configuracoes do MyAAC.

## 5. Aplicar Camada Eclipse

Copie plugins, overrides de paginas e o tema versionado.

```bash
rsync -a /opt/repos/myaac-eclipse/plugins/ /var/www/html/plugins/
rsync -a /opt/repos/myaac-eclipse/system/ /var/www/html/system/
rsync -a /opt/repos/myaac-eclipse/theme-canary/ /var/www/html/plugins/theme-canary/
rsync -a /opt/repos/myaac-eclipse/theme-canary/themes/canary/ /var/www/html/templates/canary/
cp /opt/repos/myaac-eclipse/theme-canary.json /var/www/html/plugins/theme-canary.json
chown -R www-data:www-data /var/www/html
```

Importante:

- `/var/www/html/plugins/theme-canary/` mantem hooks, paginas e assets do plugin.
- `/var/www/html/templates/canary/` permite que o MyAAC encontre o template ativo.
- `theme-canary.json` registra os hooks `STARTUP` e `TWIG`, incluindo o helper `t()` usado pelo tema.

## 6. Aplicar Migrations SQL

Execute as migrations do site em ordem numerica.

```bash
cd /opt/repos/myaac-eclipse
DBPASS=$(cat /root/.canary_db_password)
for file in sql/[0-9][0-9][0-9]-*.sql; do
  echo "Applying $file"
  mysql -ucanary -p"$DBPASS" canary < "$file"
done
```

A migration `021-normalize-canary-menu-and-settings.sql` registra o estado esperado do menu esquerdo, fixa o template `canary` e habilita o sistema de gifts/shop para exibir `Comprar Points` e `Patrocinar Boosted`.

A migration `023-set-canary-status-endpoint.sql` fixa o status do MyAAC para consultar o Canary localmente em `127.0.0.1:7173`. Em MyAAC develop, aplique tambem o patch do parser de porta numerica:

```bash
sh scripts/patch-myaac-status-port.sh.example /var/www/html
```

## 7. Configuracao Local do MyAAC

Crie `/var/www/html/config.local.php` com dados reais do ambiente:

```php
<?php
return [
    'database_host' => '127.0.0.1',
    'database_port' => 3306,
    'database_name' => 'canary',
    'database_user' => 'canary',
    'database_password' => 'SENHA_DO_BANCO',
    'server_ip' => 'IP_PUBLICO',
    'template' => 'canary',
    'template_allow_change' => false,
];
```

Depois force as mesmas configuracoes pelo CLI do MyAAC, quando disponivel:

```bash
cd /var/www/html
php aac settings:set core.template canary || true
php aac settings:set core.template_allow_change false || true
php aac settings:set core.gifts_system true || true
```

## 8. Dependencia jQuery do Menu

O tema/menu do MyAAC pode emitir `tools/ext/jquery/jquery.min.js`. Se esse arquivo nao existir, o menu esquerdo nao expande porque `MenuItemAction()` usa `$.each(...)`.

Garanta o caminho esperado:

```bash
mkdir -p /var/www/html/tools/ext/jquery
cp /var/www/html/vendor/maximebf/debugbar/src/DebugBar/Resources/vendor/jquery/dist/jquery.min.js \
  /var/www/html/tools/ext/jquery/jquery.min.js
chown -R www-data:www-data /var/www/html/tools
chmod 755 /var/www/html/tools /var/www/html/tools/ext /var/www/html/tools/ext/jquery
chmod 644 /var/www/html/tools/ext/jquery/jquery.min.js
```

Valide:

```bash
curl -I http://127.0.0.1/tools/ext/jquery/jquery.min.js
```

O retorno esperado e `HTTP/1.1 200 OK`.

## 9. Nginx

Crie `/etc/nginx/sites-available/eclipse-ot.conf`:

```nginx
server {
    listen 80;
    server_name _;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~* /(config\.local\.php|composer\.(json|lock)|install|\.git) {
        deny all;
    }
}
```

Ative:

```bash
ln -sf /etc/nginx/sites-available/eclipse-ot.conf /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx
systemctl enable --now nginx php8.3-fpm mariadb
```

### HTTPS com Let's Encrypt

Depois que o DNS do dominio apontar para a VPS, instale o Certbot e emita o certificado:

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx --non-interactive --agree-tos --register-unsafely-without-email \
  --redirect -d eclipseot.com.br -d www.eclipseot.com.br
```

O Certbot cria o timer de renovacao automaticamente. Confira:

```bash
systemctl status certbot.timer --no-pager
certbot certificates
```

Atualize as URLs publicas do MyAAC e do servidor para evitar links com HTTP/IP antigo:

```bash
mysql -ucanary -p"$(cat /root/.canary_db_password)" canary <<'SQL'
UPDATE myaac_settings
SET value = 'https://eclipseot.com.br/'
WHERE name = 'core' AND `key` = 'site_url';

INSERT INTO myaac_settings (name, `key`, value)
SELECT 'core', 'site_url', 'https://eclipseot.com.br/'
WHERE NOT EXISTS (
  SELECT 1 FROM myaac_settings WHERE name = 'core' AND `key` = 'site_url'
);

INSERT INTO myaac_config (name, value)
VALUES ('core.site_url', 'https://eclipseot.com.br/')
ON DUPLICATE KEY UPDATE value = VALUES(value);
SQL
```

Tambem ajuste:

- `/var/www/html/config.local.php`: `$config['site_url'] = 'https://eclipseot.com.br/';`
- `/opt/otserver/canary/config.lua`: `url = "https://eclipseot.com.br/"`

Depois limpe cache e recarregue os servicos:

```bash
find /var/www/html/system/cache -type f ! -name index.html -delete
systemctl reload php8.3-fpm nginx
systemctl restart canary
```

## 10. Configurar e Compilar o Servidor

```bash
cd /opt/otserver/canary
cp config.example.lua config.lua
nano config.lua
```

Configure pelo menos:

- `ip`
- `mysqlHost = "localhost"`
- `mysqlUser = "canary"`
- `mysqlPass`
- `mysqlDatabase = "canary"`
- `serverName = "Eclipse OT"`
- `url`
- `statusProtocolPort`

No deploy Eclipse OT, mantenha `statusProtocolPort = 7171`. O MyAAC deve consultar
o status em `127.0.0.1:7171`; aplique a migration
`sql/023-set-canary-status-endpoint.sql` depois das migrations principais do site.

Baixe o mapa compatível do Canary quando ele nao estiver no repo:

```bash
mkdir -p data-otservbr-global/world
curl -L -o data-otservbr-global/world/otservbr.otbm \
  https://github.com/opentibiabr/canary/releases/download/v3.5.0/otservbr.otbm
```

Compile:

```bash
export CC=gcc-14
export CXX=g++-14
export VCPKG_ROOT=/opt/vcpkg
cmake --preset linux-release
cmake --build --preset linux-release --target canary
cp build/linux-release/bin/canary /opt/otserver/canary/canary
```

Se o fork ainda nao versionar arquivos gerados exigidos pelo build/runtime, crie-os apenas no ambiente da VPS e documente antes de promover para o repo. No deploy atual foram necessarios `src/core.hpp` e `data/core.lua` como arquivos operacionais locais.

## 11. Systemd do Canary

```bash
useradd --system --home /opt/otserver/canary --shell /usr/sbin/nologin canary || true
chown -R canary:canary /opt/otserver/canary
```

Crie `/etc/systemd/system/canary.service`:

```ini
[Unit]
Description=Eclipse OT Canary Server
After=network.target mariadb.service

[Service]
Type=simple
User=canary
Group=canary
WorkingDirectory=/opt/otserver/canary
ExecStart=/opt/otserver/canary/canary
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Ative:

```bash
systemctl daemon-reload
systemctl enable --now canary
```

## 12. Firewall e Portas

```bash
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
ufw allow 7171/tcp
ufw allow 7172/tcp
ufw allow 7173/tcp
ufw --force enable
```

## 13. Limpeza de Cache e Validacao

Sempre limpe cache depois de trocar tema, menus, Twig, PHP ou config:

```bash
find /var/www/html/system/cache -type f -delete
systemctl reload php8.3-fpm
systemctl reload nginx
```

Checklist:

```bash
curl -I http://127.0.0.1/
curl -I http://127.0.0.1/tools/ext/jquery/jquery.min.js
systemctl status nginx php8.3-fpm mariadb canary --no-pager
journalctl -u canary -n 80 --no-pager
```

No navegador, valide:

- o tema Eclipse/Canary carrega;
- o menu esquerdo expande ao clicar;
- aparecem `Agenda de Eventos`, `Privacidade e LGPD`, `Mercado de Personagens`, `VIP & Loyalty`, `Bosses`, `Comprar Points`, `Patrocinar Boosted` e `Comandos e Informações`;
- o console nao mostra erro `"$ is not defined"`;
- o servidor fica online e responde nas portas configuradas.

## Problemas Comuns

### Tema abre com erro 500 e `Unknown "t" function`

O plugin do tema nao foi registrado ou o cache esta velho. Confirme:

```bash
ls -l /var/www/html/plugins/theme-canary.json
find /var/www/html/system/cache -type f -delete
systemctl reload php8.3-fpm
```

### Menu esquerdo nao expande

Verifique se o jQuery esta acessivel:

```bash
curl -I http://127.0.0.1/tools/ext/jquery/jquery.min.js
```

Se retornar `404`, repita o passo 8.

### Itens do menu nao aparecem

Rode novamente a migration `021` e limpe cache:

```bash
mysql -ucanary -p"$(cat /root/.canary_db_password)" canary \
  < /opt/repos/myaac-eclipse/sql/021-normalize-canary-menu-and-settings.sql
find /var/www/html/system/cache -type f -delete
```

### Categoria Shop nao aparece

Confirme `core.gifts_system`:

```bash
cd /var/www/html
php aac settings:get core.gifts_system || true
```

Se necessario:

```bash
php aac settings:set core.gifts_system true || true
```
