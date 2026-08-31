TUFF BEATZ V3.4.0
=================

Production WordPress theme for TUFF BEATZ — The Producer Identity of Emmanuel Tuffet.
Brand: SOUND. PURPOSE. LEGACY.

PLAYER EXPERIENCE
- Simple gold circular launcher when collapsed.
- Click launcher to open the premium full-width player.
- Real HTML5 audio playback from published WordPress Projects.
- Previous / Play-Pause / Next controls.
- Waveform-style progress seeking.
- Volume control.
- Track cover art, artist, genre, BPM and key.
- Queue button opens an immersive full-width V2.2-style playlist drawer.
- Queue branding uses the TUFF BEATZ logo preserved on the live Hestia theme.
- Clicking the player cover opens a cinematic full-screen Now Playing view.
- Scrolling automatically collapses the player interface while audio keeps playing.
- Mobile responsive player and playlist layout.

PROJECT FIELDS
Dashboard > Projects supports:
- Artist
- Audio Preview / MP3 URL
- Stream / Smart Link
- Buy / License Link
- Featured Image / Cover Art
- Genre
- BPM
- Key

DEPLOYMENT
The main branch is connected to Hestia through GitHub Actions.
Each theme-code deployment:
1. Connects to Hestia over SSH.
2. Backs up the current live theme.
3. Deploys the tuff-beatz/ folder with rsync.
4. Preserves the live assets/images/ directory and screenshot.
5. Normalizes WordPress file permissions.

SECURITY
Never commit Hestia credentials, private SSH keys, server secrets or passwords to the repository. Production connection values are stored only as private GitHub Actions repository secrets.

NOTE ON CONTINUOUS PLAYBACK
Playback continues while scrolling and interacting within the currently loaded page. Normal full WordPress page navigation reloads browser audio. True cross-page continuous playback can be added later with PJAX/SPA-style navigation.

RESTORE STATUS
This repository was intentionally restored to the approved TUFF BEATZ V3.4 production baseline on August 31, 2026.
