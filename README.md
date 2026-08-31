# TUFF BEATZ

Official WordPress theme repository for **TUFF BEATZ — The Producer Identity of Emmanuel Tuffet**.

**Brand:** SOUND. PURPOSE. LEGACY.

## Current production theme — V3.4.0

The `tuff-beatz/` folder is now the source of truth for the live WordPress theme.

V3.4 keeps the simple V2.2-style gold circular launcher and adds the premium V3 music experience after expansion.

Current behavior:

- Simple collapsed gold player button at bottom-left
- Click to open the premium expanded player
- Real WordPress MP3 playback from Projects
- Previous / play-pause / next controls
- Waveform-style seek/progress display
- Track information, time and volume controls
- Genre, BPM and Key metadata
- Scroll up/down collapses the player while music continues
- Clicking a release opens the player and starts that track
- Cinematic full-screen Now Playing view
- Full-width V2.2-style immersive playlist/queue
- Real TUFF BEATZ logo in playlist branding
- Black + metallic-gold TUFF BEATZ visual identity
- Mobile responsive player and queue

Detailed specifications and design history are stored under `docs/`.

## Repository structure

- `tuff-beatz/` — production WordPress theme source
- `docs/V3.4-PLAYER-SPEC.md` — approved V3.4 player/queue specification
- `docs/VERSION-HISTORY.md` — website/player evolution and approved decisions
- `.github/workflows/deploy-hestia.yml` — automatic GitHub → Hestia deployment workflow

## Live editing workflow

1. Edit files inside `tuff-beatz/`.
2. Commit changes to `main`.
3. GitHub Actions validates the SSH connection.
4. The current Hestia theme is backed up.
5. `rsync` deploys the new theme files to Hestia.
6. WordPress file permissions are normalized.
7. Refresh the live site to view the update.

The GitHub → Hestia deployment connection was successfully tested and is active.

## WordPress music data

Playable tracks are managed under `WordPress Dashboard → Projects`.

Each project supports:

- Artist
- MP3 / Audio Preview URL
- Stream / Smart Link
- Buy / License Link
- Featured Image / Cover Art
- Genre
- BPM
- Key

Once an audio URL is added, the track automatically becomes available to the V3.4 player and queue.

## Deployment safety

The workflow backs up the current live theme before deployment and syncs only the `tuff-beatz/` theme folder. It does not intentionally modify the WordPress database, uploads, plugins or WordPress core.

The live `assets/images/` directory and theme screenshot are currently preserved during automated `rsync` deployments so existing production artwork is not deleted.

Server credentials, host/IP information, usernames, SSH private keys and other sensitive Hestia values must **never** be committed to this public repository. Those values remain private GitHub Actions repository secrets.

## Design provenance

The commercial Kentha package was reviewed only for interaction ideas such as a persistent player and immersive playlist. TUFF BEATZ uses its own black/gold visual identity and original implementation; proprietary Kentha code/assets are not part of the TUFF BEATZ theme.
