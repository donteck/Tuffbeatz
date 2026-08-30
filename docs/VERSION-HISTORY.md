# TUFF BEATZ Website Version History

## V1

Initial custom WordPress theme and standalone HTML front page. Established the black/gold luxury producer identity, hero, genres, services, recent work, streaming links, contact experience and responsive navigation.

## V2

Added a persistent music-player concept inspired by premium music themes but implemented with original TUFF BEATZ code and branding. WordPress Projects gained Artist, Audio Preview / MP3 URL, Stream / Smart Link and Buy / License Link fields. Tracks with audio automatically populate the player and queue.

## V2.1

First click-to-open/collapsed player experiment. This package had malformed appended CSS and was superseded.

## V2.2

Fixed collapsed-player baseline. The player starts as a small gold circular launcher. Clicking opens the full bottom player. Scrolling collapses the interface while playback continues. Asset cache-busting was added to reduce stale WordPress CSS/JS.

## V3

Introduced the premium music-experience concept: waveform-style timeline, expanded transport controls, queue, full-screen Now Playing experience, track metadata and improved mobile behavior.

## V3.1

Experimented with a larger informational mini-player. This mini-player was rejected in favor of the simpler V2.2 launcher.

## V3.2

Approved simple-player direction: keep the V2.2 gold circular launcher while retaining the advanced V3 features after expansion.

## V3.3

Replaced the narrow side queue with the immersive V2.2-style full-width playlist drawer. Added TUFF BEATZ playlist branding and active-track presentation.

## V3.4 — Current approved HTML baseline

Replaced the temporary `TB` playlist branding placeholder with the actual TUFF BEATZ logo. All V3.2/V3.3 behavior remains: simple collapsed launcher, advanced expanded player, auto-collapse on scroll without stopping playback, waveform-style controls, full-screen Now Playing experience, and immersive full-width queue.

## Deployment status

GitHub repository: `donteck/Tuffbeatz`.

Hestia deployment workflow exists in the repository, but production Hestia connection details must remain in GitHub Actions secrets and must not be committed to public documentation or source files.
