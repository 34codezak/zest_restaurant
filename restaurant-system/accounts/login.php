<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Zest Restaurant</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">

        <!-- Header -->
        <div class="auth-header">
            <div class="logo">ZEST<span>.</span></div>
            <p>Welcome back! Sign in to continue</p>
        </div>

        <!-- Body -->
        <div class="auth-body">
            <h3 class="form-title">Sign In to Your Account</h3>

            <?php if (isset($errors['lockout'])): ?>
                <div class="alert alert-lockout">
                    <?php echo $errors['lockout']; ?>
                </div>
            <?php elseif (!empty($errors['general'])): ?>
                <div class="alert alert-error">
                    <?php echo $errors['general']; ?>
                </div>
            <?php elseif (!empty($errors['auth'])): ?>
                <div class="alert alert-error">
                    <?php echo $errors['auth']; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" id="loginForm">

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email"
                               class="input-field <?php echo isset($errors['email']) ? 'input-error' : ''; ?>"
                               id="email"
                               name="email"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               required>
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-text"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password"
                               class="input-field <?php echo isset($errors['password']) ? 'input-error' : ''; ?>"
                               id="password"
                               name="password"
                               required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-text"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                    <div class="forgot-password">
                        <a href="forgot_password.php">Forgot password?</a>
                    </div>
                </div>

                <!-- Remember -->
                <div class="form-check">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me for 30 days</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-primary" id="submitBtn">Sign In</button>

                <div class="security-text">
                    Secure SSL encrypted connection
                </div>
            </form>

            <div class="divider"><span>Or continue with</span></div>

            <div class="social-buttons">
                <a href="#" class="btn-social google">Google</a>
                <a href="#" class="btn-social facebook">Facebook</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            <p>Don't have an account?</p>
            <a href="register.php" class="btn-outline">Create Account</a>
            <p><a href="index.php">← Back to Restaurant</a></p>
        </div>

    </div>
</div>

</body>
</html>