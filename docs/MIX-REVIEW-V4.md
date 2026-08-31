# TUFF BEATZ — Mix Review & Revision V4

Status: IMPLEMENTED

Mix Review V4 extends the Private File Vault and Project Dashboard with version-specific audio review.

## Workflow

1. TUFF BEATZ uploads an audio delivery and labels it Mix V1, Mix V2, Mix V3, Master V1, etc.
2. The version appears in the authenticated Project Dashboard under Mix Review.
3. The client plays the secure audio directly in the workspace.
4. The client can capture the current playback position and submit a timestamped revision note.
5. Notes remain associated with the exact vault file/version and can be clicked to jump playback to that time.
6. A revision request changes that version to `revision-requested` and keeps the project in Revisions.
7. The client can approve a mix. Approval records the approved file and automatically moves the project to Mastering.
8. Previous versions and their feedback remain in project history.

## Security

Audio playback uses the existing authenticated Private File Vault download endpoint. Project authorization is still enforced server-side. Review and approval forms use project/file-specific WordPress nonces.

## Files

- `tuff-beatz/inc/project-dashboard.php`
- `tuff-beatz/page-project-dashboard.php`
- `tuff-beatz/assets/js/mix-review.js`
- `tuff-beatz/assets/css/mix-review.css`

## Preserved baseline

The approved V3.4 public frontend is not redesigned by this feature. Mix Review styles load only on the Project Dashboard.
