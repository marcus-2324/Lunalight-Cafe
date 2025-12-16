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
$CATEGORY_ID = $_POST['CATEGORY_ID'];

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

$sql_check_items = "SELECT COUNT(*) AS ITEM_COUNT FROM MENU_ITEMS WHERE CATEGORY_ID = $CATEGORY_ID";
$result_check_items = sqlsrv_query($conn, $sql_check_items);
$row_check = sqlsrv_fetch_array($result_check_items);
$item_count = $row_check['ITEM_COUNT'];

if($item_count > 0) {
    $sql_category = "UPDATE CATEGORIES SET STATUS = 'INACTIVE' WHERE CATEGORY_ID = $CATEGORY_ID";
    sqlsrv_query($conn, $sql_category);
    
    $sql_items = "UPDATE MENU_ITEMS SET AVAILABILITY = 'UNAVAILABLE' WHERE CATEGORY_ID = $CATEGORY_ID";
    sqlsrv_query($conn, $sql_items);
    
    echo "<script>alert('Category has $item_count menu items and was DEACTIVATED (along with all items) instead of deleted.');</script>";
    echo '<form id="redirectForm" action="manage_categories.php" method="post">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
    exit();
}

$sql = "DELETE FROM CATEGORIES WHERE CATEGORY_ID = $CATEGORY_ID";
$result = sqlsrv_query($conn, $sql);

if($result) {
    echo "<script>alert('Category deleted successfully!');</script>";
    echo '<form id="redirectForm" action="manage_categories.php" method="post">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
}
else {
    echo 'Error deleting category';
    die(print_r(sqlsrv_errors(), true));
}
?>