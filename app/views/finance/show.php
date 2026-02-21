<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Finance Record</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" style="width: 200px;">Transaction Date :</th>
                                <td><?= date('F d, Y', strtotime($record['transaction_date'])) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Type :</th>
                                <td>
                                    <?php if ($record['transaction_type'] === 'income'): ?>
                                        <span class="badge bg-success">Income</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Expense</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Amount :</th>
                                <td>
                                    <strong class="fs-5 <?= $record['transaction_type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                        <?= $record['transaction_type'] === 'income' ? '+' : '-' ?>$<?= number_format($record['amount'], 2) ?>
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Category :</th>
                                <td><span class="badge bg-info"><?= ucfirst($record['category']) ?></span></td>
                            </tr>
                            <tr>
                                <th scope="row">Payment Method :</th>
                                <td><?= ucfirst(str_replace('_', ' ', $record['payment_method'])) ?></td>
                            </tr>
                            <?php if (!empty($record['description'])): ?>
                            <tr>
                                <th scope="row">Description :</th>
                                <td><?= nl2br(htmlspecialchars($record['description'])) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($record['reference_number'])): ?>
                            <tr>
                                <th scope="row">Reference Number :</th>
                                <td><?= htmlspecialchars($record['reference_number']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th scope="row">Recorded :</th>
                                <td><?= date('F d, Y, h:i A', strtotime($record['created_at'])) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

