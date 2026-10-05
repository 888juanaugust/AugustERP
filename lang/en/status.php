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
        'partially_paid' => 'Partially paid',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
    ],
    'document' => [
        'draft' => 'Draft',
        'posted' => 'Posted',
        'void' => 'Void',
    ],
];
