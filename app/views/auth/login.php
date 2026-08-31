<?php
use App\Utilities\Security;
use App\Utilities\AssetHelper;

$csrfToken = Security::generateCSRFToken();
?>
<div class="text-center">
    <h5 class="mb-0">Welcome Back !</h5>
    <p class="text-muted mt-2">Sign in to continue to LCM Portal.</p>
</div>
<form class="mt-4 pt-2" method="POST" action="<?= AssetHelper::url('login') ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
    
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" 
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
               placeholder="Enter email" required autofocus>
    </div>
    <div class="mb-3">
        <div class="d-flex align-items-start">
            <div class="flex-grow-1">
                <label class="form-label">Password</label>
            </div>
            <div class="flex-shrink-0">
                <div class="">
                    <a href="<?= AssetHelper::url('forgot-password') ?>" class="text-muted">Forgot password?</a>
                </div>
            </div>
        </div>
        
        <div class="input-group auth-pass-inputgroup">
            <input type="password" class="form-control" name="password" 
                   placeholder="Enter password" aria-label="Password" 
                   aria-describedby="password-addon" required>
            <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon">
                <i class="mdi mdi-eye-outline"></i>
            </button>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-check" name="remember">
                <label class="form-check-label" for="remember-check">
                    Remember me
                </label>
            </div>  
        </div>
    </div>
    <div class="mb-3">
        <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Log In</button>
    </div>
</form>
