# Roadmap

The template is complete as a standard: every module group has its screens (90 of 91
built; see `docs/standard/README.md`), its tests and its documentation. What follows is
what the next releases add, in rough order. A client's own needs go in that client's
repository, never here, unless they are right for every client.

| # | Release | What it adds | Done when |
|---|---|---|---|
| 1 | Payroll completion (done 2026-10) | Payroll runs from salary components with BPJS and income tax Art. 21 (TER, the year in the last month), and the two Article 21 forms (the monthly return and the A1 withholding slips) | Both forms produce the tax office's file for a demo payroll; the payroll module's defaults seed their accounts |
| 2 | Email tax invoice | Sending a customer its tax invoice by email once the serial number is back, with the mail log on the invoice | The placeholder screen is real; a test sends through the mail fake |
| 3 | Indonesian UI | `lang/id.json` ships with the template, covering every string the panels show (number and date formats already follow Preferences) | Switching the locale to Indonesian leaves no English on any screen |
| 4 | Multiple currencies (done 2026-10) | Documents in a foreign currency with a rate per document and realised differences on settlement, behind the Multiple currencies switch | A sales invoice in USD settles in IDR with the difference posted |
| 5 | Client extensions guide | A worked example of a client module under `app/Client` (resources, migrations, seeders, notes), merged from a template update without conflict | The example lives in a demo client repository and its merge is tested in CI |

Not planned: a payment gateway, bank integrations, marketplace links, an app store, financing
programs, AI analysis. Those were vendor services of the original product and are not part of
the standard.
