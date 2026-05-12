# Content Grid (Contao)

Reusable Contao content element that renders the published content elements of a selected source article inside a configurable grid.

Designed as a micro-layout element: it defines a grid wrapper, while actual content is managed through normal Contao content elements in a source article.

- Includes recursion protection
- Theme-agnostic output
- No frontend CSS included by design


## Usage

- Add a new content element of type **Content Grid**
- Select the source page
- Save / reload if needed
- Select the source article
- Choose:
  - columns
  - gap
  - alignment
  - mobile stacking
- Add project CSS for the actual grid behavior

The selected article can contain any published Contao content elements, for example:

- Iconboxes
- Text elements
- Images
- Quotes
- Cards
- Custom content elements


## Recommended Role

Use this bundle when you need to render multiple content elements as a grid.

Recommended separation:

- `article-insert` = article include module
- `layout-preset` = macro layout / split layout
- `content-grid` = micro layout / grid container
- `content-elements` = reusable content blocks

Typical structure:

```text
Layout Preset
├── Slot A: Members Grid
└── Slot B: Content Grid
        └── Source article: Iconboxes
              ├── Iconbox
              ├── Iconbox
              ├── Iconbox
              └── Iconbox
```


## Notes

This bundle does not include frontend CSS intentionally.

Use your project CSS or asset pipeline to define:

- column behavior
- spacing
- responsive stacking
- item styling
- dark / light section variants

CSS ID / class values from the backend are preserved:

- configured ID is rendered on the root element
- configured CSS class is appended to the root element


## Recursion Protection

The element keeps a render stack of source article IDs.

If an article would be rendered again while it is already being rendered, the nested render is skipped to prevent recursive article inclusion.

Example:

- Content Grid renders Article A
- Article A contains another Content Grid
- That Content Grid tries to render Article A again

In this case, rendering is stopped for the recursive article.


## HTML Hooks

Root class:

- `.ce_vtxm_content_grid`

Inner hooks:

- `.content-grid__headline`
- `.content-grid__inner`
- `.content-grid__item`

Modifier classes:

- `.cg--cols-2`
- `.cg--cols-3`
- `.cg--cols-4`
- `.cg--gap-small`
- `.cg--gap-medium`
- `.cg--gap-large`
- `.cg--align-start`
- `.cg--align-center`
- `.cg--align-stretch`
- `.cg--stack-mobile`


## Template

```text
content_grid.html5
```


## Installation (via Composer / Contao Manager)

Add the package definition to your Contao project `composer.json` or install it via your configured repository setup.

Example package reference:

```json
{
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "vtxm-h/content-grid",
        "version": "1.0.2",
        "type": "contao-bundle",
        "license": "MIT",
        "description": "Contao 4.13 structural content element: render article contents as a configurable grid.",
        "dist": {
          "url": "https://github.com/vtxm-h/content-grid/archive/refs/tags/v1.0.2.zip",
          "type": "zip"
        },
        "autoload": {
          "psr-4": {
            "Vendor\\ContentGridBundle\\": "src/"
          }
        },
        "require": {
          "php": "^8.0",
          "contao/core-bundle": "^4.13",
          "contao/manager-plugin": "^2.0"
        },
        "extra": {
          "contao-manager-plugin": "Vendor\\ContentGridBundle\\ContaoManager\\Plugin"
        }
      }
    }
  ]
}
```

Install:

```bash
composer require vtxm-h/content-grid
```

Then update the Contao database so the new `tl_content` fields are created.


## Compatibility

Contao 4.13
PHP 8.0+

## License

MIT
