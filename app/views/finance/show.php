<?php
use App\Utilities\AssetHelper;
use App\Utilities\Helper;
?>

<?= $this->render('components/module_show', [
    'title' => 'Financial Record Details',
    'moduleName' => 'finance',
    'churchId' => $churchId,
    'record' => $record,
    'recordTitle' => $record['title'] ?? 'Financial Record',
    'breadcrumbs' => $breadcrumbs ?? [],
    'details' => [
        ['field' => 'title', 'label' => 'Title'],
        ['field' => 'transaction_type', 'label' => 'Type', 'type' => 'status'],
        ['field' => 'amount', 'label' => 'Amount', 'type' => 'currency'],
        ['field' => 'category', 'label' => 'Category'],
        ['field' => 'transaction_date', 'label' => 'Date', 'type' => 'date'],
        ['field' => 'payment_method', 'label' => 'Payment Method'],
        ['field' => 'reference_number', 'label' => 'Reference Number']
    ],
    'descriptionField' => 'description',
    'showEdit' => true,
    'showDelete' => true,
    'moduleIcon' => 'money',
    'recordSubtitle' => 'Financial transaction details',
    'csrf_token' => $csrf_token ?? '',
    'relatedActions' => [
        ['label' => 'View All Financial Records', 'url' => "/finance/{$churchId}", 'icon' => 'file'],
        ['label' => 'Create New Record', 'url' => "/finance/{$churchId}/create", 'icon' => 'plus']
    ]
]) ?>