# Requisitos do Sistema

## Requisitos Minimos

### Sistema Operacional
- **Ubuntu 22.04 LTS** (recomendado)
- Debian 11+
- Outras distribuicoes Linux com suporte a systemd

### Servidor Web
- **Nginx** (recomendado) ou Apache 2.4+
- Configuracao para PHP-FPM

### PHP
- **PHP 8.2** ou superior
- Extensoes obrigatorias:
  - `php-fpm` - FastCGI Process Manager
  - `php-mysql` - Conexao com MySQL/MariaDB
  - `php-curl` - Requisicoes HTTP
  - `php-gd` - Manipulacao de imagens
  - `php-mbstring` - Suporte a strings multibyte
  - `php-xml` - Processamento XML
  - `php-zip` - Manipulacao de arquivos ZIP

### Banco de Dados
- **MariaDB 10.6+** ou MySQL 8.0+
- Charset: `utf8mb4`
- Collation: `utf8mb4_unicode_ci`

### MyAAC Base
- MyAAC 2.x instalado em `/var/www/html`
- Plugin Canary Theme instalado

## Requisitos de Hardware (Producao)

| Componente | Minimo | Recomendado |
|------------|--------|-------------|
| CPU        | 2 cores | 4+ cores |
| RAM        | 2 GB   | 4+ GB |
| Disco      | 20 GB SSD | 50+ GB SSD |
| Banda      | 100 Mbps | 1 Gbps |

## Verificando Requisitos

### Verificar versao do PHP
```bash
php -v
```

### Verificar extensoes PHP
```bash
php -m | grep -E "mysql|curl|gd|mbstring|xml|zip"
```

### Verificar versao do MySQL/MariaDB
```bash
mysql --version
```

### Verificar Nginx
```bash
nginx -v
```

## Instalando Dependencias (Ubuntu 22.04)

```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Nginx
sudo apt install nginx -y

# Instalar PHP 8.2 e extensoes
sudo apt install php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd \
    php8.2-mbstring php8.2-xml php8.2-zip -y

# Instalar MariaDB
sudo apt install mariadb-server -y

# Habilitar servicos
sudo systemctl enable nginx php8.2-fpm mariadb
sudo systemctl start nginx php8.2-fpm mariadb
```

## Configuracao do PHP

Edite `/etc/php/8.2/fpm/php.ini`:

```ini
; Limites de memoria e upload
memory_limit = 256M
upload_max_filesize = 32M
post_max_size = 32M
max_execution_time = 60

; Timezone (ajuste conforme sua regiao)
date.timezone = America/Sao_Paulo

; Seguranca
expose_php = Off
```

Reinicie o PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

## Proximos Passos

Apos verificar todos os requisitos, prossiga para o [Guia de Instalacao](./install.md).
