<?php
require_once 'config/session.php';

// Only redirect when needed.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Regenerate session on login - after successful login
session_regenerate_id(true);
$_SESSION['admin_logged_in'] = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table - Zest Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">🍋 Zest Restaurant</a>
            <a href="admin/admin_login.php" class="btn btn-outline-light btn-sm">Admin Login</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card shadow-lg border-0 hero-card">
            <div class="card-body p-4">
                <h2 class="card-title text-warning fw-bold mb-3">
                    <i class="fas fa-utensils me-2"></i>Welcome to Zest
                </h2>
                <p class="card-text text-dark mb-3">
                    Experience the zest of life with our farm-to-table cuisine.
                    We specialize in citrus-infused dishes and fresh seafood.
                </p>
                <div class="border-top border-warning my-3"></div>
                <p class="card-text text-dark mb-1">
                    <strong class="text-warning"><i class="fas fa-clock me-2"></i>Hours:</strong>
                    Monday-Sun, 5:00 PM - 10:00 PM
                </p>
                <p class="card-text text-dark mb-0">
                    <strong class="text-warning"><i class="fas fa-map-marker-alt me-2"></i>Location:</strong>
                    Flavor Avenue, Kutus Town
                </p>
            </div>
            <div class="card-footer bg-transparent border-0 text-center pb-3">
                <a href="#reserve" class="btn btn-warning fw-bold px-4">
                    <i class="fas fa-calendar-check me-2"></i>Book Now
                </a>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="mb-0">Reserve Your Table</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['status'])) {
                            if ($_GET['status'] == 'success') {
                                echo '<div class="alert alert-success">Booking Request Sent Successfully!</div>';
                            } elseif ($_GET['status'] == 'error') {
                                echo '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
                            }
                        }
                        ?>
                        <form action="create_reservation.php" method="POST" id="reserve">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Time</label>
                                    <select name="time" class="form-select" required>
                                        <option value="">Select Time</option>
                                        <option value="17:00">5:00 PM</option>
                                        <option value="18:00">6:00 PM</option>
                                        <option value="19:00">7:00 PM</option>
                                        <option value="20:00">8:00 PM</option>
                                        <option value="21:00">9:00 PM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Number of Guests</label>
                                <input type="number" name="guests" class="form-control" min="1" max="20" required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100 fw-bold">Confirm Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 mb-3 text-muted">
        <p>&copy; <?php echo date('Y'); ?> Zest Restaurant. Fresh Flavors.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>