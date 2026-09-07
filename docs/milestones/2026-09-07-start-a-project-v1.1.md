# TUFF BEATZ Milestone — Start a Project V1.1

**Date:** September 7, 2026  
**Canonical intake:** https://tuffbeatz.com/start-a-project/

## What changed

The existing Start a Project page now writes new public submissions into the canonical Studio OS `tb_request` pipeline instead of creating standalone `tb_project` draft records.

A new isolated bridge module was added:

`tuff-beatz/inc/public-intake-bridge.php`

It maps the public service labels to canonical Studio OS service keys, creates a `tb_request`, stores client/artist/contact/service/genre/budget/timeline/reference/project-brief metadata, marks the request `new`, and identifies the source as `start-a-project`.

## Producer Command Center integration

Because the Producer Command Center already consumes `tb_request` records, new public Start a Project submissions now enter the existing Studio Pipeline and Attention Queue automatically as new intake work.

## Producer CRM integration

The Producer CRM now includes a **Public Intake Queue** for unconverted public leads in `new` or `reviewing` status. It shows client/artist identity, email, service, budget, timeline and direct Review/Manage actions.

This queue is intentionally a pre-client pipeline. Anonymous public intake does not automatically create a WordPress client account.

## Status

- Public Intake Bridge: **code-complete**
- Start a Project → canonical `tb_request`: **code-complete**
- Producer Command Center intake visibility: **architecture-connected**
- Producer CRM Public Intake Queue: **code-complete**
- Deployment for canonical intake connection: **successful** (GitHub Actions run #326)
- CRM queue deployment: **in progress at time of documentation** (run #327)
- Browser/runtime submission test: **not yet verified**
- CRM visual/runtime verification: **not yet verified**

## Guardrails preserved

- Existing public Start a Project URL retained.
- V3.4 homepage baseline untouched.
- `main.css` untouched.
- Site Editor remains paused.
- Private Studio OS security boundaries unchanged.
- Public intake does not automatically grant client access or create a payment obligation.

## Next milestone

After browser/runtime verification, the next commercial step is:

**Producer Review → Qualify Intake → Create CRM Opportunity → Proposal / Quote**
