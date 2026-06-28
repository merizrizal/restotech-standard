# Shared services for business logic

Business workflows will live in Laravel services/actions rather than controllers, views, or Vue components. POS internal endpoints and public API endpoints will call the same services so checkout, ordering, stock mutation, transaction numbering, and related rules do not drift across transports.
