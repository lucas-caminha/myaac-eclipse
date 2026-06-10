# AGENTS.md

## Scope

This directory contains dated migration scripts and migration documentation.

## Migration Guidelines

- Use date-prefixed names when adding files here, for example `2026-06-10-change-name.sql`.
- Explain why the migration exists at the top of the file.
- Prefer idempotent migrations.
- Keep rollback notes in comments when rollback is realistic.
- Do not include real production data.

## Review

- Check interactions with numbered SQL files in `sql/`.
- Update `docs/sql-migrations.md` when adding or changing migration behavior.
