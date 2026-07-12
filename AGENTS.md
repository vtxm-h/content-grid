# AGENTS.md

## Project overview

This repository is part of the VTXM modular Contao element system.

Keep the project role clear and do not mix responsibilities between repositories.

This Contao bundle provides a structural content grid element that renders published content elements from a selected source article inside a configurable grid wrapper.

## Repository-specific role

This repository is responsible for arranging multiple content elements in a grid.

Keep grid behavior separate from the logic of individual content elements.

Guard against invalid source references, empty sources and recursive inclusion.

Do not turn this repository into a collection of concrete content elements.

The element uses page/article source selection and a render stack for recursion protection. Preserve those safeguards when touching rendering logic.

This bundle is theme-agnostic. It should output stable hooks and modifiers while actual CSS belongs in `frontend-assets` or project CSS.

## VTXM architecture rules

This repository is part of the VTXM modular Contao element system.

Conceptual repository roles:

- `frontend-assets` provides shared frontend assets, scripts and styles.
- `content-elements` provides actual Contao content elements.
- `section-elements` provides inline structural Start, Area and End elements inside the current article.
- `content-grid` arranges multiple content elements in a grid.
- `article-insert` inserts or renders article content in another context.
- `layout-preset` provides macro layout structures with slots or layout modes.

Keep these roles separated.

Do not merge responsibilities between repositories without explicit architectural approval.

Structure elements such as `content-grid`, `article-insert` and `layout-preset` should organize, include or arrange content. They should not contain unrelated content-element logic.

Content elements should provide actual frontend content. They should not become layout controllers unless explicitly designed for that purpose.

Shared frontend assets should remain generic. They should not contain project-specific rendering logic for individual content elements unless explicitly required.

## General rules

- Read `README.md` before making changes.
- Inspect the relevant files before editing.
- Keep changes small, focused and reviewable.
- Preserve existing architecture and naming conventions.
- Prefer additive changes over destructive refactoring.
- Do not introduce new dependencies without a clear reason.
- Do not change database structure without explicit approval.
- Do not touch secrets, credentials, `.env` files or private notes.
- Do not commit files from `.codex-private/`.
- Explain risks, assumptions and changed files after every task.

## JavaScript rules

- Follow Skullz-Ready-Pattern where JavaScript is used.
- Use `jQuery(document).ready(...)`.
- Do not use IIFEs.
- Avoid unnecessary global variables.
- Use defensive existence checks.
- Inject scripts only once via ID check.
- Define functions first, call them at the bottom.
- Do not silently rewrite existing JavaScript architecture unless explicitly requested.
- This bundle should not include frontend JavaScript unless explicitly requested.

## Contao rules

- Keep compatibility with PHP `^8.0` and Contao `^4.13`.
- Use classic Contao registration in `src/Resources/contao/config/config.php`.
- Do not add `services.yaml` unless explicitly requested.
- Do not rename existing content elements, modules, templates, fields or palettes without migration notes.
- Do not change DCA fields, palettes or database structure without explicit explanation.
- Preserve existing frontend output unless the task explicitly requires changes.
- Keep backend labels, palettes and template names consistent with the existing naming style.
- Avoid recursive article or content inclusion unless explicit guards are implemented.
- Prefer clear separation between backend configuration, frontend templates and rendering logic.
- Keep `cssID` support on the grid root element intact.

## CSS rules

- Preserve existing class names unless explicitly asked.
- Avoid broad selectors that affect unrelated components.
- Keep responsive behavior intact.
- Prefer component-scoped styles.
- Do not remove existing utility classes unless their usage has been checked.
- Avoid global CSS changes unless the repository is explicitly responsible for shared frontend assets.
- This bundle should expose stable grid hooks; shared styling belongs in `frontend-assets` or project CSS.

## Git rules

- Do not work directly on `main` unless explicitly instructed.
- Use small commits with clear messages.
- Summarize changed files after every task.
- Mention open points and possible follow-up work.
- Before committing, check that `.codex-private/`, `.env`, local notes, caches and generated build artifacts are not included.

## Private context

Private project notes may exist locally in `.codex-private/`.

These files are private, synced outside Git, and must never be committed, copied into public files, or included in pull requests.

If private notes are present locally, read them only for context and keep them out of public repository files.
