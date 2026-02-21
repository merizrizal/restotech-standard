# Restotech Standard: Project Study Notes

This documentation is a comprehensive study of the current code in this repository (`restotech-standard`) as of February 21, 2026.

## Scope Summary

`restotech-standard` is a Yii2 extension module for a restaurant/POS domain, not a standalone Yii2 app.
It packages 3 submodules:

- `backend`: back office (master data, reports, configuration).
- `frontend`: POS operator interface (table/session/order/payment flows).
- `api`: JSON endpoints mirroring part of POS behavior.

## Repository Snapshot

- Total files: `1756`
- PHP files: `346`
- PHP files outside media/vendor-style assets: `345`
- Backend controllers: `39`
- Backend models: `57`
- Backend search models: `34`
- Frontend controllers: `6`
- API controllers: `4` (including `api/controllers/frontend/*`)

## Documentation Map

- `docs/architecture.md`
  - Runtime architecture, module composition, dependencies, config layering.
- `docs/domain-and-flows.md`
  - Business domain map and end-to-end process flows.
- `docs/api-reference.md`
  - API controller surface and endpoint behavior.
- `docs/data-model.md`
  - Data model groups and key entity relationships.
- `docs/operations-and-setup.md`
  - Setup assumptions, required host params/aliases, testing state.
- `docs/insights-and-risks.md`
  - Practical engineering insights, risks, and recommended priorities.
- `docs/appendix-model-index.md`
  - Full model-to-table index extracted from source.
- `docs/appendix-controller-index.md`
  - Controller inventory and action counts.

## Important Context

This package depends heavily on code/configuration from outside this repository:

- `synctech/yii2-synctbase` (required in `composer.json`)
- Host-app params such as `posModule`, `version`, `currencyOptions`, `navigation`
- Host aliases such as `@rootUrl`, `@uploads`, `@uploadsUrl`

Because of that, this repository should be treated as a domain extension layer that plugs into a larger Synctech/Yii2 platform.
