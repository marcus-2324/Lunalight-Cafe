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
$DELETE_ID = $_POST['DELETE_ID'];

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

$sql_self_check = "SELECT * FROM USERS WHERE USER_ID = $DELETE_ID AND USER_ID = $USER_ID";
$result_self = sqlsrv_query($conn, $sql_self_check);
$is_self = sqlsrv_fetch_array($result_self);

if($is_self) {
    echo "<script>alert('You cannot delete your own account!');</script>";
    echo '<form id="redirectForm" action="manage_users.php" method="post">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
    exit();
}

$sql_check_orders = "SELECT COUNT(*) AS ORDER_COUNT FROM ORDERS WHERE USER_ID = $DELETE_ID";
$result_check_orders = sqlsrv_query($conn, $sql_check_orders);
$row_check = sqlsrv_fetch_array($result_check_orders);
$order_count = $row_check['ORDER_COUNT'];

if($order_count > 0) {
    $sql = "UPDATE USERS SET STATUS = 'INACTIVE' WHERE USER_ID = $DELETE_ID";
    $result = sqlsrv_query($conn, $sql);
    
    if($result) {
        echo "<script>alert('User has $order_count orders and was DEACTIVATED instead of deleted.');</script>";
        echo '<form id="redirectForm" action="manage_users.php" method="post">
                <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
              </form>
              <script>document.getElementById("redirectForm").submit();</script>';
    }
    else {
        echo 'Error deactivating user';
        die(print_r(sqlsrv_errors(), true));
    }
}
else {
    $sql = "DELETE FROM USERS WHERE USER_ID = $DELETE_ID";
    $result = sqlsrv_query($conn, $sql);

    if($result) {
        echo "<script>alert('User deleted successfully!');</script>";
        echo '<form id="redirectForm" action="manage_users.php" method="post">
                <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
              </form>
              <script>document.getElementById("redirectForm").submit();</script>';
    }
    else {
        echo 'Error deleting user';
        die(print_r(sqlsrv_errors(), true));
    }
}
?>