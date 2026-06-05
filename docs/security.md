# Seguranca

Este guia documenta as melhores praticas de seguranca para o MyAAC Eclipse OT.

## Arquivos Sensiveis

### Nunca Commitar

Os seguintes arquivos/dados NUNCA devem ser commitados no repositorio:

| Item | Descricao |
|------|-----------|
| `config.local.php` | Credenciais de banco de dados |
| Senhas de admin | Credenciais do painel administrativo |
| Chaves SSH | Acesso ao servidor |
| Backups de producao | Dumps de banco de dados |
| Dados de jogadores | Informacoes pessoais de usuarios |
| Chaves de API | Tokens de servicos externos |

### .gitignore Recomendado

Adicione ao `.gitignore` do projeto:

```gitignore
# Configuracoes locais
config.local.php
*.local.php

# Backups
*.sql
*.sql.gz
backup/
backups/

# Logs
*.log
logs/

# Cache
cache/
system/cache/

# Chaves
*.pem
*.key
id_rsa*

# Ambiente
.env
.env.*
```

## Configuracao do Nginx

### Bloquear Arquivos Sensiveis

Adicione ao seu `nginx.conf`:

```nginx
# Bloquear arquivos de configuracao
location ~ /\.(env|git|htaccess|htpasswd) {
    deny all;
    return 404;
}

# Bloquear arquivos PHP de configuracao direta
location ~ ^/(config|system|install).*\.php$ {
    deny all;
    return 404;
}

# Bloquear acesso ao cache
location ~ ^/system/cache {
    deny all;
    return 404;
}

# Bloquear backups
location ~ \.(sql|sql\.gz|bak|backup)$ {
    deny all;
    return 404;
}
```

### Headers de Seguranca

```nginx
# Headers de seguranca
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;
```

### HTTPS (Recomendado)

Instale um certificado SSL com Let's Encrypt:

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obter certificado
sudo certbot --nginx -d seu-dominio.com -d www.seu-dominio.com

# Renovacao automatica (ja configurada pelo certbot)
sudo systemctl status certbot.timer
```

## Configuracao do PHP

### php.ini Seguro

Edite `/etc/php/8.2/fpm/php.ini`:

```ini
; Ocultar versao do PHP
expose_php = Off

; Desabilitar funcoes perigosas
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source

; Limitar includes
allow_url_include = Off
allow_url_fopen = Off

; Logs de erro (nao exibir em producao)
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php_errors.log

; Sessoes seguras
session.cookie_httponly = On
session.cookie_secure = On
session.use_strict_mode = On
```

### Pool PHP-FPM

Edite `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
; Usuario e grupo
user = www-data
group = www-data

; Permissoes do socket
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

; Limites de processos
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

## Banco de Dados

### Usuario com Privilegios Minimos

Crie um usuario especifico para o MyAAC:

```sql
-- Criar usuario
CREATE USER 'myaac'@'localhost' IDENTIFIED BY 'senha_forte_aqui';

-- Conceder apenas privilegios necessarios
GRANT SELECT, INSERT, UPDATE, DELETE ON canary.* TO 'myaac'@'localhost';

-- Aplicar
FLUSH PRIVILEGES;
```

### Senha Forte

Use senhas fortes para o banco de dados:

```bash
# Gerar senha aleatoria
openssl rand -base64 32
```

### Backup Criptografado

```bash
# Backup criptografado com GPG
mysqldump canary | gpg --symmetric --cipher-algo AES256 > backup_$(date +%Y%m%d).sql.gpg

# Restaurar
gpg --decrypt backup_20260602.sql.gpg | mysql canary
```

## Firewall

### UFW (Ubuntu)

```bash
# Instalar UFW
sudo apt install ufw -y

# Regras basicas
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Permitir SSH
sudo ufw allow ssh

# Permitir HTTP/HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Permitir porta do servidor de jogo (ajuste conforme necessario)
sudo ufw allow 7171/tcp
sudo ufw allow 7172/tcp

# Ativar firewall
sudo ufw enable

# Verificar status
sudo ufw status verbose
```

## Fail2Ban

Proteja contra ataques de forca bruta:

```bash
# Instalar
sudo apt install fail2ban -y

# Configurar
sudo nano /etc/fail2ban/jail.local
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
port = ssh
filter = sshd
logpath = /var/log/auth.log
maxretry = 3

[nginx-http-auth]
enabled = true
filter = nginx-http-auth
logpath = /var/log/nginx/error.log
```

```bash
# Reiniciar
sudo systemctl restart fail2ban

# Verificar status
sudo fail2ban-client status
```

## Auditoria

### Verificar Logins Falhos

```bash
# SSH
grep "Failed password" /var/log/auth.log | tail -20

# MyAAC (se logado)
mysql -e "SELECT * FROM myaac_account_actions WHERE action = 'login_failed' ORDER BY date DESC LIMIT 20" canary
```

### Verificar Alteracoes de Arquivos

```bash
# Ultimas alteracoes no tema
find /var/www/html/plugins/theme-canary -mtime -1 -ls

# Verificar integridade (crie um baseline primeiro)
md5sum /var/www/html/plugins/theme-canary/themes/canary/*.php > baseline.md5
# Depois, compare
md5sum -c baseline.md5
```

## Checklist de Seguranca

- [ ] `config.local.php` nao esta no repositorio
- [ ] HTTPS configurado com certificado valido
- [ ] Firewall ativo (UFW)
- [ ] Fail2Ban configurado
- [ ] Usuario de banco de dados com privilegios minimos
- [ ] Senhas fortes em todos os lugares
- [ ] `expose_php = Off` no php.ini
- [ ] `display_errors = Off` em producao
- [ ] Headers de seguranca no Nginx
- [ ] Backups automaticos e criptografados
- [ ] Atualizacoes de seguranca do SO habilitadas

## Atualizacoes de Seguranca

Mantenha o sistema atualizado:

```bash
# Atualizacoes automaticas de seguranca
sudo apt install unattended-upgrades -y
sudo dpkg-reconfigure unattended-upgrades
```

## Proximos Passos

- [Changelog](./changelog.md)
- [Voltar ao Inicio](./README.md)
