# AGENTS.md

## Scope

This directory contains numbered SQL scripts for MyAAC/Eclipse OT site content and configuration changes.

## SQL Guidelines

- Follow the existing numeric prefix pattern, for example `007-description.sql`.
- Prefer idempotent SQL where practical.
- Add comments for non-obvious updates.
- Keep statements scoped to the intended MyAAC tables.
- Avoid touching real player/account data unless explicitly requested.
- Do not include credentials, dumps of production data or private account information.

## Common Tables

- `myaac_news`: site news.
- `myaac_menu`: menu entries.
- `myaac_settings`: site settings.
- `myaac_pages`: custom pages.
- `myaac_plugins`: installed plugins.

## Safety

- Review SQL against the target MyAAC schema before running it.
- For destructive updates/deletes, include a clear WHERE clause and document the intent.
- If a migration depends on existing rows, make the dependency visible in comments.
