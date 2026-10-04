# Tax

Module group `tax`. 3 screens in the standard menu.

## Behaviours

- Output tax invoices export to the tax office's bulk-import XML; the older CSV layout stays selectable so an earlier filing can be reproduced. The serial numbers the tax office returns are pasted back onto the invoices.
- Emailing tax invoices to customers is planned for a later release.

## Screens

- [e-Tax Invoice Export](#e-tax-invoice-export)
- [Email Tax Invoice](#email-tax-invoice)
- [Legacy e-Tax Export](#legacy-e-tax-export)

## e-Tax Invoice Export

Menu key `company__efaktur-ctas` · module `tax` · switched by Preferences → Features → Tax

### Filters and inputs

| Field | Column | Type | Required |
|---|---|---|---|
| Tax | `kind` | select |  |
| Month | `month` | select |  |
| Year | `year` | select |  |
| From day | `day_from` | select |  |
| To day | `day_to` | select |  |
| Branch | `branch_id` | select |  |
| Search | `search` | text |  |

### List

**Columns:** Tax date · Transaction No. · Tax invoice No. · Tax base (DPP) · VAT · Document · Status · Tax ID · Name

**Actions:** Export selected

## Email Tax Invoice

Menu key `customer__efaktur-send` · module `tax` · switched by Preferences → Features → Tax

Planned for a later release. Emailing tax invoices to customers is outside the first release; the exported file and the serial numbers are the record. Send the PDF from the invoice list when printing lands.

## Legacy e-Tax Export

Menu key `company__efaktur-online` · module `tax` · switched by Preferences → Features → Tax

### Filters and inputs

| Field | Column | Type | Required |
|---|---|---|---|
| Tax | `kind` | select |  |
| Month | `month` | select |  |
| Year | `year` | select |  |
| From day | `day_from` | select |  |
| To day | `day_to` | select |  |
| Branch | `branch_id` | select |  |
| Search | `search` | text |  |

### List

**Columns:** Tax date · Transaction No. · Tax invoice No. · Tax base (DPP) · VAT · Document · Status · Tax ID · Name

**Actions:** Export selected

