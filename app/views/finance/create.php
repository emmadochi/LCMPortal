<?php
use App\Utilities\AssetHelper;
?>

<?= $this->render('components/module_form', [
    'title' => 'Create Financial Record',
    'moduleName' => 'finance',
    'churchId' => $churchId,
    'formAction' => "/finance/{$churchId}",
    'breadcrumbs' => $breadcrumbs ?? [],
    'formFields' => [
        [
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
            'placeholder' => 'Enter transaction title',
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
            'required' => true
        ],
        [
            'name' => 'amount',
            'label' => 'Amount',
            'type' => 'number',
            'placeholder' => '0.00',
            'min' => '0',
            'step' => '0.01',
            'required' => true
        ],
        [
            'name' => 'category',
            'label' => 'Category',
            'type' => 'text',
            'placeholder' => 'e.g., Tithes, Donations, Utilities',
            'required' => true
        ],
        [
            'name' => 'transaction_date',
            'label' => 'Date',
            'type' => 'date',
            'required' => true
        ],
        [
            'name' => 'description',
            'label' => 'Description',
            'type' => 'textarea',
            'placeholder' => 'Enter description (optional)',
            'required' => false
        ]
    ],
    'csrf_token' => $csrf_token ?? '',
    'moduleIcon' => 'money',
    'formTitle' => 'Financial Record Details',
    'cancelUrl' => "/finance/{$churchId}",
    'submitText' => 'Save Financial Record',
    'showSaveAndNew' => true
]) ?>