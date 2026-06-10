# AGENTS.md

## Scope

This directory is the active Canary theme customization for Eclipse OT.

It includes PHP entry points, Twig templates, CSS, JS, images, theme boxes, custom pages and theme configuration.

## Theme Guidelines

- Preserve the MyAAC/Canary theme conventions already used here.
- Keep edits close to the affected template, page, box or config file.
- Do not rename public assets unless every reference is updated.
- Prefer existing images, button assets, CSS classes and layout patterns before adding new ones.
- Avoid broad restyling unless the task explicitly asks for it.
- Keep the fantasy/Tibia-style visual language consistent with the existing theme.

## Important Files

- `config.ini`: visual/theme configuration.
- `config.php`: PHP-side theme settings, carousel, social links and menu category config.
- `menus.php`: theme menu structure.
- `index.php`: main theme entry.
- `arise-overrides.css`: custom Eclipse/Arise visual layer.
- `basic.css` and `basic.js`: base theme styles/scripts.

## PHP and Twig

- Keep `defined('MYAAC') or die('Direct access not allowed!');` in custom PHP pages that are loaded through MyAAC.
- Use existing MyAAC helpers such as `getLink()` when linking internal routes.
- Keep Twig syntax compatible with the MyAAC/Twig version used by the site.
- After changing PHP, Twig or config files, clear the MyAAC cache during deployment.

## Visual Checks

- Check desktop and narrow widths when editing page-level CSS.
- Avoid text overflow in old-style image-backed buttons and boxes.
- Be careful with inline styles because many original theme components depend on precise image dimensions.
