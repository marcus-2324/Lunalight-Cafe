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

$sql_count = "SELECT COUNT(*) AS ITEM_COUNT FROM MENU_ITEMS WHERE CATEGORY_ID = $CATEGORY_ID";
$result_count = sqlsrv_query($conn, $sql_count);
$row_count = sqlsrv_fetch_array($result_count);
$item_count = $row_count['ITEM_COUNT'];

$sql_category = "UPDATE CATEGORIES SET STATUS = 'ACTIVE' WHERE CATEGORY_ID = $CATEGORY_ID";
$result_category = sqlsrv_query($conn, $sql_category);

$sql_items = "UPDATE MENU_ITEMS SET AVAILABILITY = 'AVAILABLE' WHERE CATEGORY_ID = $CATEGORY_ID";
$result_items = sqlsrv_query($conn, $sql_items);

if($result_category) {
    echo "<script>alert('Category activated! $item_count menu items in this category were also marked as AVAILABLE.');</script>";
    echo '<form id="redirectForm" action="manage_categories.php" method="post">
            <input type="hidden" name="USER_ID" value="'.$USER_ID.'">
          </form>
          <script>document.getElementById("redirectForm").submit();</script>';
}
else {
    echo 'Error activating category';
    die(print_r(sqlsrv_errors(), true));
}
?>