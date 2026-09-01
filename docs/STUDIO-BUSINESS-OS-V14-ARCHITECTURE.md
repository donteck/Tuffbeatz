# TUFF BEATZ — Studio Business OS V14 Architecture

**Phase:** V14.0 Architecture Foundation  
**V13 code baseline:** `fc320fb6d8eade8b6824cd21439e2a4457040c15`  
**Rule:** V13 production hardening remains frozen. V14 business capabilities extend the platform without silently rewriting the frozen V13 security/delivery core.

## Mission

Turn TUFF BEATZ from a production workspace into an end-to-end studio operating system that manages the commercial relationship around production.

`Lead → Client → Opportunity → Quote → Contract → Deposit → Project → Production → Invoice → Payment → Delivery → Retention`

## Architecture layers

### 1. Lead & Opportunity Engine
- Prospective client records
- Lead source
- Service interest
- Estimated value
- Pipeline stage
- Next action / follow-up date
- Win/loss state and reason

### 2. Client 360
Extend the existing Client CRM into a commercial relationship view:
- Active/completed projects
- Lifetime quoted value
- Lifetime paid value
- Outstanding balance
- Files and delivery history
- Open opportunities
- Last interaction
- Next follow-up
- Relationship status

### 3. Sales Pipeline
Canonical business stages:
- New Lead
- Qualified
- Discovery
- Proposal
- Contract
- Deposit Due
- Won / Project Created
- Lost

The sales pipeline is separate from the V13 production workflow. A sales-stage change must never directly mutate production status unless an explicit conversion action creates/links a project.

### 4. Quote / Proposal Engine
Build on existing quote/commerce data rather than duplicating it:
- Versioned proposals
- Services / line items
- Discounts / taxes where configured
- Expiration
- Client approval state
- Conversion to contract/invoice/project

### 5. Contract Layer
- Contract version
- Acceptance state
- Accepted by / timestamp
- Link to opportunity and project
- Future certified e-sign integration boundary

Do not represent current WordPress acceptance as certified e-signature unless a certified provider is integrated.

### 6. Billing & Accounts Receivable
Build on existing V7 invoice/payment reconciliation:
- Invoice ledger
- Due / overdue / partially paid / paid
- Deposit requirements
- Outstanding receivables
- Aging buckets
- Payment history
- Receipts

Native payment processing is a later integration; V14 must not pretend reconciliation is a payment gateway.

### 7. Business Command Center
Producer-only executive view:
- Pipeline value
- Weighted pipeline
- Quotes awaiting decision
- Contracts awaiting acceptance
- Deposits due
- Accounts receivable
- Overdue invoices
- Active production value
- Completed revenue
- Follow-ups due
- Recent commercial activity

### 8. Conversion Engine
A controlled conversion connects business and production:

`Opportunity WON → approved quote/contract → client account → tb_request project → initial invoice/deposit → V13 Studio OS production workflow`

Conversion must be idempotent: one opportunity cannot accidentally create duplicate projects/invoices.

### 9. Retention & Relationship Engine
After delivery:
- Follow-up tasks
- Repeat-client indicator
- Reorder / new-project opportunity
- Referral source tracking
- Client lifetime value
- Dormant-client reminders

### 10. Business Activity Ledger
Commercial events should be normalized separately but linkable to project activity:
- lead_created
- lead_qualified
- proposal_sent
- proposal_approved
- contract_accepted
- invoice_created
- payment_recorded
- opportunity_won
- opportunity_lost
- project_converted
- followup_due

## Canonical data boundaries

### Existing objects to preserve
- `tb_request` — production project/request
- WordPress user — client identity
- V7 invoice/payment project metadata — current billing foundation
- V10 Client CRM — current relationship summary foundation
- V13 Vault / Workflow / Delivery / Acceptance — frozen production core

### New V14 object
Use a dedicated `tb_opportunity` custom post type for pre-production commercial opportunities. Do not overload `tb_request` with lead/sales states.

Recommended opportunity metadata:
- `_tb_v14_client_id`
- `_tb_v14_contact_name`
- `_tb_v14_contact_email`
- `_tb_v14_service`
- `_tb_v14_source`
- `_tb_v14_stage`
- `_tb_v14_estimated_value`
- `_tb_v14_probability`
- `_tb_v14_next_action`
- `_tb_v14_followup_at`
- `_tb_v14_linked_request_id`
- `_tb_v14_lost_reason`
- `_tb_v14_activity`

## Security boundary

V14 business administration is producer/admin only by default. Client-facing proposal/contract/payment views must use explicit ownership or token/authenticated access rules. V14 may call V13 APIs but must not weaken V13 permissions, final-delivery authority, vault protection, integrity checks, or completion locks.

## Version roadmap

- **V14.0** Architecture Foundation / data boundaries
- **V14.1** Opportunity Engine
- **V14.2** Business Command Center
- **V14.3** Client 360 commercial intelligence
- **V14.4** Quote / Proposal workflow
- **V14.5** Contract workflow
- **V14.6** Accounts Receivable command center
- **V14.7** Opportunity → Project conversion engine
- **V14.8** Follow-up / retention engine
- **V14.9** Business notifications & automation
- **V14.10** Business OS QA / release candidate

## Non-goals for V14.0

V14.0 is an architecture/data-contract phase. It does not change the public homepage, does not alter the V13 final-delivery chain, does not add a payment gateway, and does not claim certified e-signatures.
