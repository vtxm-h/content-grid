# Content Grid

Contao 4.13 bundle that adds the structural content element `vtxm_content_grid`.

The element renders the published content elements from a selected source article into a configurable grid. It is meant for small structural and micro-layout use cases, not for reusable content element composition.

## Installation

```bash
composer require vtxm-h/content-grid
```

Then update the Contao database so the new `tl_content` fields are created.

## Usage

Create a new content element in the `vtxm` category and choose **Content grid**.

Select the source page first, then choose one of that page's articles. The content grid will render the published content elements from the selected article. Each rendered element is wrapped in:

```html
<div class="content-grid__item">...</div>
```

The element includes options for columns, gap size, vertical alignment, and a mobile stacking hook. The bundle does not ship frontend CSS, so projects can define the actual grid behavior in their theme or asset pipeline.

## HTML hooks

The template exposes these hooks:

- `.vtxm-content-grid`
- `.content-grid__headline`
- `.content-grid__inner`
- `.content-grid__item`
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

`cssID` is preserved: the configured ID is rendered on the root element and the configured class is appended to `.vtxm-content-grid`.

## Recursion protection

The element keeps a render stack of source article IDs. If an article would be rendered again while it is already in that stack, the nested render is skipped to prevent recursive article inclusion.
