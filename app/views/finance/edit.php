<?php
use App\Utilities\AssetHelper;
?>

<?= $this->render('components/module_form', [
    'title' => 'Edit Financial Record',
    'moduleName' => 'finance',
    'churchId' => $churchId,
    'formAction' => "/finance/{$churchId}/{$record['id']}",
    'breadcrumbs' => $breadcrumbs ?? [],
    'formFields' => [
        [
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
            'placeholder' => 'Enter transaction title',
            'value' => $record['title'] ?? '',
            'required' => true
        ],
        [
            'name' => 'transaction_type',
            'label' => 'Transaction Type',
            'type' => 'select',
            'options' => [
                ['value' => 'income', 'label' => 'Income'],
                ['value' => 'expense', 'label' => 'Expense']
            ],
            'value' => $record['transaction_type'] ?? '',
            'required' => true
        ],
        [
            'name' => 'amount',
            'label' => 'Amount',
            'type' => 'number',
            'placeholder' => '0.00',
            'value' => $record['amount'] ?? '',
            'min' => '0',
            'step' => '0.01',
            'required' => true
        ],
        [
            'name' => 'category',
            'label' => 'Category',
            'type' => 'text',
            'placeholder' => 'e.g., Tithes, Donations, Utilities',
            'value' => $record['category'] ?? '',
            'required' => true
        ],
        [
            'name' => 'transaction_date',
            'label' => 'Date',
            'type' => 'date',
            'value' => $record['transaction_date'] ?? '',
            'required' => true
        ],
        [
            'name' => 'description',
            'label' => 'Description',
            'type' => 'textarea',
            'placeholder' => 'Enter description (optional)',
            'value' => $record['description'] ?? '',
            'required' => false
        ]
    ],
    'csrf_token' => $csrf_token ?? '',
    'moduleIcon' => 'money',
    'formTitle' => 'Financial Record Details',
    'cancelUrl' => "/finance/{$churchId}/{$record['id']}",
    'submitText' => 'Update Financial Record'
]) ?>