# TUFF BEATZ — Contracts, Quotes & Payments V6

Status: IMPLEMENTED

V6 connects the business side of a production engagement to the existing TUFF BEATZ Project Dashboard.

## Admin workflow

For each Production Request, TUFF BEATZ can create and maintain:
- Quote number
- Scope of work
- Project subtotal
- Discount
- Tax / fees
- Deposit percentage
- Quote expiration date
- Secure external checkout/payment URL
- Contract / project agreement terms
- Payment records, method and transaction/reference number

The system calculates project total, required deposit, amount paid and remaining balance.

## Client workflow

Authorized project members with Billing permission see a Business Office section inside the Project Dashboard. It includes:
- Quote and current status
- Scope of work
- Total, deposit, paid and balance amounts
- Agreement terms
- Quote + agreement approval control
- Payment history
- Secure payment button when a checkout URL has been configured
- Paid-in-full state

## Acceptance record

Approval records the authenticated WordPress user ID, display name, email, acceptance timestamp, quote version, contract version and a salted hash of the request IP. The raw IP is not stored by this feature.

Changing material quote terms invalidates the prior quote acceptance. Changing agreement text invalidates the prior contract acceptance.

## Payment architecture

V6 records payments in the TUFF BEATZ project workspace, but it does **not** process or store credit-card/bank credentials. The payment button uses an admin-configured external secure checkout URL. A future gateway integration can add verified webhooks and automatic payment reconciliation.

## Permission model

Project owner and TUFF BEATZ admin retain business access. Collaborators require the V5 `billing` permission to see quote, contract and payment information.

## Files
- `tuff-beatz/inc/project-commerce.php`
- `tuff-beatz/inc/project-dashboard.php`
- `tuff-beatz/assets/css/commerce.css`

The approved V3.4 public homepage remains unchanged; V6 assets load only in the authenticated Project Dashboard.
