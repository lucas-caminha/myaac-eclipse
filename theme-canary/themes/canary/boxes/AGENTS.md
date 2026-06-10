# AGENTS.md

## Scope

This directory contains sidebar/theme boxes used by the Canary theme.

Some boxes render PHP directly and some delegate to Twig templates under `boxes/templates/`.

## Box Guidelines

- Keep box dimensions compatible with the existing themebox image assets.
- Preserve existing IDs/classes when they are tied to CSS or JavaScript.
- Use `$template_path` for theme image URLs, following nearby files.
- Use `getLink()` for internal actions and links where possible.
- Avoid changing randomized or rotating box behavior unless explicitly requested.
- Keep text short enough to fit image-backed layouts.

## Templates

- If a box already has a matching Twig template in `boxes/templates/`, prefer extending that pattern instead of mixing unrelated rendering styles.
- Keep PHP logic light; move repeated markup into templates only when it reduces real duplication.

## Verification

- Inspect the sidebar visually after changing a box.
- Check that images load and hover/button states still work.
