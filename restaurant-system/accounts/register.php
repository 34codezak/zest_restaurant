<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Zest Restaurant</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">

        <!-- Header -->
        <div class="auth-header">
            <div class="logo"  style="color: #542b01;">ZEST<span>.</span></div>
            <p>Create your Zest account</p>
        </div>

        <div class="auth-body">
            <h3 class="form-title">Create Your Account</h3>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-error"><?php echo $errors['general']; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <br><small>Redirecting...</small>
                </div>
            <?php endif; ?>

            <form method="POST" id="registerForm">

                <!-- NAME ROW -->
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text"
                               class="input-field <?php echo isset($errors['first_name']) ? 'input-error' : ''; ?>"
                               name="first_name"
                               value="<?php echo htmlspecialchars($oldInput['first_name'] ?? ''); ?>">
                        <?php if (isset($errors['first_name'])): ?>
                            <div class="error-text"><?php echo $errors['first_name']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text"
                               class="input-field <?php echo isset($errors['last_name']) ? 'input-error' : ''; ?>"
                               name="last_name"
                               value="<?php echo htmlspecialchars($oldInput['last_name'] ?? ''); ?>">
                        <?php if (isset($errors['last_name'])): ?>
                            <div class="error-text"><?php echo $errors['last_name']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- EMAIL -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email"
                           class="input-field <?php echo isset($errors['email']) ? 'input-error' : ''; ?>"
                           name="email"
                           value="<?php echo htmlspecialchars($oldInput['email'] ?? ''); ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-text"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- PHONE -->
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel"
                           class="input-field"
                           name="phone"
                           value="<?php echo htmlspecialchars($oldInput['phone'] ?? ''); ?>">
                </div>

                <!-- PASSWORD -->
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <input type="password"
                               class="input-field <?php echo isset($errors['password']) ? 'input-error' : ''; ?>"
                               id="password"
                               name="password">
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div id="strengthBar"></div>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-text"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password"
                               class="input-field <?php echo isset($errors['confirm_password']) ? 'input-error' : ''; ?>"
                               id="confirm_password"
                               name="confirm_password">
                        <button type="button" class="toggle-password" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="error-text"><?php echo $errors['confirm_password']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- TERMS -->
                <div class="form-check">
                    <input type="checkbox" name="agree_terms">
                    <label>I agree to Terms &amp; Privacy</label>
                </div>

                <!-- SUBMIT -->
                <button class="btn-primary" id="submitBtn">Create Account</button>

                <div class="security-text">
                    Your password is securely encrypted
                </div>

            </form>

            <div class="divider"><span>Or sign up with</span></div>

            <div class="social-buttons">
                <a href="#" class="btn-social google">Google</a>
                <a href="#" class="btn-social facebook">Facebook</a>
            </div>

        </div>

        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Sign in</a></p>
        </div>

    </div>
</div>

</body>
</html>