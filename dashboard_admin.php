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
            color: #f5f5f5 !important;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6),
                        0 0 30px rgba(138, 43, 226, 0.2) !important;
        }
        .card-body {
            color: #000000 !important;
        }
        .card-body h1, .card-body h2, .card-body h3, 
        .card-body h4, .card-body h5, .card-body h6 {
            color: #000000 !important;
            font-weight: 700 !important;
        }
        
        .landing-container {
            background: rgba(255, 255, 255, 0.99) !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.7),
                        0 0 60px rgba(0, 255, 157, 0.3) !important;
        }
        .landing-container h1, .landing-container h2,
        .landing-container p, .landing-container .tagline {
            color: #000000 !important;
            font-weight: 600 !important;
        }
        
        .interface-card {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }
        .interface-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.6),
                        0 0 40px rgba(138, 43, 226, 0.3);
            transform: translateY(-5px);
        }
        
        .admin-card {
            background: linear-gradient(135deg, #2d1b4e 0%, #1e3a5f 100%) !important;
            color: #ffffff !important;
        }
        .admin-card h3, .admin-card p, .admin-card .card-title {
            color: #ffffff !important;
            font-weight: 700 !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .cashier-card {
            background: linear-gradient(135deg, #00ff9d 0%, #00cc7f 100%) !important;
            color: #000000 !important;
        }
        .cashier-card h3, .cashier-card p, .cashier-card .card-title {
            color: #000000 !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.5);
        }
        
        .navbar {
            background: rgba(13, 17, 38, 0.98) !important;
            box-shadow: 0 2px 20px rgba(0, 255, 157, 0.4);
            backdrop-filter: blur(10px);
        }
        .navbar-brand, .nav-link, .navbar-text {
            color: #ffffff !important;
            font-weight: 600 !important;
            text-shadow: 0 0 15px rgba(0, 255, 157, 0.6),
                        0 0 5px rgba(255, 255, 255, 0.3);
        }
        .navbar-brand:hover, .nav-link:hover {
            color: #00ff9d !important;
            text-shadow: 0 0 20px rgba(0, 255, 157, 0.8);
        }
        

        label {
            color: #000000 !important;
            font-weight: 600 !important;
            font-size: 1.05em;
            margin-bottom: 0.5rem;
        }
        
        input, select, textarea {
            background: rgba(255, 255, 255, 0.98) !important;
            border: 2px solid rgba(138, 43, 226, 0.4) !important;
            color: #000000 !important;
            font-weight: 500 !important;
            font-size: 1.05em !important;
        }
        input::placeholder, textarea::placeholder {
            color: #666666 !important;
            font-weight: 400 !important;
        }
        input:focus, select:focus, textarea:focus {
            border-color: rgba(138, 43, 226, 0.8) !important;
            box-shadow: 0 0 0 0.3rem rgba(138, 43, 226, 0.3) !important;
            background: #ffffff !important;
        }
        
        .table {
            background: rgba(255, 255, 255, 0.98) !important;
            color: #000000 !important;
        }
        .table td, .table th {
            color: #000000 !important;
            font-weight: 500 !important;
            vertical-align: middle;
        }
        .table thead {
            background: linear-gradient(135deg, #2d1b4e 0%, #1e3a5f 100%) !important;
        }
        .table thead th {
            color: #ffffff !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
            border: none !important;
        }
        .table-dark {
            background: linear-gradient(135deg, #2d1b4e 0%, #1e3a5f 100%) !important;
        }
        .table-dark th, .table-dark td {
            color: #ffffff !important;
            font-weight: 600 !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
        
        .btn {
            font-weight: 700 !important;
            font-size: 1.05em !important;
            padding: 0.6rem 1.2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
        }
        .btn-primary {
            background: linear-gradient(135deg, #8a2be2 0%, #6a1bb2 100%) !important;
            border: none !important;
            color: #ffffff !important;
        }
        .btn-success {
            background: linear-gradient(135deg, #00ff9d 0%, #00cc7f 100%) !important;
            border: none !important;
            color: #000000 !important;
            font-weight: 700 !important;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff1493 0%, #cc1073 100%) !important;
            border: none !important;
            color: #ffffff !important;
        }
        .btn-dark {
            background: linear-gradient(135deg, #2d1b4e 0%, #1e3a5f 100%) !important;
            border: none !important;
            color: #ffffff !important;
        }
        
        /* Badges */
        .badge {
            font-weight: 700 !important;
            font-size: 0.9em !important;
            padding: 0.5em 0.9em;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        .badge-success {
            background: linear-gradient(135deg, #00ff9d 0%, #00cc7f 100%) !important;
            color: #000000 !important;
        }
        .badge-danger {
            background: linear-gradient(135deg, #ff1493 0%, #cc1073 100%) !important;
            color: #ffffff !important;
        }
        .badge-primary {
            background: linear-gradient(135deg, #8a2be2 0%, #6a1bb2 100%) !important;
            color: #ffffff !important;
        }
        .badge-dark {
            background: linear-gradient(135deg, #2d1b4e 0%, #1e3a5f 100%) !important;
            color: #ffffff !important;
        }
        
        .text-muted {
            color: #555555 !important;
            font-weight: 500 !important;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.98) !important;
            border-left: 5px solid #8a2be2;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        .stat-card h3, .stat-card h4, .stat-card h5 {
            color: #000000 !important;
            font-weight: 700 !important;
        }
        .stat-card p, .stat-card small {
            color: #333333 !important;
            font-weight: 600 !important;
        }
        
        /* Order summary */
        .order-summary {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        }
        .order-summary h4, .order-summary h5 {
            color: #000000 !important;
            font-weight: 700 !important;
        }
        .order-summary .total {
            color: #8a2be2 !important;
            font-weight: 800 !important;
            font-size: 1.5em;
        }
        
        .menu-item {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        .menu-item h5, .menu-item .item-name {
            color: #000000 !important;
            font-weight: 700 !important;
        }
        .menu-item .price {
            color: #8a2be2 !important;
            font-weight: 800 !important;
            font-size: 1.3em;
        }
        
        .alert {
            font-weight: 600 !important;
            border: 2px solid;
        }
        .alert-success {
            background: rgba(0, 255, 157, 0.2) !important;
            border-color: #00ff9d !important;
            color: #000000 !important;
        }
        .alert-danger {
            background: rgba(255, 20, 147, 0.2) !important;
            border-color: #ff1493 !important;
            color: #000000 !important;
        }
        .alert-info {
            background: rgba(138, 43, 226, 0.2) !important;
            border-color: #8a2be2 !important;
            color: #000000 !important;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.99) !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.7),
                        0 0 60px rgba(138, 43, 226, 0.3) !important;
        }
        .login-card h2, .login-card h3 {
            color: #000000 !important;
            font-weight: 700 !important;
        }
        
        .receipt {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .receipt h1, .receipt h2, .receipt h3 {
            color: #000000 !important;
            font-weight: 700 !important;
        }
        .receipt .total-amount {
            color: #8a2be2 !important;
            font-weight: 800 !important;
        }

        body {
            color: #f0f0f0;
        }
        .card {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6) !important;
        }
        .card-body {
            color: #1a1a1a;
        }
        .text-muted {
            color: #6c757d !important;
        }
        .navbar {
            background: rgba(26, 26, 46, 0.95) !important;
            box-shadow: 0 2px 20px rgba(0, 255, 157, 0.3);
        }
        .navbar-brand, .nav-link {
            color: #f0f0f0 !important;
            text-shadow: 0 0 10px rgba(0, 255, 157, 0.5);
        }
        h1, h2, h3, h4, h5, h6 {
            color: #1a1a1a;
        }
        .card h1, .card h2, .card h3, .card h4, .card h5, .card h6 {
            color: #1a1a1a;
        }
        label {
            color: #2a2a2a;
            font-weight: 500;
        }
        .table {
            background: rgba(255, 255, 255, 0.95);
        }
        .table thead {
            background: #2d1b4e !important;
            color: #f0f0f0 !important;
        }
        .table thead th {
            color: #f0f0f0 !important;
        }
        .table-dark {
            background: #2d1b4e !important;
            color: #f0f0f0 !important;
        }
        .table-dark th, .table-dark td {
            color: #f0f0f0 !important;
        }
        .btn {
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
        }
        input, select, textarea {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 2px solid rgba(138, 43, 226, 0.3) !important;
            color: #1a1a1a !important;
        }
        input:focus, select:focus, textarea:focus {
            border-color: rgba(138, 43, 226, 0.6) !important;
            box-shadow: 0 0 0 0.2rem rgba(138, 43, 226, 0.25) !important;
        }
        .badge {
            font-weight: 600;
            padding: 0.5em 0.8em;
        }
        .landing-container {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.6),
                        0 0 40px rgba(0, 255, 157, 0.2) !important;
        }
        .interface-card {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        }
        .admin-card {
            background: linear-gradient(135deg, #2d1b4e 0%, #1e3a5f 100%) !important;
            color: #f0f0f0 !important;
        }
        .admin-card h3, .admin-card p {
            color: #f0f0f0 !important;
        }
        
        body {
            background: linear-gradient(to bottom, 
                #0a0e27 0%,
                #1a1f3a 20%,
                #2d1b4e 40%,
                #1e3a5f 60%,
                #0f2027 80%,
                #000000 100%);;
            position: relative;
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 60%;
            background: linear-gradient(90deg, 
                rgba(0, 255, 157, 0.2) 0%,
                rgba(0, 204, 255, 0.2) 25%,
                rgba(138, 43, 226, 0.2) 50%,
                rgba(255, 20, 147, 0.2) 75%,
                rgba(0, 255, 157, 0.2) 100%);
            animation: aurora 15s ease-in-out infinite;
            pointer-events: none;
            filter: blur(50px);
        }
        @keyframes aurora {
            0%, 100% {
                opacity: 0.4;
                transform: translateY(0) scaleY(1);
            }
            25% {
                opacity: 0.6;
                transform: translateY(-20px) scaleY(1.1);
            }
            50% {
                opacity: 0.5;
                transform: translateY(-10px) scaleY(0.9);
            }
            75% {
                opacity: 0.7;
                transform: translateY(-30px) scaleY(1.2);
            }
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
            background: linear-gradient(to bottom, 
                #0a0e27 0%,
                #1a1f3a 20%,
                #2d1b4e 40%,
                #1e3a5f 60%,
                #0f2027 80%,
                #000000 100%);;
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