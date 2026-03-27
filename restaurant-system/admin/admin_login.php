<?php
// Start session safely
require_once '../config/session.php';

// Database connection
require_once '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: tables.php");
    exit();
}

// Initialize variables
$error = '';

// Handle Login Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            // Use existing PDO from db.php (DO NOT recreate)
            global $pdo;

            // Secure prepared statement
            $stmt = $pdo->prepare("
                SELECT admin_id, username, password_hash 
                FROM admins 
                WHERE username = :username
                LIMIT 1
            ");
            $stmt->execute(['username' => $username]);

            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if ($admin && password_verify($password, $admin['password_hash'])) {

                // Secure session setup
                session_regenerate_id(true);

                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_username'] = $admin['username'];

                // Update last login
                $updateStmt = $pdo->prepare("
                    UPDATE admins 
                    SET last_login = NOW() 
                    WHERE admin_id = :id
                ");
                $updateStmt->execute(['id' => $admin['admin_id']]);

                // Redirect to admin dashboard
                header("Location: admin_reservations.php");
                exit();

            } else {
                $error = "Invalid username or password.";
            }

        } catch (PDOException $e) {
            // For development ONLY (remove in production)
            // echo $e->getMessage();

            $error = "Database error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Zest Restaurant</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-orange: #f0c92fdc;
            --dark-black: #1a1a1a;
            --charcoal: #2d2d2d;
        }
        
        body {
            background: linear-gradient(135deg, var(--dark-black) 0%, var(--charcoal) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-card {
            background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
            border: 2px solid var(--primary-orange);
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(255, 159, 67, 0.3);
            max-width: 450px;
            width: 100%;
        }
        
        .login-header {
            background: var(--primary-orange);
            color: var(--dark-black);
            padding: 2rem;
            border-radius: 13px 13px 0 0;
            text-align: center;
        }
        
        .login-header h2 {
            font-weight: 700;
            margin: 0;
        }
        
        .form-control {
            background: #3d3d3d;
            border: 1px solid #555;
            color: #fff;
            padding: 0.75rem 1rem;
        }
        
        .form-control:focus {
            background: #4d4d4d;
            border-color: var(--primary-orange);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(255, 159, 67, 0.25);
        }
        
        .form-label {
            color: var(--primary-orange);
            font-weight: 600;
        }
        
        .btn-orange {
            background: var(--primary-orange);
            border: none;
            color: var(--dark-black);
            font-weight: 700;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }
        
        .btn-orange:hover {
            background: #e58e3c;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 159, 67, 0.4);
        }
        
        .alert-custom {
            border-left: 4px solid;
        }
        
        .alert-error {
            background: rgba(255, 118, 117, 0.1);
            border-color: #ff7675;
            color: #ff7675;
        }
        
        .alert-success {
            background: rgba(85, 239, 196, 0.1);
            border-color: #55efc4;
            color: #55efc4;
        }
        
        .logo-icon {
            font-size: 3rem;
            color: var(--dark-black);
            margin-bottom: 0.5rem;
        }
        
        .back-link {
            color: var(--primary-orange);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: #fff;
        }
        
        .input-group-text {
            background: #3d3d3d;
            border: 1px solid #555;
            color: var(--primary-orange);
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="login-card">
                    <!-- Header -->
                    <div class="login-header">
                        <i class="fas fa-user-shield logo-icon"></i>
                        <h2>Admin Login</h2>
                        <p class="mb-0 text-dark">Zest Restaurant Management</p>
                    </div>
                    
                    <!-- Body -->
                    <div class="card-body p-4">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-custom alert-error mb-3">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-custom alert-success mb-3">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="admin_login.php">
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           name="username" 
                                           placeholder="Enter username"
                                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                           required 
                                           autofocus>
                                </div>
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-orange btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>
                            
                            <!-- Remember Me -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label text-light" for="remember">
                                    Remember me
                                </label>
                            </div>
                            
                            <!-- Back Link -->
                            <div class="text-center">
                                <a href="../index.php" class="back-link">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Restaurant
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Footer -->
                    <div class="card-footer text-center p-3" style="background: #1a1a1a; border-top: 1px solid #333;">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Secure Admin Access
                        </small>
                    </div>
                </div>
                
                <!-- Security Notice -->
                <div class="text-center mt-4">
                    <p class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Default credentials: <strong>admin</strong> / <strong>Admin123!</strong>
                        <br>
                        <span class="text-warning">Please change after first login!</span>
                    </p>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Toggle Password Visibility -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
    
</body>
</html>