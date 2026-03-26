<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zest Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    /* ===== Global Reset ===== */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', sans-serif;
}

body {
    background-color: #f4f4f4;
    color: #333;
    line-height: 1.6;
}

/* ===== Dashboard Layout ===== */
.dashboard {
    display: flex;
    min-height: 100vh;
}

/* ===== Sidebar ===== */
.sidebar {
    width: 220px;
    background-color: #1a1a1a;
    color: #fff;
    padding: 2rem 1rem;
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100%;
}

.sidebar .logo {
    font-size: 1.8rem;
    font-weight: bold;
    text-align: center;
    margin-bottom: 2rem;
}

.sidebar nav {
    display: flex;
    flex-direction: column;
}

.sidebar nav a {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    text-decoration: none;
    color: #ccc;
    padding: 0.7rem 1rem;
    border-radius: 5px;
    margin-bottom: 0.5rem;
    transition: 0.2s;
}

.sidebar nav a:hover,
.sidebar nav a.active {
    background-color: #ffc107;
    color: #1a1a1a;
}

/* ===== Main Content ===== */
.main-content {
    margin-left: 220px;
    padding: 2rem;
    width: 100%;
}

/* ===== Dashboard Header ===== */
.dashboard-header h1 {
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
}

/* ===== Alerts ===== */
.alert {
    padding: 0.8rem 1rem;
    margin-bottom: 1rem;
    border-radius: 5px;
    text-align: center;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

/* ===== Card Styles ===== */
.card {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    padding: 1.5rem;
    overflow-x: auto;
}

/* ===== Table Styles ===== */
.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #f0f0f0;
}

.table tr:hover {
    background-color: #f9f9f9;
}

/* ===== Status Badges ===== */
.status {
    display: inline-block;
    padding: 0.3rem 0.6rem;
    border-radius: 5px;
    font-weight: 600;
    text-transform: capitalize;
    font-size: 0.85rem;
}

.status.pending {
    background-color: #fff3cd;
    color: #856404;
}

.status.confirmed {
    background-color: #d4edda;
    color: #155724;
}

.status.cancelled {
    background-color: #f8d7da;
    color: #721c24;
}

/* ===== Buttons ===== */
button,
.btn-danger {
    background-color: #dc3545;
    color: #fff;
    border: none;
    padding: 0.5rem 0.9rem;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.2s;
}

button:hover,
.btn-danger:hover {
    background-color: #c82333;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .dashboard {
        flex-direction: column;
    }
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        flex-direction: row;
        justify-content: space-around;
    }
    .main-content {
        margin-left: 0;
        padding: 1rem;
    }
    .table th,
    .table td {
        padding: 0.5rem;
    }
}
</style>
</head>
<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2 class="logo">ZEST</h2>
        <nav>
            <a href="#" class="active"><i class="fas fa-calendar"></i> Reservations</a>
            <a href="#"><i class="fas fa-users"></i> Customers</a>
            <a href="tables.php"><i class="fas fa-table"></i> Tables</a>
            <a href="#"><i class="fas fa-cog"></i> Settings</a>
            <a href="index.php"><i class="fas fa-logout"></i> Logout</a>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- HEADER -->
        <div class="dashboard-header">
            <h1>Reservations</h1>
        </div>

        <?php if (isset($msg)): ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php endif; ?>

        <!-- TABLE CARD -->
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Table</th>
                        <th>Size</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td><?= $res['reservation_id'] ?></td>
                        <td><?= htmlspecialchars($res['customer_name']) ?></td>
                        <td><?= $res['reservation_date'] ?></td>
                        <td><?= $res['slot_name'] ?></td>
                        <td><?= $res['table_number'] ?></td>
                        <td><?= $res['party_size'] ?></td>
                        <td>
                            <span class="status <?= $res['status'] ?>">
                                <?= $res['status'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($res['status'] != 'cancelled'): ?>
                            <form method="POST">
                                <input type="hidden" name="cancel_id" value="<?= $res['reservation_id'] ?>">
                                <button class="btn-danger" onclick="return confirm('Cancel this reservation?')">Cancel</button>
                            </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </main>

</div>

</body>
</html>