<?php

/** Document and payment statuses, as badges. */
return [
    'fulfilment' => [
        'pending' => 'Pending',
        'partial' => 'Partial',
        'processed' => 'Processed',
        'closed' => 'Closed',
    ],
    'mail' => [
        'queued' => 'Queued',
        'sent' => 'Sent',
        'failed' => 'Failed',
        'skipped' => 'Skipped (already sent)',
    ],
    'payment' => [
        'unpaid' => 'Unpaid',
        'partial' => 'Partially paid',
        'partially_paid' => 'Partially paid',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
    ],
    'commission_basis' => ['sales_value' => 'Sales value', 'gross_profit' => 'Gross profit'],
    'filing' => ['draft' => 'Draft', 'exported' => 'Exported', 'numbered' => 'Numbered'],
    'giro' => ['outstanding' => 'Outstanding', 'cleared' => 'Cleared', 'bounced' => 'Bounced'],
    'return_type' => ['invoice' => 'Invoice', 'delivery' => 'Delivery', 'none' => 'No invoice', 'down_payment' => 'Down payment'],
    'approval' => ['not_required' => 'Not required', 'awaiting' => 'Awaiting approval', 'approved' => 'Approved', 'rejected' => 'Rejected'],
    'access_type' => ['operator' => 'Operator', 'administrator' => 'Administrator'],
    'period' => ['open' => 'Open', 'closed' => 'Closed'],
    'opname' => ['draft' => 'Draft', 'approved' => 'Approved', 'open' => 'Open', 'closed' => 'Closed', 'pending' => 'Pending', 'processed' => 'Processed'],
    'target_type' => ['per_item' => 'Per item', 'per_category' => 'Per item category', 'per_salesman' => 'Per salesperson', 'per_month' => 'Per month'],
    'budget_scope' => ['general' => 'General'],
    'asset_change' => ['data' => 'Data', 'revaluation' => 'Revaluation'],
    'contact' => ['customer' => 'Customer', 'vendor' => 'Vendor', 'employee' => 'Employee', 'other' => 'Other'],
    'payroll_type' => ['monthly' => 'Monthly', 'non_monthly' => 'Non-monthly'],
    'audit' => [
        'created' => 'Created', 'updated' => 'Updated', 'deleted' => 'Deleted', 'printed' => 'Printed', 'approved' => 'Approved', 'rejected' => 'Rejected',
        'imported' => 'Imported', 'bank_reconciled' => 'Bank reconciled', 'bank_statement_imported' => 'Bank statement imported',
        'coretax_pdf_attached' => 'Coretax PDF attached', 'depreciation_run' => 'Depreciation run', 'giro_bounced' => 'Giro bounced', 'giro_cleared' => 'Giro cleared',
        'period_closed' => 'Period closed', 'period_reopened' => 'Period reopened', 'preference_changed' => 'Preference changed', 'recurring_run' => 'Recurring run',
        'tax_filing_exported' => 'Tax file exported', 'tax_invoice_emailed' => 'Tax invoice emailed', 'tax_serial_cleared' => 'Tax serial cleared',
        'tax_serial_stored' => 'Tax serial stored', 'vat_return_saved' => 'VAT return saved',
    ],
    'document' => [
        'draft' => 'Draft',
        'posted' => 'Posted',
        'void' => 'Void',
    ],
];
