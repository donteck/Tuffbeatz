# TUFF BEATZ Producer & Client Portals V8.5

Status: IMPLEMENTED

V8.5 turns the production workflow into a two-sided front-end portal experience.

## Producer Portal

URL: `/producer-portal/`

For TUFF BEATZ staff/admin users. Provides a production command center with project counts, status overview, progress, client/service details, links to each front-end workspace, and direct management links for WordPress project administration.

## Client Portal

URL: `/client-portal/`

For client/project-member accounts. Shows only projects the signed-in user owns or has permission to view. Each project card shows status, service, milestone-based completion percentage, and an Open Project link to the full Project Dashboard.

## Account routing

WordPress login routing sends producer/admin users to Producer Portal and client users to Client Portal except when an explicit wp-admin destination is requested. Logged-in users see `My Portal` in the existing header CTA position. Logged-out visitors retain `Work With Me`.

## Existing project workspace

The portal does not duplicate V3–V8 project systems. It organizes and exposes them. Opening a project continues into `/project-dashboard/?project=ID`, where the private vault, Mix Review, messages, collaborators, quote/contract/payment workspace, invoices/receipts, milestones/activity timeline, and final delivery remain permission-controlled.

## Security

Producer Portal requires WordPress edit-post capability. Client Portal project visibility reuses project ownership and V5 collaborator permission checks. A client cannot use the producer portal simply by knowing its URL.

## Visual isolation

Portal styling lives in `assets/css/portal-hubs.css` and loads only on the two portal pages. The approved V3.4 public homepage styling remains isolated.

## Files

- `inc/portal-hubs.php`
- `page-producer-portal.php`
- `page-client-portal.php`
- `assets/css/portal-hubs.css`
- `header.php`
- `functions.php`
