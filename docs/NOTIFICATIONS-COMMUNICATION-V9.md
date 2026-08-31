# TUFF BEATZ V9 — Notifications & Client Communication

Status: IMPLEMENTED

V9 adds an authenticated in-project Communication Center and event-driven email notifications while preserving the approved V3.4 public website.

## Client experience
- Project Dashboard receives a Notifications tab.
- Notifications are stored per project in `_tb_notifications_v9`.
- Each notification records type, title, message, timestamp and per-user read state.
- Clients and authorized project members can mark all project notifications read.
- Notification emails include a direct authenticated project-workspace link.

## Automated events
V9 watches project workflow metadata and can notify on:
- Project review started
- Project approved
- Production started
- Revision stage
- Mastering started
- Final delivery ready
- Project completed
- Deposit due
- Balance due
- Paid in full
- Mix approved

Notifications also enter the V8 activity timeline when available.

## Recipients and privacy
Email recipients are limited to the WordPress project owner and linked project collaborators with known email addresses. The in-app center uses the existing project authorization model; it is not public.

## Email delivery caveat
V9 uses WordPress `wp_mail()`. Actual inbox delivery therefore depends on the server's mail configuration. For production-grade deliverability, connect WordPress to a transactional SMTP/email provider and verify SPF, DKIM and DMARC for the sending domain.

## Scope
V9 does not send SMS, WhatsApp or push notifications and does not add third-party marketing tracking. It does not expose project data publicly.
