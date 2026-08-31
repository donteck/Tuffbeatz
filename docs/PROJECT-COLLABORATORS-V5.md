# TUFF BEATZ — Project Collaborators & Permissions V5

Status: IMPLEMENTED

Project Collaborators V5 expands the TUFF BEATZ Production Portal from a single-client workspace into a multi-person music-industry project environment.

## Supported project roles

Artist, Manager, Producer, Songwriter, Composer, Musician, Engineer, Label / A&R, Publisher, Band Member, Assistant, and other collaborators.

## Permission model

Each collaborator can be granted any combination of:

- View Project
- Upload Files
- Messages
- Mix Review
- Approve Mix
- View Payments

The project owner and TUFF BEATZ administrators retain full access.

## Invitation flow

The project owner or TUFF BEATZ can invite a collaborator by name and email from the Project Dashboard. Existing WordPress users are linked to the project. If the email is new, a restricted TUFF BEATZ client account is created and the invitee is instructed to set a password through the portal's password-reset flow.

## Access controls

Server-side checks now protect project viewing, file uploads, project messaging, timestamped mix-review notes, and mix approval. Billing metadata is masked for collaborators without the View Payments permission. Dashboard controls are also hidden when the current collaborator does not have the corresponding permission.

## Multi-party use cases

A label can include its artist, manager, producer, engineer, and A&R on one project. A band can include several members while keeping payment details restricted to management. A songwriter or outside producer can upload files or participate in review without gaining approval or billing authority.

## Files

- `tuff-beatz/inc/project-collaborators.php`
- `tuff-beatz/inc/project-dashboard.php`
- `tuff-beatz/assets/css/collaborators.css`

## Public-site baseline

This feature is scoped to authenticated project workspaces and does not redesign the approved V3.4 public homepage.
