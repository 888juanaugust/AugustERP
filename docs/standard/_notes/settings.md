## Behaviours

- Preferences are the one source of truth for every switch: the company's identity, the modules that are on, the default accounts the posting layer uses, the business rules, the aging basis, the attachments and extra columns. Every change is audited with the old and the new value.
- Access is by group: each group holds the five rights (view, create, update, delete, print) per screen plus the special rights (see cost, change selling price, see credit data, override credit limit, open a closed period, back-date, edit others' transactions, delete posted transactions, approve transactions, export). A user belongs to groups, may carry per-user grants or revocations, and is limited to the branches and warehouses assigned to them. A group's rights can be copied from another group.
- Six groups are seeded (Administrator, Accounting, Finance, Sales, Purchasing, Warehouse); the client reshapes them on the Access Groups screen.
- Numbering is configured per document type: a pattern of tokens (prefix, year, month, counter), the counter width, and when it resets; several series per type, one of them the default, each limited to chosen users or open to all.
- Print layouts are designed per document type: which blocks print, the heading, the footer, paper and orientation; one default per type.
- Approval rules (Transaction Approvers) say which documents need approval, from what amount, in which branch, for whose entries, by whom (users or groups) and under which condition. This release honours every condition as "any one of the approvers".
