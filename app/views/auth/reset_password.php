<?php
use App\Utilities\Security;
use App\Utilities\AssetHelper;
?>
<div class="text-center">
    <h5 class="mb-0">Reset Your Password</h5>
    <p class="text-muted mt-2">Enter your new password below.</p>
</div>

<?php if ($this->session->hasFlash('error')): ?>
    <div class="alert alert-danger text-center my-4" role="alert">
        <?= htmlspecialchars($this->session->getFlash('error')) ?>
    </div>
<?php endif; ?>

<?php if ($this->session->hasFlash('errors')): ?>
    <div class="alert alert-danger text-center my-4" role="alert">
        <?php foreach ($this->session->getFlash('errors') as $field => $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form class="mt-4 pt-2" method="POST" action="<?= AssetHelper::url("reset-password/{$token}") ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
    
    <div class="mb-3">
        <label class="form-label">New Password</label>
        <div class="input-group auth-pass-inputgroup">
            <input type="password" class="form-control" id="password" name="password" 
                   placeholder="Enter new password" aria-label="Password" 
                   aria-describedby="password-addon" required>
            <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon">
                <i class="mdi mdi-eye-outline"></i>
            </button>
        </div>
        <div class="form-text">Password must be at least 6 characters long.</div>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <div class="input-group auth-pass-inputgroup">
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
                   placeholder="Confirm new password" aria-label="Confirm Password" 
                   aria-describedby="confirm-password-addon" required>
            <button class="btn btn-light shadow-none ms-0" type="button" id="confirm-password-addon">
                <i class="mdi mdi-eye-outline"></i>
            </button>
        </div>
    </div>
    
    <div class="mb-3">
        <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">
            <i class="mdi mdi-lock-reset me-1"></i> Reset Password
        </button>
    </div>
</form>

<div class="mt-4 text-center">
    <p class="mb-0">
        Remember your password? 
        <a href="<?= AssetHelper::url('login') ?>" class="fw-medium text-primary">Sign in</a>
    </p>
</div>

<div class="mt-4 text-center">
    <div class="alert alert-info">
        <h6 class="mb-2"><i class="mdi mdi-information-outline me-1"></i> Security Notice</h6>
        <p class="mb-0 small">
            After resetting your password, you will be redirected to the login page.
            Your password reset link will expire in 24 hours.
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility for both fields
    document.getElementById('password-addon').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('mdi-eye-outline');
        this.querySelector('i').classList.toggle('mdi-eye-off-outline');
    });
    
    document.getElementById('confirm-password-addon').addEventListener('click', function() {
        const passwordInput = document.getElementById('password_confirmation');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('mdi-eye-outline');
        this.querySelector('i').classList.toggle('mdi-eye-off-outline');
    });
});
</script>