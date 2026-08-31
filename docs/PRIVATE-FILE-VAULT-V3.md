# TUFF BEATZ — Private File Vault V3

**Status:** Production feature  
**Purpose:** Protect unreleased client audio, stems, sessions, mixes, masters and delivery files from ordinary public WordPress Media Library URLs.

## Architecture

Private project files are stored outside the site's `public_html` directory under the domain-level private storage area:

`private/tuff-beatz-vault/project-{REQUEST_ID}/`

The browser never receives a direct filesystem URL. Downloads are routed through WordPress using an authenticated `admin-post.php` action.

A download is allowed only when:

1. The visitor is logged in.
2. The visitor owns the project request or has WordPress permission to edit it.
3. The requested file belongs to that project.
4. The per-file WordPress nonce is valid.

## File metadata

Each vault file records:

- UUID file ID
- original filename
- randomized stored filename
- file size
- MIME information
- category (`source` or `delivery`)
- version/delivery label
- uploader user ID
- upload timestamp
- SHA-256 checksum

Allowed extensions currently include WAV, AIFF/AIF, MP3, M4A, ZIP, PDF, TXT and MIDI.

## Client workflow

New Production Portal intake uploads now enter the Private File Vault instead of becoming public Media Library attachments.

Inside Project Dashboard → Files, clients can:

- see secure project files
- download authorized files
- upload additional project material
- label new uploads, e.g. `New Stems`, `Vocal Fixes`, `Revision Files`

## Versioned TUFF BEATZ delivery

WordPress admins receive a **Private File Vault — Versions & Delivery** metabox on each Project Request.

TUFF BEATZ can upload one or many files with a version label such as:

- Mix V1
- Mix V2
- Mix V3
- Master V1
- Final Master
- Instrumental
- Acapella
- TV Mix
- Stems Delivery

Delivery uploads automatically move the request status to `Final Delivery` and appear in the client's Project Dashboard → Delivery section.

## Legacy files

Files uploaded before V3 may still exist as regular WordPress Media Library attachments and are labeled as legacy in the project workspace. New uploads use the private vault. Existing public attachments are not automatically migrated or deleted by this release.

## Security notes

The vault location is deliberately outside `public_html`, which is stronger than relying on `.htaccess` rules for a directory that is still inside the public web root. File names on disk are randomized. Authorization is checked on every download.

This is application-level access control. Server backups and hosting administrators can still access files at the operating-system level, as expected for server-hosted project data.

## Key files

- `tuff-beatz/inc/private-file-vault.php`
- `tuff-beatz/inc/project-portal.php`
- `tuff-beatz/inc/project-dashboard.php`
- `tuff-beatz/page-project-dashboard.php`

## Visual baseline rule

Private File Vault V3 does not redesign the approved TUFF BEATZ V3.4 public homepage. The approved V3.4 visual baseline remains the reference for public-facing site changes.
