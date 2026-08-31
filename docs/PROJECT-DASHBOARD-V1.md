# TUFF BEATZ — Project Dashboard V1

## Purpose

The Production Portal now continues beyond intake into a project-specific workspace for artists, groups, bands, songwriters, producers, labels and other industry clients.

Each submitted `tb_request` can be opened from **Start a Project → Your Projects → Open Workspace**.

## Workspace sections

- Overview — service, status, genre, BPM/key, stems, target date, creative direction and mix/master notes.
- Files — existing project attachments, large-session transfer link, and additional multi-file uploads.
- Messages — project-specific client/TUFF BEATZ conversation stored with the request.
- Credits — producer, songwriter, musician, engineer and collaborator information managed from WordPress admin.
- Payments — quote/deposit/balance/paid status managed from WordPress admin.
- Delivery — delivery notes and final secure links managed from WordPress admin.

## Workflow progress

The dashboard presents the production lifecycle as:

`Intake → Approved → Production → Revisions → Mastering → Delivery`

The visual progress state is derived from the existing Project Request status.

## Access control

A normal portal user can only open a `tb_request` that they authored. WordPress administrators/editors with permission to edit the request may also access the workspace for review and communication.

## Admin controls

Project Requests now include a **Client Dashboard & Delivery** metabox for:

- Credits / personnel
- Payment status
- Delivery notes
- Final delivery links
- Direct dashboard link
- Message count

## Current limitation / next security phase

Project files still use WordPress Media Library attachment storage. The next major infrastructure upgrade should move unreleased client files and final masters into protected/private storage with authorization checks and expiring download links.
