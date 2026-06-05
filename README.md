# MyAAC Eclipse OT

Custom MyAAC/Canary theme and site assets for **Eclipse OT**.

This repository stores the public, non-secret site customization used on the Eclipse OT VPS:

- Canary MyAAC theme files
- Eclipse OT logo, favicon and red/black fantasy background
- Custom CSS layer
- MyAAC news/content SQL
- Deployment documentation
- Example Nginx and local config files

## Documentation

Full documentation is available in the [docs/](docs/) folder:

- [Introduction](docs/introduction.md) - Project overview
- [Requirements](docs/requirements.md) - System requirements
- [Installation](docs/install.md) - Full installation guide
- [Configuration](docs/configuration.md) - Theme and site configuration
- [Theme Customization](docs/theme.md) - Customizing the Canary theme
- [SQL Migrations](docs/sql-migrations.md) - Database scripts
- [Operations](docs/operations.md) - Server maintenance
- [Security](docs/security.md) - Security best practices
- [Changelog](docs/changelog.md) - Version history

## What Is Not Committed

Do not commit production secrets or runtime data:

- `config.local.php`
- database passwords
- MyAAC admin credentials
- SSH keys
- cache, logs and database backups
- real player/account data

## Quick Deploy

```bash
sudo rsync -a theme-canary/themes/canary/ /var/www/html/plugins/theme-canary/themes/canary/
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/themes/canary
sudo find /var/www/html/system/cache -type f -delete
```

Then apply optional content SQL:

```bash
mysql canary < sql/001-eclipse-news.sql
```

See [docs/install.md](docs/install.md) for the full flow.
