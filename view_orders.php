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

$sql_check = "SELECT * FROM USERS WHERE USER_ID = $USER_ID AND ROLE = 'ADMIN'";
$result_check = sqlsrv_query($conn, $sql_check);
$user_data = sqlsrv_fetch_array($result_check);

if($user_data == false) {
    header("Location: admin_login.html");
    exit();
}

$filter = $_POST['filter'];
if($filter == '') {
    $filter = 'all';
}

if($filter == 'today') {
    $sql_orders = "SELECT o.ORDER_NUMBER, o.ORDER_DATE, o.ORDER_TIME, o.TOTAL_AMOUNT, 
                          o.PAYMENT_METHOD, o.STATUS, u.FULL_NAME
                   FROM ORDERS AS o 
                   INNER JOIN USERS AS u ON o.USER_ID = u.USER_ID 
                   WHERE CAST(o.ORDER_DATE AS DATE) = CAST(GETDATE() AS DATE)
                   ORDER BY o.ORDER_ID DESC";
}
else if($filter == 'week') {
    $sql_orders = "SELECT o.ORDER_NUMBER, o.ORDER_DATE, o.ORDER_TIME, o.TOTAL_AMOUNT, 
                          o.PAYMENT_METHOD, o.STATUS, u.FULL_NAME
                   FROM ORDERS AS o 
                   INNER JOIN USERS AS u ON o.USER_ID = u.USER_ID 
                   WHERE o.ORDER_DATE >= DATEADD(DAY, -7, GETDATE())
                   ORDER BY o.ORDER_ID DESC";
}
else {
    $sql_orders = "SELECT o.ORDER_NUMBER, o.ORDER_DATE, o.ORDER_TIME, o.TOTAL_AMOUNT, 
                          o.PAYMENT_METHOD, o.STATUS, u.FULL_NAME
                   FROM ORDERS AS o 
                   INNER JOIN USERS AS u ON o.USER_ID = u.USER_ID 
                   ORDER BY o.ORDER_ID DESC";
}
$result_orders = sqlsrv_query($conn, $sql_orders);

$sql_today = "SELECT COUNT(*) AS TODAY_ORDERS, ISNULL(SUM(TOTAL_AMOUNT), 0) AS TODAY_REVENUE 
              FROM ORDERS 
              WHERE CAST(ORDER_DATE AS DATE) = CAST(GETDATE() AS DATE)";
$result_today = sqlsrv_query($conn, $sql_today);
$today_data = sqlsrv_fetch_array($result_today);

$sql_week = "SELECT COUNT(*) AS WEEK_ORDERS, ISNULL(SUM(TOTAL_AMOUNT), 0) AS WEEK_REVENUE 
             FROM ORDERS 
             WHERE ORDER_DATE >= DATEADD(DAY, -7, GETDATE())";
$result_week = sqlsrv_query($conn, $sql_week);
$week_data = sqlsrv_fetch_array($result_week);

$sql_all = "SELECT COUNT(*) AS ALL_ORDERS, ISNULL(SUM(TOTAL_AMOUNT), 0) AS ALL_REVENUE 
            FROM ORDERS";
$result_all = sqlsrv_query($conn, $sql_all);
$all_data = sqlsrv_fetch_array($result_all);

$sql_top_items = "SELECT m.ITEM_NAME, SUM(oi.QUANTITY) AS TOTAL_SOLD, SUM(oi.SUBTOTAL) AS TOTAL_SALES
                  FROM ORDER_ITEMS AS oi
                  INNER JOIN MENU_ITEMS AS m ON oi.ITEM_ID = m.ITEM_ID
                  WHERE oi.ORDER_ID IN (
                      SELECT ORDER_ID FROM ORDERS 
                      WHERE ORDER_DATE >= DATEADD(DAY, -7, GETDATE())
                  )
                  GROUP BY m.ITEM_NAME
                  ORDER BY TOTAL_SOLD DESC";
$result_top_items = sqlsrv_query($conn, $sql_top_items);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Orders - Lunalight Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .container-main {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            max-width: 1400px;
            margin: 0 auto;
        }
        .page-title {
            color: #1a1a2e;
            font-weight: bold;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #0f3460;
        }
        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .stat-box {
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            flex: 1;
            min-width: 200px;
            text-align: center;
        }
        .stat-box.weekly {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        }
        .stat-box.alltime {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
        }
        .filter-btn {
            background: #e0e0e0;
            color: #1a1a2e;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            margin-right: 10px;
        }
        .filter-btn.active {
            background: #0f3460;
            color: white;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .top-items-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title mb-0">📊 Orders & Reports</h1>
            <form action="dashboard_admin.php" method="post" style="display: inline;">
                <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                <button type="submit" class="btn-back">← Back</button>
            </form>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-number"><?php echo $today_data['TODAY_ORDERS']; ?></div>
                <div class="stat-label">Today's Orders</div>
                <div class="stat-number" style="font-size: 1.5rem; margin-top: 10px;">
                    ₱<?php echo number_format($today_data['TODAY_REVENUE'], 2); ?>
                </div>
                <div class="stat-label">Today's Revenue</div>
            </div>
            
            <div class="stat-box weekly">
                <div class="stat-number"><?php echo $week_data['WEEK_ORDERS']; ?></div>
                <div class="stat-label">This Week's Orders</div>
                <div class="stat-number" style="font-size: 1.5rem; margin-top: 10px;">
                    ₱<?php echo number_format($week_data['WEEK_REVENUE'], 2); ?>
                </div>
                <div class="stat-label">This Week's Revenue</div>
            </div>
            
            <div class="stat-box alltime">
                <div class="stat-number"><?php echo $all_data['ALL_ORDERS']; ?></div>
                <div class="stat-label">All Time Orders</div>
                <div class="stat-number" style="font-size: 1.5rem; margin-top: 10px;">
                    ₱<?php echo number_format($all_data['ALL_REVENUE'], 2); ?>
                </div>
                <div class="stat-label">All Time Revenue</div>
            </div>
        </div>

        <div class="mb-3">
            <form method="post" action="view_orders.php" style="display: inline;">
                <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                <input type="hidden" name="filter" value="all">
                <button type="submit" class="filter-btn <?php if($filter == 'all') echo 'active'; ?>">All Orders</button>
            </form>
            <form method="post" action="view_orders.php" style="display: inline;">
                <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                <input type="hidden" name="filter" value="today">
                <button type="submit" class="filter-btn <?php if($filter == 'today') echo 'active'; ?>">Today</button>
            </form>
            <form method="post" action="view_orders.php" style="display: inline;">
                <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                <input type="hidden" name="filter" value="week">
                <button type="submit" class="filter-btn <?php if($filter == 'week') echo 'active'; ?>">This Week</button>
            </form>
        </div>

        <h4>
            <?php 
            if($filter == 'today') echo "Today's Orders";
            else if($filter == 'week') echo "This Week's Orders";
            else echo "All Orders";
            ?>
        </h4>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Order #</th>
                    <th>Seller</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while($row = sqlsrv_fetch_array($result_orders)) {
                    $order_number = $row['ORDER_NUMBER'];
                    $cashier = $row['FULL_NAME'];
                    $order_date = $row['ORDER_DATE'];
                    $order_time = $row['ORDER_TIME'];
                    $total = $row['TOTAL_AMOUNT'];
                    $payment = $row['PAYMENT_METHOD'];
                    $status = $row['STATUS'];
                    
                    $date_display = 'N/A';
                    if($order_date) {
                        $date_display = $order_date->format('Y-m-d');
                    }
                    
                    $time_display = 'N/A';
                    if($order_time) {
                        $time_display = $order_time->format('H:i:s');
                    }
                    
                    $status_badge = 'bg-success';
                    if($status != 'COMPLETED') {
                        $status_badge = 'bg-warning';
                    }
                    
                    echo "<tr>
                        <td>$order_number</td>
                        <td>$cashier</td>
                        <td>$date_display</td>
                        <td>$time_display</td>
                        <td>₱" . number_format($total, 2) . "</td>
                        <td>$payment</td>
                        <td><span class='badge $status_badge'>$status</span></td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="top-items-section">
            <h4>🔥 Top Selling Items This Week</h4>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity Sold</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    while($item = sqlsrv_fetch_array($result_top_items)) {
                        $count = $count + 1;
                        if($count > 5) {
                            break;
                        }
                        
                        echo "<tr>
                            <td>" . $item['ITEM_NAME'] . "</td>
                            <td>" . $item['TOTAL_SOLD'] . "</td>
                            <td>₱" . number_format($item['TOTAL_SALES'], 2) . "</td>
                        </tr>";
                    }
                    
                    if($count == 0) {
                        echo "<tr><td colspan='3' class='text-center'>No sales data this week</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>