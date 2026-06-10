# AGENTS.md

## Scope

This directory contains custom MyAAC PHP pages for Eclipse OT.

Examples include downloads, rules, event schedule, OT server info and VIP loyalty pages.

## Page Guidelines

- Start PHP pages with the MyAAC direct-access guard:

```php
defined('MYAAC') or die('Direct access not allowed!');
```

- Set a clear `$title` for each page.
- Follow the existing pattern of page-local CSS when the page is highly custom.
- Keep CSS selectors scoped to a page wrapper such as `.eclipse-download-page` to avoid leaking styles into the full theme.
- Use existing theme colors, borders, image assets and typography patterns where practical.
- Prefer static content in these files only when it is truly site content; put database-driven content in SQL migrations when it belongs in MyAAC tables.

## Links and Assets

- Use `getLink()` for MyAAC internal routes when available.
- Keep download URLs and public asset paths easy to update.
- Do not hardcode production secrets or server-local filesystem paths.

## Verification

- Run a PHP syntax check for edited `.php` pages when possible.
- After deployment, clear MyAAC cache and open the affected page through the site menu or direct route.
