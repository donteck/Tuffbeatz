# TUFF BEATZ — Invoices, Receipts & Payment Reconciliation V7

Status: IMPLEMENTED

V7 extends the TUFF BEATZ Business Office with invoice lifecycle, payment receipts and a provider-neutral signed reconciliation endpoint.

## Invoice lifecycle

After an accepted V6 quote/contract:
- A deposit invoice is generated automatically using the configured deposit percentage.
- Once recorded payments reach the deposit amount, a balance invoice is generated automatically.
- Each invoice receives a stable TUFF BEATZ invoice number.
- Admin can configure invoice due days per project.
- Invoice states include due, partially paid, overdue, partially paid overdue and paid.

## Receipts

Every payment receives:
- Internal payment ID
- TUFF BEATZ receipt number
- Amount
- Date
- Payment method
- Reference / transaction data

Legacy V6 payment records are upgraded with receipt metadata automatically when accessed.

## Client documents

Authorized project members with Billing permission can open each invoice or receipt from the Project Dashboard. Documents are generated as printer-friendly authenticated HTML and include a **Print / Save PDF** control. This intentionally avoids storing generated PDFs publicly.

## Automatic reconciliation

V7 adds a signed provider-neutral endpoint:

`/wp-json/tuff-beatz/v1/payment-webhook`

The endpoint remains disabled until `TUFF_BEATZ_PAYMENT_WEBHOOK_SECRET` is configured securely in the server environment or `wp-config.php`.

Requests must provide an `X-Tuff-Signature` HMAC-SHA256 signature over the raw JSON body. Required payload data:
- `project_id`
- `amount`
- `transaction_id`
- `provider`
- `status` = `paid`

Transaction IDs are deduplicated per provider. A successful event records the payment, creates a receipt, updates deposit/balance/payment status, and can trigger balance invoice creation.

## Important gateway note

This is a secure reconciliation foundation, not native Stripe or PayPal webhook verification. Native provider integration should verify each provider's official webhook signature format and event semantics before mapping the event into the V7 reconciliation engine. No card or bank credentials are stored by TUFF BEATZ.

## Files
- `tuff-beatz/inc/project-invoicing.php`
- `tuff-beatz/functions.php`
- `tuff-beatz/assets/css/invoicing.css`

The approved V3.4 public homepage remains unchanged. V7 interface assets load only inside the authenticated Project Dashboard.
