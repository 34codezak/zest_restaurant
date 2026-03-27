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
                <a href="reservation_form.php" class="btn btn-warning fw-bold px-4">
                    <i class="fas fa-calendar-check me-2"></i>Book Now
                </a>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 mb-3 text-muted">
        <p>&copy; <?php echo date('Y'); ?> Zest Restaurant. Fresh Flavors.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>