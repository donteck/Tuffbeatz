# TUFF BEATZ Client CRM V10

V10 adds a producer-only client relationship directory at `/producer-crm/`.

## What V10 adds

- Producer-only CRM hub.
- Client directory built from TUFF BEATZ client and artist accounts.
- Per-client relationship profile.
- Project count, active projects, completed projects.
- Aggregate quoted project value, paid amount, and balance.
- Private Vault file count across the client's projects.
- Full project history with links into the project workspace and WordPress management screen.
- Direct `Client CRM` entry point from the Producer Portal.

## Access model

Only authenticated producer/admin-capable users can access the Producer CRM. Other authenticated users are redirected to the Client Portal.

## Data model

V10 intentionally reuses the existing TUFF BEATZ project system instead of duplicating project or financial records. CRM totals are computed from existing project requests, V6 quote/payment data, and the V3 private file vault.

## Important limitation

The CRM currently treats the WordPress account that authored a project as the primary client relationship. Collaborators remain project-level relationships and are not promoted to primary CRM clients unless they have their own primary projects.

## URLs

- Producer CRM: `/producer-crm/`
- Producer Portal: `/producer-portal/`
- Client Portal: `/client-portal/`
