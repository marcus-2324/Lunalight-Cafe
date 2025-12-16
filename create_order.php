<?php
$serverName = "LAPTOP-QNA7LUF8\SQLEXPRESS";
$connectionOptions = [
    "Database" => "LUNALIGHT",
    "Uid" => "",
    "PWD" => ""
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}

$USER_ID = $_POST['USER_ID'];

if($USER_ID == '') {
    header("Location: admin_login.html");
    exit();
}

$sql_check = "SELECT * FROM USERS WHERE USER_ID = $USER_ID";
$result_check = sqlsrv_query($conn, $sql_check);
$user_data = sqlsrv_fetch_array($result_check);

if($user_data == false) {
    header("Location: admin_login.html");
    exit();
}

$action = $_POST['action'];
if($action == '') {
    $action = 'view';
}

if($action == 'remove') {
    $CART_ID = $_POST['cart_id'];
    $sql_delete = "DELETE FROM CART WHERE CART_ID = $CART_ID AND USER_ID = $USER_ID";
    sqlsrv_query($conn, $sql_delete);
    
    echo '<form id="redirectForm" action="create_order.php" method="post">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
            <input type="hidden" name="action" value="view">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
    exit();
}

if($action == 'clear') {
    $sql_clear = "DELETE FROM CART WHERE USER_ID = $USER_ID";
    sqlsrv_query($conn, $sql_clear);
    
    echo '<form id="redirectForm" action="create_order.php" method="post">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
            <input type="hidden" name="action" value="view">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
    exit();
}

if($action == 'add') {
    $ITEM_ID = $_POST['item_id'];
    $QUANTITY = $_POST['quantity'];
    
    if($QUANTITY > 0) {
        $sql_price = "SELECT PRICE FROM MENU_ITEMS WHERE ITEM_ID = $ITEM_ID";
        $result_price = sqlsrv_query($conn, $sql_price);
        $row_price = sqlsrv_fetch_array($result_price);
        $UNIT_PRICE = $row_price['PRICE'];
        $SUBTOTAL = $UNIT_PRICE * $QUANTITY;
        
        $sql_check_cart = "SELECT * FROM CART WHERE USER_ID = $USER_ID AND ITEM_ID = $ITEM_ID";
        $result_check_cart = sqlsrv_query($conn, $sql_check_cart);
        $existing_item = sqlsrv_fetch_array($result_check_cart);
        
        if($existing_item == false) {
            $sql_insert = "INSERT INTO CART (USER_ID, ITEM_ID, QUANTITY, UNIT_PRICE, SUBTOTAL) 
                           VALUES ($USER_ID, $ITEM_ID, $QUANTITY, $UNIT_PRICE, $SUBTOTAL)";
            sqlsrv_query($conn, $sql_insert);
        }
        else {
            $NEW_QUANTITY = $existing_item['QUANTITY'] + $QUANTITY;
            $NEW_SUBTOTAL = $UNIT_PRICE * $NEW_QUANTITY;
            
            $sql_update = "UPDATE CART 
                           SET QUANTITY = $NEW_QUANTITY, SUBTOTAL = $NEW_SUBTOTAL 
                           WHERE CART_ID = " . $existing_item['CART_ID'];
            sqlsrv_query($conn, $sql_update);
        }
    }
    
    echo '<form id="redirectForm" action="create_order.php" method="post">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
            <input type="hidden" name="action" value="view">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
    exit();
}

$sql_cart = "SELECT c.CART_ID, c.QUANTITY, c.UNIT_PRICE, c.SUBTOTAL, m.ITEM_NAME
             FROM CART AS c
             INNER JOIN MENU_ITEMS AS m ON c.ITEM_ID = m.ITEM_ID
             WHERE c.USER_ID = $USER_ID";
$result_cart = sqlsrv_query($conn, $sql_cart);

$sql_total = "SELECT SUM(SUBTOTAL) AS TOTAL FROM CART WHERE USER_ID = $USER_ID";
$result_total = sqlsrv_query($conn, $sql_total);
$row_total = sqlsrv_fetch_array($result_total);
$TOTAL_AMOUNT = $row_total['TOTAL'];
if($TOTAL_AMOUNT == '') {
    $TOTAL_AMOUNT = 0;
}

$sql_categories = "SELECT * FROM CATEGORIES WHERE STATUS = 'ACTIVE' ORDER BY CATEGORY_NAME";
$result_categories = sqlsrv_query($conn, $sql_categories);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Order - Lunalight Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
        }
        .menu-image {
            width: 100%;
            height: 180px;
            object-fit: contain;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .no-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        .order-summary {
            position: sticky;
            top: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4">
                <h1 class="text-center fw-bold mb-4">🛒 Create New Order</h1>

                <div class="row">
                    <div class="col-lg-8">
                        <?php
                        while($category = sqlsrv_fetch_array($result_categories)) {
                            $cat_id = $category['CATEGORY_ID'];
                            $cat_name = $category['CATEGORY_NAME'];
                            
                            $sql_items = "SELECT m.ITEM_ID, m.ITEM_NAME, m.PRICE, i.FILEPATH
                                          FROM MENU_ITEMS AS m 
                                          LEFT OUTER JOIN IMAGES AS i ON m.ITEM_ID = i.ITEM_ID 
                                          WHERE m.CATEGORY_ID = $cat_id AND m.AVAILABILITY = 'AVAILABLE'
                                          ORDER BY m.ITEM_NAME";
                            $result_items = sqlsrv_query($conn, $sql_items);
                            
                            echo "<div class='mb-4'>
                                    <h4 class='fw-bold mb-3 p-3 bg-light rounded-3'>$cat_name</h4>
                                    <div class='row g-3'>";
                            
                            while($item = sqlsrv_fetch_array($result_items)) {
                                $item_id = $item['ITEM_ID'];
                                $item_name = $item['ITEM_NAME'];
                                $price = $item['PRICE'];
                                $filepath = $item['FILEPATH'];
                                
                                echo "<div class='col-md-4'>
                                        <div class='card border-2 h-100 shadow-sm'>
                                            <div class='card-body p-3'>
                                                <form method='post' action='create_order.php'>
                                                    <input type='hidden' name='action' value='add'>
                                                    <input type='hidden' name='item_id' value='$item_id'>
                                                    <input type='hidden' name='USER_ID' value='$USER_ID'>";
                                
                                if($filepath) {
                                    echo "<img src='$filepath' class='menu-image mb-3' alt='$item_name'>";
                                }
                                else {
                                    echo "<div class='no-image mb-3'>🍽️</div>";
                                }
                                
                                echo "<h6 class='fw-bold mb-2'>$item_name</h6>
                                      <p class='text-success fw-bold fs-5 mb-3'>₱" . number_format($price, 2) . "</p>
                                      <div class='mb-3'>
                                          <label class='form-label small fw-medium'>Qty:</label>
                                          <input type='number' name='quantity' class='form-control rounded-3' value='1' min='1' max='99' required>
                                      </div>
                                      <button type='submit' class='btn btn-success w-100 rounded-3 fw-semibold'>Add to Order</button>
                                    </form>
                                  </div>
                                </div>
                            </div>";
                            }
                            
                            echo "</div></div>";
                        }
                        ?>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-success border-3 shadow-sm rounded-4 order-summary">
                            <div class="card-body p-4">
                                <h4 class="fw-bold mb-4 pb-3 border-bottom border-2">Order Summary</h4>
                                
                                <?php
                                $has_items = false;
                                while($cart_item = sqlsrv_fetch_array($result_cart)) {
                                    $has_items = true;
                                    echo '<div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                                        <div class="flex-grow-1">
                                            <strong class="d-block mb-1">' . $cart_item['ITEM_NAME'] . '</strong>
                                            <small class="text-muted">' . $cart_item['QUANTITY'] . ' x ₱' . number_format($cart_item['UNIT_PRICE'], 2) . '</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success mb-2">₱' . number_format($cart_item['SUBTOTAL'], 2) . '</div>
                                            <form method="post" class="d-inline" onsubmit="return confirm(\'Remove this item?\')">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="cart_id" value="' . $cart_item['CART_ID'] . '">
                                                <input type="hidden" name="USER_ID" value="' . $USER_ID . '">
                                                <button type="submit" class="btn btn-danger btn-sm rounded-2">Remove</button>
                                            </form>
                                        </div>
                                    </div>';
                                }
                                
                                if($has_items == false) {
                                    echo '<p class="text-center text-muted py-4">No items in cart</p>';
                                }
                                ?>
                                
                                <div class="d-flex justify-content-between align-items-center py-3 border-top border-success border-2">
                                    <span class="fs-4 fw-bold">TOTAL:</span>
                                    <span class="fs-3 fw-bold text-success">₱<?php echo number_format($TOTAL_AMOUNT, 2); ?></span>
                                </div>
                                
                                <?php
                                if($has_items) {
                                    echo '<form action="process_order.php" method="post" class="mt-4">
                                        <input type="hidden" name="USER_ID" value="' . $USER_ID . '">
                                        <input type="hidden" name="TOTAL_AMOUNT" value="' . $TOTAL_AMOUNT . '">
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Payment Method</label>
                                            <select name="PAYMENT_METHOD" class="form-select rounded-3" required>
                                                <option value="">Select Payment</option>
                                                <option value="CASH">Cash</option>
                                                <option value="CARD">Card</option>
                                                <option value="GCASH">GCash</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">Amount Paid (₱)</label>
                                            <input type="number" step="0.01" name="AMOUNT_PAID" class="form-control rounded-3" placeholder="0.00" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-3 fw-semibold mb-2">Complete Order</button>
                                    </form>
                                    
                                    <form method="post" onsubmit="return confirm(\'Clear entire cart?\')" class="mb-2">
                                        <input type="hidden" name="action" value="clear">
                                        <input type="hidden" name="USER_ID" value="' . $USER_ID . '">
                                        <button type="submit" class="btn btn-warning w-100 rounded-3 fw-semibold">Clear Cart</button>
                                    </form>';
                                }
                                ?>
                                
                                <form action="<?php 
                                    if($user_data['ROLE'] == 'ADMIN') {
                                        echo 'dashboard_admin.php';
                                    }
                                    else {
                                        echo 'dashboard_cashier.php';
                                    }
                                ?>" method="post">
                                    <input type="hidden" name="USER_ID" value="<?php echo $USER_ID; ?>">
                                    <button type="submit" class="btn btn-secondary w-100 rounded-3">← Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>