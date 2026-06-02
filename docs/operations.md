# Operations Notes

## Useful Production Paths

- Site root: `/var/www/html`
- Theme path: `/var/www/html/plugins/theme-canary/themes/canary`
- MyAAC config: `/var/www/html/config.local.php`
- MyAAC cache: `/var/www/html/system/cache`

## After Deploying Visual Changes

```bash
sudo find /var/www/html/system/cache -type f -delete
sudo systemctl reload nginx
```

If browser assets appear stale, bump the query string in `index.php`, for example:

```html
arise-overrides.css?v=9
```

## Security

Never commit:

- database credentials
- admin passwords
- SSH keys
- production backups
- player/account database dumps
