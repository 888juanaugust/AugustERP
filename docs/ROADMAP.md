# Roadmap

The template is complete as a standard: every module group has its screens (86 of 89
built; see `docs/standard/README.md`), its tests and its documentation. What follows is
what the next releases add, in rough order. A client's own needs go in that client's
repository, never here, unless they are right for every client.

| # | Release | What it adds | Done when |
|---|---|---|---|
| 1 | Payroll completion | Payroll runs from salary components with the statutory deductions, and the two Article 21 income-tax forms (the annual return and the withholding slips) that today are placeholders | Both forms produce the tax office's file for a demo payroll; the payroll module's defaults seed their accounts |
| 2 | Email tax invoice | Sending a customer its tax invoice by email once the serial number is back, with the mail log on the invoice | The placeholder screen is real; a test sends through the mail fake |
| 3 | Locale number and date formats | The Number format, Date format and decimals preferences, today marked reserved, drive `Format`; `lang/id.json` ships with the template | A `Format` test passes under both conventions; the Indonesian UI is complete |
| 4 | Approval conditions | The two-approver and in-order conditions of Transaction Approvers, today honoured as "any one" | An order under an in-order rule waits for each approver in turn |
| 5 | Multiple currencies | Documents in a foreign currency with a rate per document and realised differences on settlement, behind the Multiple currencies switch | A sales invoice in USD settles in IDR with the difference posted |
| 6 | Client extensions guide | A worked example of a client module under `app/Client` (resources, migrations, seeders, notes), merged from a template update without conflict | The example lives in a demo client repository and its merge is tested in CI |

Not planned: a payment gateway, bank integrations, marketplace links, an app store, financing
programs, AI analysis. Those were vendor services of the original product and are not part of
the standard.
