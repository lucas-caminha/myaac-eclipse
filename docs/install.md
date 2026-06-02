# Install / Deploy

This repo is meant to be deployed on top of an existing MyAAC installation with the Canary theme plugin installed.

## Requirements

- Ubuntu 22.04+
- Nginx
- PHP 8.2-FPM
- MariaDB/MySQL
- MyAAC installed in `/var/www/html`
- Canary theme plugin available at `/var/www/html/plugins/theme-canary/themes/canary`

## Deploy Theme

```bash
cd /opt/repos/myaac-eclipse
sudo rsync -a theme-canary/themes/canary/ /var/www/html/plugins/theme-canary/themes/canary/
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/themes/canary
sudo find /var/www/html/system/cache -type f -delete
```

## Apply News Content

```bash
mysql canary < sql/001-eclipse-news.sql
sudo find /var/www/html/system/cache -type f -delete
```

## Set MyAAC Template

The production site should use the Canary template and not allow template switching:

```bash
cd /var/www/html
php8.2 aac settings:set core.template canary
php8.2 aac settings:set core.template_allow_change false
```

## Production Secrets

Use `config.local.php.example` only as a reference. The real `/var/www/html/config.local.php` must stay on the server and must not be committed.
