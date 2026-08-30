# TUFF BEATZ

Official WordPress theme repository for **TUFF BEATZ — The Producer Identity of Emmanuel Tuffet**.

**Brand:** SOUND. PURPOSE. LEGACY.

## Current approved design baseline — V3.4

The approved music experience keeps the simple V2.2-style gold circular player launcher while adding the advanced V3 experience after expansion.

Approved behavior:

- Simple collapsed gold player button at bottom-left
- Click to open the premium expanded player
- Previous / play-pause / next controls
- Waveform-style seek/progress display
- Track information, time and volume controls
- Scroll up/down collapses the player while music continues
- Clicking a release opens the player and starts that track
- Immersive full-screen Now Playing view
- Full-width V2.2-style playlist/queue instead of a narrow side panel
- Real TUFF BEATZ logo in the playlist branding instead of the temporary `TB` placeholder
- Black + metallic-gold TUFF BEATZ visual identity

Detailed specifications and design history are stored under `docs/`.

## Repository structure

- `tuff-beatz/` — installable WordPress theme source
- `docs/V3.4-PLAYER-SPEC.md` — approved V3.4 player/queue specification
- `docs/VERSION-HISTORY.md` — website/player evolution and approved decisions
- `.github/workflows/deploy-hestia.yml` — automatic deployment workflow for the Hestia server

## Development workflow

1. Edit files inside `tuff-beatz/`.
2. Commit the changes.
3. Push to the `main` branch.
4. Once Hestia secrets are configured, GitHub Actions can back up the production theme and deploy the updated theme.

## WordPress music data

Playable tracks are managed under `WordPress Dashboard → Projects`.

Each project supports Artist, MP3/audio preview URL, Stream/smart link, Buy/license link and Featured image. Once an audio URL is added, the track can populate the TUFF BEATZ player and queue dynamically.

## Deployment safety

The deployment workflow is designed to sync only the `tuff-beatz/` theme folder. It does not intentionally modify the WordPress database, uploads, plugins or WordPress core.

Server credentials, host/IP information, usernames, SSH private keys and other sensitive Hestia values must **never** be committed to this public repository. Configure those values only as private GitHub Actions repository secrets when production deployment is enabled.

## Design provenance

The commercial Kentha package was reviewed only for interaction ideas such as a persistent player and immersive playlist. TUFF BEATZ uses its own black/gold visual identity and original implementation; proprietary Kentha code/assets are not part of the TUFF BEATZ theme.
