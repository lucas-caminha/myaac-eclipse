# AGENTS.md

## Scope

This directory contains helper and example operational scripts.

## Script Guidelines

- Treat scripts as examples unless explicitly asked to make deploy-ready automation.
- Keep scripts POSIX-shell friendly for the Linux VPS environment.
- Do not embed secrets, tokens, private hostnames or credentials.
- Prefer explicit paths that match the documentation.
- Add comments only where they prevent operational mistakes.

## Safety

- Be careful with destructive commands.
- For cache clearing, target only the documented MyAAC cache path.
- For service commands, keep them readable and easy to review before use.
