---
name: tailwindcss-development
description: "Use for Cachet Tailwind CSS and UI styling: Blade utility classes, public status-page components, Filament dashboard theme work, responsive layout, dark mode, spacing, typography, and Tailwind v4 source configuration. Skip for backend PHP logic, database queries, API routes, and JavaScript with no HTML or CSS change."
license: MIT
metadata:
  author: laravel
---
# Tailwind CSS Development

## Documentation

Use `search-docs` for detailed Tailwind CSS v4 patterns and documentation.

## Core Package Conventions

- Core uses Tailwind CSS v4 only.
- Public status-page styles enter through `resources/css/cachet.css` and package Blade views under `resources/views`.
- Dashboard styles enter through `resources/css/dashboard/theme.css`. Prefer Filament components and supported theme hooks before adding selectors against Filament internals.
- Preserve the existing CSS-first configuration, `@source` boundaries, plugins, theme variables, and `cachet::` Blade component namespace.
- The public status page follows `prefers-color-scheme`; the Filament dashboard follows its `.dark` class. Match the convention of the surface being changed.
- Source files live under `resources/`. Run `composer build` first when the Testbench host is absent, then run `npm run build` after CSS, Blade utility, or frontend JavaScript changes. Never edit Testbench's mirrored assets.
- The Vite build produces the package's publishable assets and mirrors them into the Testbench skeleton for feature tests.

## Basic Usage

- Use Tailwind CSS classes to style HTML. Check and follow existing Tailwind conventions in the project before introducing new patterns.
- Offer to extract repeated patterns into components that match the project's conventions (e.g., Blade, JSX, Vue).
- Consider class placement, order, priority, and defaults. Remove redundant classes, add classes to parent or child elements carefully to reduce repetition, and group elements logically.

## Tailwind CSS v4 Specifics

- Always use Tailwind CSS v4 and avoid deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.

### CSS-First Configuration

In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed:

<!-- CSS-First Config -->
```css
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
```

### Import Syntax

In Tailwind v4, import Tailwind with a regular CSS `@import` statement instead of the `@tailwind` directives used in v3:

<!-- v4 Import Syntax -->
```diff
- @tailwind base;
- @tailwind components;
- @tailwind utilities;
+ @import "tailwindcss";
```

### Replaced Utilities

Tailwind v4 removed deprecated utilities. Use the replacements shown below. Opacity values remain numeric.

| Deprecated | Replacement |
|------------|-------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |

## Spacing

Use `gap` utilities instead of margins for spacing between siblings:

<!-- Gap Utilities -->
```html
<div class="flex gap-8">
    <div>Item 1</div>
    <div>Item 2</div>
</div>
```

## Dark Mode

If existing pages and components support dark mode, new pages and components must support it the same way, typically using the `dark:` variant:

<!-- Dark Mode -->
```html
<div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
    Content adapts to color scheme
</div>
```

## Common Patterns

### Flexbox Layout

<!-- Flexbox Layout -->
```html
<div class="flex items-center justify-between gap-4">
    <div>Left content</div>
    <div>Right content</div>
</div>
```

### Grid Layout

<!-- Grid Layout -->
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div>Card 1</div>
    <div>Card 2</div>
    <div>Card 3</div>
</div>
```

## Common Pitfalls

- Using deprecated v3 utilities (bg-opacity-*, flex-shrink-*, etc.)
- Using `@tailwind` directives instead of `@import "tailwindcss"`
- Trying to use `tailwind.config.js` instead of CSS `@theme` directive
- Using margins for spacing between siblings instead of gap utilities
- Forgetting to add dark mode variants when the project uses dark mode
