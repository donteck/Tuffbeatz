# TUFF BEATZ — APPROVED GOOD VERSION

**Status:** APPROVED / KNOWN-GOOD BASELINE  
**Approved:** August 31, 2026  
**Branch:** `main`

This document marks the TUFF BEATZ website version approved after restoring the WordPress frontend to the approved V3.4 visual reference.

## Source of truth

The approved visual baseline is the V3.4 TUFF BEATZ design and its current WordPress implementation on `main`.

Key identity and presentation that must be preserved:

- TUFF BEATZ — producer identity of Emmanuel Tuffet
- Luxury black + metallic gold visual language
- `SOUND. PURPOSE. LEGACY.` branding
- Navigation: Home, Featured, Music, Credits, Contact
- Premium cinematic homepage composition
- Approved V3.4 hero proportions and portrait treatment
- Clean three-column Featured stats presentation
- Approved release-card spacing, badge treatment, image behavior, and hover lift
- Premium V3.4 music player behavior and queue experience
- Start a Project client/artist portal remains part of the WordPress implementation

## Reference-parity protection

The WordPress frontend includes `assets/css/v34-frontend-restore.css`, loaded after the main stylesheet, to preserve the approved V3.4 frontend appearance and correct visual drift without changing the core player or project portal systems.

Do not remove or substantially rewrite the V3.4 parity layer unless the replacement has been visually checked against the approved V3.4 reference.

## Known-good implementation commits

The approved restoration was finalized through these commits:

- `4cf93250e43676971a7c290ad5e5b42e9e7b41e5` — Match WordPress frontend to approved V3.4 HTML reference
- `8eaa8f4b72a107aa0b2118b0790ee7bef7cc2fc7` — Load approved V3.4 HTML parity corrections

The first deployment completed successfully. The second commit contains the final loading/integration change for the approved parity corrections.

## Change rule going forward

Treat this version as the rollback/reference point for future visual work. New improvements should preserve the approved layout and identity unless a redesign is explicitly approved. Before replacing major homepage styling, compare the proposed result with this baseline so previously corrected visual drift is not accidentally reintroduced.

When troubleshooting future frontend problems, check the current implementation against this document and the approved V3.4 reference before making broad CSS/layout changes.
