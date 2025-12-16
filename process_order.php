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

$sql_check = "SELECT * FROM USERS WHERE USER_ID = $USER_ID";
$result_check = sqlsrv_query($conn, $sql_check);
$user_data = sqlsrv_fetch_array($result_check);

if($user_data == false) {
    header("Location: Lunalight.html");
    exit();
}

$TOTAL_AMOUNT = $_POST['TOTAL_AMOUNT'];
$PAYMENT_METHOD = $_POST['PAYMENT_METHOD'];
$AMOUNT_PAID = $_POST['AMOUNT_PAID'];

if($AMOUNT_PAID < $TOTAL_AMOUNT) {
    echo "<script>alert('Insufficient payment!'); window.history.back();</script>";
    exit();
}

$CHANGE_AMOUNT = $AMOUNT_PAID - $TOTAL_AMOUNT;
$ORDER_NUMBER = 'ORD-' . date('Ymd') . '-' . time();
$ORDER_DATE = date('Y-m-d');
$ORDER_TIME = date('H:i:s');

$sql_order = "INSERT INTO ORDERS (ORDER_NUMBER, USER_ID, ORDER_DATE, ORDER_TIME, TOTAL_AMOUNT, PAYMENT_METHOD, AMOUNT_PAID, CHANGE_AMOUNT, STATUS) 
              VALUES ('$ORDER_NUMBER', $USER_ID, '$ORDER_DATE', '$ORDER_TIME', $TOTAL_AMOUNT, '$PAYMENT_METHOD', $AMOUNT_PAID, $CHANGE_AMOUNT, 'COMPLETED')";
$result_order = sqlsrv_query($conn, $sql_order);

if($result_order == false) {
    echo 'Error creating order';
    die(print_r(sqlsrv_errors(), true));
}
else {
    $sql_get_id = "SELECT ORDER_ID FROM ORDERS WHERE ORDER_NUMBER = '$ORDER_NUMBER'";
    $result_get_id = sqlsrv_query($conn, $sql_get_id);
    $row_id = sqlsrv_fetch_array($result_get_id);
    $ORDER_ID = $row_id['ORDER_ID'];
    
    $sql_cart = "SELECT ITEM_ID, QUANTITY, UNIT_PRICE, SUBTOTAL FROM CART WHERE USER_ID = $USER_ID";
    $result_cart = sqlsrv_query($conn, $sql_cart);
    
    while($cart_item = sqlsrv_fetch_array($result_cart)) {
        $ITEM_ID = $cart_item['ITEM_ID'];
        $QUANTITY = $cart_item['QUANTITY'];
        $UNIT_PRICE = $cart_item['UNIT_PRICE'];
        $SUBTOTAL = $cart_item['SUBTOTAL'];
        
        $sql_item = "INSERT INTO ORDER_ITEMS (ORDER_ID, ITEM_ID, QUANTITY, UNIT_PRICE, SUBTOTAL) 
                     VALUES ($ORDER_ID, $ITEM_ID, $QUANTITY, $UNIT_PRICE, $SUBTOTAL)";
        $result_item = sqlsrv_query($conn, $sql_item);
        
        if($result_item == false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }
    
    $sql_clear = "DELETE FROM CART WHERE USER_ID = $USER_ID";
    sqlsrv_query($conn, $sql_clear);
    
    $PDF_FILENAME = 'RECEIPT_' . $ORDER_NUMBER . '.html';
    $PDF_FILEPATH = 'receipts/' . $PDF_FILENAME;
    $DATE_GENERATED = date('Y-m-d H:i:s');
    
    $sql_receipt = "INSERT INTO RECEIPT (ORDER_ID, ORDER_NUMBER, PDF_FILENAME, PDF_FILEPATH, DATE_GENERATED, FILE_SIZE_KB) 
                    VALUES ($ORDER_ID, '$ORDER_NUMBER', '$PDF_FILENAME', '$PDF_FILEPATH', '$DATE_GENERATED', 0)";
    $result_receipt = sqlsrv_query($conn, $sql_receipt);
    
    if($result_receipt == false) {
        die(print_r(sqlsrv_errors(), true));
    }
    else {
        echo '<form id="redirectForm" action="receipt.php" method="post">
                <input type="hidden" name="ORDER_NUMBER" value="'.$ORDER_NUMBER.'">
                <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
              </form>
              <script>document.getElementById("redirectForm").submit();</script>';
        exit();
    }
}
?>