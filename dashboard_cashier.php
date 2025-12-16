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
    header("Location: cashier_login.html");
    exit();
}

$sql_check = "SELECT * FROM USERS WHERE USER_ID = $USER_ID AND ROLE = 'CASHIER'";
$result_check = sqlsrv_query($conn, $sql_check);

if($result_check == false) {
    die(print_r(sqlsrv_errors(), true));
}

$user_data = sqlsrv_fetch_array($result_check);

if($user_data == false) {
    header("Location: cashier_login.html");
    exit();
}

$sql_today = "SELECT COUNT(*) AS TODAY_ORDERS, ISNULL(SUM(TOTAL_AMOUNT), 0) AS TODAY_REVENUE 
              FROM ORDERS 
              WHERE CAST(ORDER_DATE AS DATE) = CAST(GETDATE() AS DATE) 
              AND STATUS = 'COMPLETED'
              AND USER_ID = $USER_ID";
$result_today = sqlsrv_query($conn, $sql_today);
$today_data = sqlsrv_fetch_array($result_today);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cashier Dashboard - Lunalight Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
        }
        .navbar {
            background: rgba(46, 204, 113, 0.95) !important;
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
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            transition: all 0.3s;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-4" href="#">🌙 Lunalight Café - Cashier</a>
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
            <div class="col-md-6">
                <div class="card stats-card shadow-sm border-0 rounded-4 text-center p-5">
                    <div class="stats-icon mb-3">📋</div>
                    <div class="stats-number fw-bold text-success"><?php echo $today_data['TODAY_ORDERS']; ?></div>
                    <div class="text-muted fs-5">Orders Today</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stats-card shadow-sm border-0 rounded-4 text-center p-5">
                    <div class="stats-icon mb-3">💰</div>
                    <div class="stats-number fw-bold text-success">₱<?php echo number_format($today_data['TODAY_REVENUE'], 2); ?></div>
                    <div class="text-muted fs-5">Sales Today</div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-5 text-center">
                <h2 class="fw-bold mb-4 pb-3 border-bottom border-3 border-success">Start Taking Orders</h2>
                <form action="create_order.php" method="post">
                    <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                    <button type="submit" class="btn btn-action text-white btn-lg px-5 py-4 rounded-3 fw-semibold fs-4">🛒 New Order</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>