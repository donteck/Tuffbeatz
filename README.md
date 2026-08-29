# TUFF BEATZ

Official WordPress theme repository for **TUFF BEATZ — The Producer Identity of Emmanuel Tuffet**.

## Repository structure

- `tuff-beatz/` — installable WordPress theme source
- `.github/workflows/deploy-hestia.yml` — automatic deployment to the Hestia server

## Development workflow

1. Edit files inside `tuff-beatz/`.
2. Commit your changes.
3. Push to the `main` branch.
4. GitHub Actions automatically backs up the current production theme and deploys the new version to Hestia.

## Required GitHub repository secrets

Go to:

`GitHub Repository → Settings → Secrets and variables → Actions → New repository secret`

Create these secrets:

- `HESTIA_HOST` — Hestia server hostname or public IP
- `HESTIA_PORT` — SSH port, normally `22`
- `HESTIA_USER` — Hestia/SSH user that owns the website
- `HESTIA_SSH_KEY` — private SSH key used by GitHub Actions
- `HESTIA_THEME_PATH` — absolute path to the live theme, for example:
  `/home/USERNAME/web/tuffbeatz.com/public_html/wp-content/themes/tuff-beatz`
- `HESTIA_BACKUP_PATH` — backup directory, for example:
  `/home/USERNAME/web/tuffbeatz.com/private/theme-backups`

## Hestia setup

On the Hestia server:

1. Make sure SSH access is enabled for the website user.
2. Add the deployment public key to `~/.ssh/authorized_keys`.
3. Confirm the user owns the WordPress files.
4. Confirm `rsync` is installed.
5. Keep the theme folder name as `tuff-beatz`.

## Safety

Every deployment creates a timestamped `.tar.gz` backup of the current theme before rsync replaces the files.

The deployment workflow only syncs the `tuff-beatz/` theme folder. It does not touch the WordPress database, uploads, plugins, or core WordPress files.

## WordPress music player

Playable tracks are managed under `WordPress Dashboard → Projects`.

Each project supports Artist, MP3/audio preview URL, Stream/smart link, Buy/license link, and Featured image. Once an audio URL is added, the track automatically appears in the persistent TUFF BEATZ player and queue.
