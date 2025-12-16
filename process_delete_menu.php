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
$ITEM_ID = $_POST['ITEM_ID'];

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

$sql_check_orders = "SELECT COUNT(*) AS ORDER_COUNT FROM ORDER_ITEMS WHERE ITEM_ID = $ITEM_ID";
$result_check_orders = sqlsrv_query($conn, $sql_check_orders);
$row_check = sqlsrv_fetch_array($result_check_orders);
$order_count = $row_check['ORDER_COUNT'];

if($order_count > 0) {
    $sql = "UPDATE MENU_ITEMS SET AVAILABILITY = 'UNAVAILABLE' WHERE ITEM_ID = $ITEM_ID";
    $result = sqlsrv_query($conn, $sql);
    
    if($result) {
        echo "<script>alert('Menu item appears in $order_count orders and was marked as UNAVAILABLE instead of deleted.');</script>";
        echo '<form id="redirectForm" action="manage_menu.php" method="post">
                <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
              </form>
              <script>document.getElementById("redirectForm").submit();</script>';
    }
    else {
        echo 'Error updating menu item';
        die(print_r(sqlsrv_errors(), true));
    }
}
else {
    $sql_delete_images = "DELETE FROM IMAGES WHERE ITEM_ID = $ITEM_ID";
    sqlsrv_query($conn, $sql_delete_images);

    $sql = "DELETE FROM MENU_ITEMS WHERE ITEM_ID = $ITEM_ID";
    $result = sqlsrv_query($conn, $sql);

    if($result) {
        echo "<script>alert('Menu item deleted successfully!');</script>";
        echo '<form id="redirectForm" action="manage_menu.php" method="post">
                <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
              </form>
              <script>document.getElementById("redirectForm").submit();</script>';
    }
    else {
        echo 'Error deleting menu item';
        die(print_r(sqlsrv_errors(), true));
    }
}
?>