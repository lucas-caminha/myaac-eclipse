# AGENTS.md

## Scope

This directory contains example Nginx configuration for the MyAAC/Eclipse OT site.

## Nginx Guidelines

- Keep files as examples unless the task explicitly targets production config.
- Do not include real domains, private IPs, certificates or secrets.
- Preserve security protections for sensitive PHP/config paths.
- Keep PHP-FPM socket/version assumptions aligned with `docs/requirements.md` and `docs/install.md`.

## Verification

- Recommend `sudo nginx -t` before reload when config changes.
- If docs need updating, adjust `docs/install.md`, `docs/operations.md` or `docs/security.md`.
