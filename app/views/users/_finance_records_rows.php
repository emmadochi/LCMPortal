<?php
use App\Utilities\AssetHelper;
if (empty($records)):
?>
<tr>
    <td colspan="6" class="text-center text-muted py-4">No records for this period.</td>
</tr>
<?php else:
    foreach ($records as $record):
?>
<tr>
    <td><?= date('M d, Y', strtotime($record['transaction_date'])) ?></td>
    <td>
        <?php if ($record['transaction_type'] === 'income'): ?>
            <span class="badge bg-success">Income</span>
        <?php else: ?>
            <span class="badge bg-danger">Expense</span>
        <?php endif; ?>
    </td>
    <td>
        <strong class="<?= $record['transaction_type'] === 'income' ? 'text-success' : 'text-danger' ?>">
            <?= $record['transaction_type'] === 'income' ? '+' : '-' ?>₦<?= number_format((float) $record['amount'], 2) ?>
        </strong>
    </td>
    <td><span class="badge bg-info"><?= ucfirst(htmlspecialchars($record['category'] ?? 'other')) ?></span></td>
    <td><?= htmlspecialchars($record['description'] ?: '—') ?></td>
    <td>
        <a href="<?= AssetHelper::url('finance/' . $record['id']) ?>" class="btn btn-sm btn-outline-primary">
            <i data-feather="eye" class="icon-sm"></i>
        </a>
    </td>
</tr>
<?php
    endforeach;
endif;
