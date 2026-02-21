<?php
use App\Utilities\AssetHelper;
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create Finance Record</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= AssetHelper::url('finance') ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <?php if (!empty($churchId)): ?>
                        <input type="hidden" name="church_id" value="<?= (int)$churchId ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Unit</label>
                                <select class="form-select" id="unit_id" name="unit_id">
                                    <option value="">Whole church / General (no unit)</option>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?= $unit['id'] ?>" 
                                            <?= (isset($_POST['unit_id']) && $_POST['unit_id'] == $unit['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($unit['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_type" class="form-label">Transaction Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="transaction_type" name="transaction_type" required>
                                    <option value="">Select Type...</option>
                                    <?php foreach ($transactionTypes as $type): ?>
                                        <option value="<?= $type ?>" 
                                            <?= (isset($_POST['transaction_type']) && $_POST['transaction_type'] === $type) ? 'selected' : '' ?>>
                                            <?= ucfirst($type) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($members)): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="member_id" class="form-label">
                                    Member (for personal tithes / donations)
                                </label>
                                <select class="form-select" id="member_id" name="member_id">
                                    <option value="">
                                        Not linked to a specific member
                                    </option>
                                    <?php foreach ($members as $member): ?>
                                        <?php
                                            $fullName = trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));
                                            $label = $fullName !== '' ? $fullName : ($member['email'] ?? ('Member #' . $member['id']));
                                        ?>
                                        <option value="<?= (int)$member['id'] ?>"
                                            <?= (isset($_POST['member_id']) && (int)$_POST['member_id'] === (int)$member['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($label) ?>
                                            <?php if (!empty($member['email'])): ?>
                                                (<?= htmlspecialchars($member['email']) ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">
                                    Use this when recording a personal tithe, donation, or partnership so it is linked to that member.
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>" 
                                           step="0.01" min="0.01" required placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="transaction_date" name="transaction_date" 
                                       value="<?= htmlspecialchars($_POST['transaction_date'] ?? date('Y-m-d')) ?>" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category...</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category ?>" 
                                            <?= (isset($_POST['category']) && $_POST['category'] === $category) ? 'selected' : '' ?>>
                                            <?= ucfirst($category) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <?php foreach ($paymentMethods as $method): ?>
                                        <option value="<?= $method ?>" 
                                            <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === $method) ? 'selected' : ($method === 'cash' ? 'selected' : '') ?>>
                                            <?= ucfirst(str_replace('_', ' ', $method)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Transaction description..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control" id="reference_number" name="reference_number" 
                               value="<?= htmlspecialchars($_POST['reference_number'] ?? '') ?>" 
                               placeholder="Check number, transaction ID, etc.">
                    </div>

                    <div class="d-flex gap-2">
                        <?php
                        $backToFinanceUrl = !empty($churchId)
                            ? AssetHelper::url('finance?church_id=' . (int)$churchId)
                            : AssetHelper::url('finance');
                        ?>
                        <a href="<?= $backToFinanceUrl ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="check-circle" class="me-1"></i> Create Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

