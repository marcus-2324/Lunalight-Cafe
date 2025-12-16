<?php
$serverName = "LAPTOP-QNA7LUF8\SQLEXPRESS";
$connectionOptions = [
    "Database" => "LUNALIGHT",
    "Uid" => "",
    "PWD" => ""
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn == false)
    die(print_r(sqlsrv_errors(), true));

$USER_ID = $_POST['USER_ID'];

if($USER_ID == '') {
    header("Location: admin_login.html");
    exit();
}

$sql_check = "SELECT * FROM USERS WHERE USER_ID = $USER_ID AND ROLE = 'ADMIN'";
$result_check = sqlsrv_query($conn, $sql_check);

if($result_check == false) {
    die(print_r(sqlsrv_errors(), true));
}

$user_data = sqlsrv_fetch_array($result_check);

if($user_data == false) {
    header("Location: admin_login.html");
    exit();
}

$sql_menu_count = "SELECT COUNT(*) AS TOTAL FROM MENU_ITEMS";
$result_menu = sqlsrv_query($conn, $sql_menu_count);
$row_menu = sqlsrv_fetch_array($result_menu);
$total_menu_items = $row_menu['TOTAL'];

$sql_cashier_count = "SELECT COUNT(*) AS TOTAL FROM USERS WHERE ROLE = 'CASHIER' AND STATUS = 'ACTIVE'";
$result_cashier = sqlsrv_query($conn, $sql_cashier_count);
$row_cashier = sqlsrv_fetch_array($result_cashier);
$total_cashiers = $row_cashier['TOTAL'];

$sql_admin_count = "SELECT COUNT(*) AS TOTAL FROM USERS WHERE ROLE = 'ADMIN' AND STATUS = 'ACTIVE'";
$result_admin = sqlsrv_query($conn, $sql_admin_count);
$row_admin = sqlsrv_fetch_array($result_admin);
$total_admins = $row_admin['TOTAL'];

$sql_orders_count = "SELECT COUNT(*) AS TOTAL FROM ORDERS";
$result_orders = sqlsrv_query($conn, $sql_orders_count);
$row_orders = sqlsrv_fetch_array($result_orders);
$total_orders = $row_orders['TOTAL'];

$sql_revenue = "SELECT ISNULL(SUM(TOTAL_AMOUNT), 0) AS REVENUE FROM ORDERS WHERE STATUS = 'COMPLETED'";
$result_revenue = sqlsrv_query($conn, $sql_revenue);
$row_revenue = sqlsrv_fetch_array($result_revenue);
$total_revenue = $row_revenue['REVENUE'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Lunalight Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
        }
        .navbar {
            background: rgba(26, 26, 46, 0.95) !important;
            backdrop-filter: blur(10px);
        }
        .stats-card {
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon { font-size: 3rem; }
        .stats-number { font-size: 2.5rem; }
        .btn-action {
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            transition: all 0.3s;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 52, 96, 0.3);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-4" href="#">🌙 Lunalight Café - Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo $user_data['FULL_NAME']; ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Lunalight.html">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid p-4">
        <div class="row g-4 mb-4">
            <div class="col-md">
                <div class="card stats-card shadow-sm border-0 rounded-4 text-center p-4">
                    <div class="stats-icon mb-3">🍽️</div>
                    <div class="stats-number fw-bold text-primary"><?php echo $total_menu_items; ?></div>
                    <div class="text-muted">Menu Items</div>
                </div>
            </div>
            <div class="col-md">
                <div class="card stats-card shadow-sm border-0 rounded-4 text-center p-4">
                    <div class="stats-icon mb-3">🌙</div>
                    <div class="stats-number fw-bold text-primary"><?php echo $total_admins; ?></div>
                    <div class="text-muted">Admins</div>
                </div>
            </div>
            <div class="col-md">
                <div class="card stats-card shadow-sm border-0 rounded-4 text-center p-4">
                    <div class="stats-icon mb-3">👥</div>
                    <div class="stats-number fw-bold text-primary"><?php echo $total_cashiers; ?></div>
                    <div class="text-muted">Cashiers</div>
                </div>
            </div>
            <div class="col-md">
                <div class="card stats-card shadow-sm border-0 rounded-4 text-center p-4">
                    <div class="stats-icon mb-3">📋</div>
                    <div class="stats-number fw-bold text-primary"><?php echo $total_orders; ?></div>
                    <div class="text-muted">Orders</div>
                </div>
            </div>
            <div class="col-md">
                <div class="card stats-card shadow-sm border-0 rounded-4 text-center p-4">
                    <div class="stats-icon mb-3">💰</div>
                    <div class="stats-number fw-bold text-success">₱<?php echo number_format($total_revenue, 2); ?></div>
                    <div class="text-muted">Revenue</div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <h2 class="fw-bold mb-4 pb-3 border-bottom border-3 border-primary">Quick Actions</h2>
                <div class="row g-3">
                    <div class="col-md">
                        <form action="create_order.php" method="post">
                            <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                            <button type="submit" class="btn btn-action text-white w-100 py-3 rounded-3 fw-semibold">🛒 Take Orders</button>
                        </form>
                    </div>
                    <div class="col-md">
                        <form action="manage_categories.php" method="post">
                            <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                            <button type="submit" class="btn btn-action text-white w-100 py-3 rounded-3 fw-semibold">📂 Manage Categories</button>
                        </form>
                    </div>
                    <div class="col-md">
                        <form action="manage_menu.php" method="post">
                            <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                            <button type="submit" class="btn btn-action text-white w-100 py-3 rounded-3 fw-semibold">🍴 Manage Menu Items</button>
                        </form>
                    </div>
                    <div class="col-md">
                        <form action="manage_users.php" method="post">
                            <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                            <button type="submit" class="btn btn-action text-white w-100 py-3 rounded-3 fw-semibold">👥 Manage Users</button>
                        </form>
                    </div>
                    <div class="col-md">
                        <form action="view_orders.php" method="post">
                            <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                            <button type="submit" class="btn btn-action text-white w-100 py-3 rounded-3 fw-semibold">📊 View Orders & Reports</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>