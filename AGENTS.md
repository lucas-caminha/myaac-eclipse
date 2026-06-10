# AGENTS.md

## Project Overview

This repository contains the public website customization for Eclipse OT, built on MyAAC with the Canary theme.

Main areas:

- `theme-canary/themes/canary/`: active theme files, Twig templates, PHP pages, CSS, JS and images.
- `docs/`: project documentation for install, configuration, operations, security and theme work.
- `sql/`: SQL content and numbered site migrations.
- `docs/migrations/`: documented or dated migration scripts.
- `nginx/`: example Nginx configuration.
- `scripts/`: helper scripts and operational examples.

## General Guidelines

- Prefer small, focused changes that match the existing MyAAC/Canary structure.
- Read nearby files before editing; this project relies on local conventions.
- Do not introduce a new framework or build pipeline unless explicitly requested.
- Keep public repository content free of production secrets and runtime data.
- Never commit `config.local.php`, database passwords, admin credentials, SSH keys, cache, logs, backups or real player/account data.
- If behavior changes, update the relevant documentation in `docs/`.

## MyAAC Notes

- The production theme is deployed under `/var/www/html/plugins/theme-canary/themes/canary/`.
- After Twig, PHP, menu or config changes, clear the MyAAC cache.
- Theme configuration usually lives in `config.ini`, `config.php` and `menus.php`.
- Custom pages are usually exposed through MyAAC page/menu records and files under the theme.

## Deployment Reference

Typical theme deploy:

```bash
sudo rsync -a theme-canary/themes/canary/ /var/www/html/plugins/theme-canary/themes/canary/
sudo chown -R www-data:www-data /var/www/html/plugins/theme-canary/themes/canary
sudo find /var/www/html/system/cache -type f -delete
```

Apply SQL only after reviewing it against the target database.

## Verification

- For PHP files, check syntax when possible.
- For Twig/CSS changes, inspect the affected page visually when a local or staging site is available.
- For SQL changes, prefer idempotent statements and include enough comments for safe review.
