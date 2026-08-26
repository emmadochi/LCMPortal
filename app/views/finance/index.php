<?php
use App\Utilities\AssetHelper;
use App\Utilities\Helper;
?>

<?= $this->render('components/module_index', [
    'title' => 'Financial Management',
    'moduleName' => 'finance',
    'churchId' => $churchId,
    'records' => $records ?? [],
    'breadcrumbs' => $breadcrumbs ?? [],
    'tableColumns' => [
        ['field' => 'title', 'label' => 'Title'],
        ['field' => 'transaction_type', 'label' => 'Type', 'format' => function($record) {
            $class = $record['transaction_type'] === 'income' ? 'success' : 'danger';
            $icon = $record['transaction_type'] === 'income' ? 'trending-up' : 'trending-down';
            return '<span class="badge bg-' . $class . '"><i class="bx bx-' . $icon . ' me-1"></i>' . 
                   ucfirst($record['transaction_type']) . '</span>';
        }],
        ['field' => 'amount', 'label' => 'Amount', 'format' => function($record) {
            return '$' . number_format($record['amount'] ?? 0, 2);
        }],
        ['field' => 'category', 'label' => 'Category'],
        ['field' => 'transaction_date', 'label' => 'Date', 'format' => function($record) {
            return date('M j, Y', strtotime($record['transaction_date'] ?? ''));
        }]
    ],
    'showFilters' => true,
    'showExport' => true,
    'showCreate' => true,
    'showEdit' => true,
    'showDelete' => true,
    'moduleIcon' => 'money',
    'description' => 'Manage church financial records and transactions',
    'csrf_token' => $csrf_token ?? ''
]) ?>