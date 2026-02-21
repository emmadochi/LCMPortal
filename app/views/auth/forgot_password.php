<?php
use App\Utilities\Security;
use App\Utilities\AssetHelper;
?>
<div class="text-center">
    <h5 class="mb-0">Reset Password</h5>
    <p class="text-muted mt-2">Enter your email address and we'll send a request to administrators for password reset.</p>
</div>

<?php if ($this->session->hasFlash('success')): ?>
    <div class="alert alert-success text-center my-4" role="alert">
        <?= htmlspecialchars($this->session->getFlash('success')) ?>
    </div>
<?php endif; ?>

<?php if ($this->session->hasFlash('error')): ?>
    <div class="alert alert-danger text-center my-4" role="alert">
        <?= htmlspecialchars($this->session->getFlash('error')) ?>
    </div>
<?php endif; ?>

<form class="mt-4 pt-2" method="POST" action="<?= AssetHelper::url('forgot-password') ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
    
    <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" 
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
               placeholder="Enter your email address" required autofocus>
        <?php if ($this->session->hasFlash('errors') && isset($this->session->getFlash('errors')['email'])): ?>
            <div class="text-danger small mt-1"><?= htmlspecialchars($this->session->getFlash('errors')['email']) ?></div>
        <?php endif; ?>
    </div>
    
    <div class="mb-3">
        <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">
            <i class="mdi mdi-email-send me-1"></i> Send Reset Request
        </button>
    </div>
</form>

<div class="mt-4 text-center">
    <p class="mb-0">
        Remember your password? 
        <a href="<?= AssetHelper::url('login') ?>" class="fw-medium text-primary">Sign in</a>
    </p>
</div>

<div class="mt-5 text-center">
    <div class="alert alert-info">
        <h6 class="mb-2"><i class="mdi mdi-information-outline me-1"></i> Important Notice</h6>
        <p class="mb-0 small">
            Password reset requests require administrator approval. 
            You will be notified once your request is reviewed.
            Please allow some time for processing your request.
        </p>
    </div>
    <div class="alert alert-warning mt-2">
        <h6 class="mb-2"><i class="mdi mdi-shield-account me-1"></i> Security Notice</h6>
        <p class="mb-0 small">
            You will receive an email notification once your password reset request is approved or rejected. 
            For security reasons, the link will expire in 24 hours after approval.
        </p>
    </div>
</div>